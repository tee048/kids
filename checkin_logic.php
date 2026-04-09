<?php
// 開啟輸出緩衝，確保中途的 PHP 警告不會破壞最終的 JSON 格式
ob_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "kids_club");

if ($conn->connect_error) {
    ob_clean();
    die(json_encode(["status" => "error", "message" => "資料庫連線失敗"]));
}

// 強制捕捉資料庫層級錯誤
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$action = $_POST['action'] ?? '';

try {
    // --- 功能 A：管理後台更新備註 ---
    if ($action === 'update_remark') {
        $log_id = (int)($_POST['log_id'] ?? 0);
        $remark = $_POST['remark'] ?? '';
        
        // 修正：使用 id 作為判斷基準
        $stmt = $conn->prepare("UPDATE check_in_logs SET remark = ? WHERE id = ?");
        $stmt->bind_param("si", $remark, $log_id);
        $stmt->execute();

        ob_clean();
        echo json_encode(["status" => "success"]);
        exit;
    }

    // --- 功能 E：刪除名單 (家長或幼兒) ---
    if ($action === 'delete_person') {
        $member_id = (int)($_POST['member_id'] ?? 0);
        $type = $_POST['type'] ?? '';
        $target_name = trim($_POST['name'] ?? '');

        if ($member_id > 0 && $target_name !== '') {
            $stmt = $conn->prepare("SELECT parent_name, child_name, child_birthday, child_gender, child_exact_age, child_age_group FROM members WHERE id = ?");
            $stmt->bind_param("i", $member_id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();

            if ($type === 'parent') {
                $parents = explode('|', $row['parent_name'] ?? '');
                if (($key = array_search($target_name, $parents)) !== false) unset($parents[$key]);
                $new_parents = implode('|', array_filter($parents));
                $up_stmt = $conn->prepare("UPDATE members SET parent_name = ? WHERE id = ?");
                $up_stmt->bind_param("si", $new_parents, $member_id);
                $up_stmt->execute();
            } else if ($type === 'child') {
                $children = explode('|', $row['child_name'] ?? '');
                $birthdays = explode('|', $row['child_birthday'] ?? '');
                $genders = explode('|', $row['child_gender'] ?? '');
                $exact_ages = explode('|', $row['child_exact_age'] ?? '');
                $age_groups = explode('|', $row['child_age_group'] ?? '');

                if (($key = array_search($target_name, $children)) !== false) {
                    unset($children[$key]);
                    if (isset($birthdays[$key])) unset($birthdays[$key]);
                    if (isset($genders[$key])) unset($genders[$key]);
                    if (isset($exact_ages[$key])) unset($exact_ages[$key]);
                    if (isset($age_groups[$key])) unset($age_groups[$key]);
                }

                $up_stmt = $conn->prepare("UPDATE members SET child_name=?, child_birthday=?, child_gender=?, child_exact_age=?, child_age_group=? WHERE id = ?");
                $up_stmt->bind_param("sssssi", 
                    implode('|', array_filter($children)), 
                    implode('|', array_filter($birthdays)), 
                    implode('|', array_filter($genders)), 
                    implode('|', array_filter($exact_ages)), 
                    implode('|', array_filter($age_groups)), 
                    $member_id
                );
                $up_stmt->execute();
            }
            ob_clean();
            echo json_encode(["status" => "success"]);
        } else {
            ob_clean();
            echo json_encode(["status" => "error", "message" => "參數錯誤"]);
        }
        exit;
    }

    // --- 功能 B：舊會員報到邏輯 (已覆蓋為包含新幼兒性別/生日的完整版) ---
    if ($action === 'final_checkin') {
        $member_id = (int)($_POST['member_id'] ?? 0);
        $selected_parents = $_POST['selected_parents'] ?? '';
        $selected_children = $_POST['selected_children'] ?? '';
        $new_parent = trim($_POST['new_parent'] ?? '');
        $new_child = trim($_POST['new_child'] ?? '');
        
        $new_gender = $_POST['new_child_gender'] ?? '';
        $new_birthday = $_POST['new_child_birthday'] ?? '';

        if ($member_id <= 0) {
            ob_clean();
            echo json_encode(["status" => "error", "message" => "無效的會員 ID"]);
            exit;
        }

        $stmt = $conn->prepare("SELECT parent_name, child_name, child_birthday, child_gender, child_exact_age, child_age_group FROM members WHERE id = ?");
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        $old_data = $stmt->get_result()->fetch_assoc();

        // 處理家長追加
        if ($new_parent !== "") {
            $db_parents = explode('|', $old_data['parent_name'] ?? '');
            if (!in_array($new_parent, $db_parents)) {
                $db_parents[] = $new_parent;
                $new_full_parents = implode('|', array_filter($db_parents));
                $up_p = $conn->prepare("UPDATE members SET parent_name = ? WHERE id = ?");
                $up_p->bind_param("si", $new_full_parents, $member_id);
                $up_p->execute();
            }
        }

        // 處理幼兒追加與年齡計算
        if ($new_child !== "" && $new_birthday !== "") {
            $db_names = explode('|', $old_data['child_name'] ?? '');
            if (!in_array($new_child, $db_names)) {
                $diff = (new DateTime($new_birthday))->diff(new DateTime('today'));
                $total_m = ($diff->y * 12) + $diff->m;
                $e_age = "{$diff->y}歲{$diff->m}個月";
                
                if ($total_m < 6) $a_group = "未滿6個月";
                elseif ($total_m < 12) $a_group = "6個月~未滿1歲";
                else $a_group = "{$diff->y}歲~未滿" . ($diff->y + 1) . "歲";

                $new_names = implode('|', array_filter(array_merge($db_names, [$new_child])));
                $new_bdays = implode('|', array_filter(array_merge(explode('|', $old_data['child_birthday']), [$new_birthday])));
                $new_genders = implode('|', array_filter(array_merge(explode('|', $old_data['child_gender']), [$new_gender])));
                $new_e_ages = implode('|', array_filter(array_merge(explode('|', $old_data['child_exact_age']), [$e_age])));
                $new_groups = implode('|', array_filter(array_merge(explode('|', $old_data['child_age_group']), [$a_group])));

                $up_c = $conn->prepare("UPDATE members SET child_name=?, child_birthday=?, child_gender=?, child_exact_age=?, child_age_group=? WHERE id = ?");
                $up_c->bind_param("sssssi", $new_names, $new_bdays, $new_genders, $new_e_ages, $new_groups, $member_id);
                $up_c->execute();
            }
        }

        $log_parents = implode('|', array_filter(explode('|', $selected_parents . '|' . $new_parent)));
        $log_children = implode('|', array_filter(explode('|', $selected_children . '|' . $new_child)));

        $log_stmt = $conn->prepare("INSERT INTO check_in_logs (member_id, checked_parents, checked_children, check_in_time) VALUES (?, ?, ?, NOW())");
        $log_stmt->bind_param("iss", $member_id, $log_parents, $log_children);
        $log_stmt->execute();

        $display_name = $new_parent ?: ($selected_parents ? explode('|', $selected_parents)[0] : "家長");

        ob_clean();
        echo json_encode(["status" => "success", "user_name" => $display_name]);
        exit;
    }

    // --- 功能 C：電話查詢 ---
    $phone = $_POST['phone'] ?? '';
    if ($action === 'check_member' && !empty($phone)) {
        $stmt = $conn->prepare("SELECT id, parent_name, child_name FROM members WHERE phone = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        ob_clean();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode(["status" => "old_member_select", "data" => ["id" => $user['id'], "parent_name" => $user['parent_name'] ?? '', "child_name" => $user['child_name'] ?? '']]);
        } else {
            echo json_encode(["status" => "need_register"]);
        }
        exit;
    }

    // --- 功能 D：新會員註冊流程 ---
    if ($action === 'register') {
        $name = $_POST['name'] ?? '';
        if (!empty($name)) {
            $phone = $_POST['phone'] ?? '';
            $district = $_POST['district'] ?? '';
            $purpose = $_POST['purpose'] ?? '';
            $relationship = $_POST['relationship'] ?? '';
            $adult_male = (int)($_POST['adult_male'] ?? 0);
            $adult_female = (int)($_POST['adult_female'] ?? 0);
            $child_count = (int)($_POST['child_count'] ?? 0);
            $languages = $_POST['languages'] ?? '';
            $respondent_type = $_POST['respondent_type'] ?? '';

            $c_names = $_POST['child_name'] ?? '';
            $c_birthdays = $_POST['child_birthday'] ?? '';
            $c_genders = $_POST['child_gender'] ?? '';

            $birthday_array = explode('|', $c_birthdays);
            $exact_ages = [];
            $age_groups = [];
            foreach ($birthday_array as $bd) {
                if (empty($bd) || $bd == "0000-00-00") {
                    $exact_ages[] = "未知"; $age_groups[] = "未知"; continue;
                }
                $diff = (new DateTime($bd))->diff(new DateTime('today'));
                $total_m = ($diff->y * 12) + $diff->m;
                $exact_ages[] = "{$diff->y}歲{$diff->m}個月";
                if ($total_m < 6) $age_groups[] = "未滿6個月";
                elseif ($total_m < 12) $age_groups[] = "6個月~未滿1歲";
                else $age_groups[] = "{$diff->y}歲~未滿" . ($diff->y + 1) . "歲";
            }

            $sql = "INSERT INTO members (parent_name, phone, district, purpose, relationship, adult_male, adult_female, child_count, languages, respondent_type, child_name, child_birthday, child_gender, child_exact_age, child_age_group) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($sql);
            $insert_stmt->bind_param("sssssiiisssssss", $name, $phone, $district, $purpose, $relationship, $adult_male, $adult_female, $child_count, $languages, $respondent_type, $c_names, $c_birthdays, $c_genders, implode('|', $exact_ages), implode('|', $age_groups));
            $insert_stmt->execute();

            $new_id = $conn->insert_id;
            $conn->query("INSERT INTO check_in_logs (member_id, checked_parents, checked_children, check_in_time) VALUES ($new_id, '$name', '$c_names', NOW())");
            
            ob_clean();
            echo json_encode(["status" => "success", "user_name" => explode('|', $name)[0]]);
            exit;
        }
    }

} catch (Exception $e) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "系統異常：" . $e->getMessage()]);
    exit;
}
$conn->close();
