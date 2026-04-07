<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "kids_club");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "資料庫連線失敗"]));
}

$action = $_POST['action'] ?? '';

// --- 功能 A：管理後台更新備註 ---
if ($action === 'update_remark') {
    $log_id = (int)($_POST['log_id'] ?? 0); // 這裡改收 log_id
    $remark = $_POST['remark'] ?? '';

    // 這裡改為更新 check_in_logs 資料表
    $stmt = $conn->prepare("UPDATE check_in_logs SET remark = ? WHERE id = ?");
    $stmt->bind_param("si", $remark, $log_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit;
}

// --- 功能 B：舊會員報到邏輯 (補齊缺失的邏輯) ---
if ($action === 'final_checkin') {
    $member_id = (int)($_POST['member_id'] ?? 0);
    $selected_parents = $_POST['selected_parents'] ?? '';
    $selected_children = $_POST['selected_children'] ?? '';
    $new_parent = trim($_POST['new_parent'] ?? '');
    $new_child = trim($_POST['new_child'] ?? '');

    if ($member_id <= 0) {
        echo json_encode(["status" => "error", "message" => "無效的會員 ID"]);
        exit;
    }

    // 1. 處理家長名單更新
    $stmt = $conn->prepare("SELECT parent_name, child_name FROM members WHERE id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $old_data = $stmt->get_result()->fetch_assoc();

    $db_parents = explode('|', $old_data['parent_name'] ?? '');
    $db_children = explode('|', $old_data['child_name'] ?? '');

    // 更新家長總名單
    if ($new_parent !== "" && !in_array($new_parent, $db_parents)) {
        $db_parents[] = $new_parent;
        $new_full_parents = implode('|', array_filter($db_parents));
        $up_p = $conn->prepare("UPDATE members SET parent_name = ? WHERE id = ?");
        $up_p->bind_param("si", $new_full_parents, $member_id);
        $up_p->execute();
    }

    // 更新幼兒總名單
    if ($new_child !== "" && !in_array($new_child, $db_children)) {
        $db_children[] = $new_child;
        $new_full_children = implode('|', array_filter($db_children));
        $up_c = $conn->prepare("UPDATE members SET child_name = ? WHERE id = ?");
        $up_c->bind_param("si", $new_full_children, $member_id);
        $up_c->execute();
    }

    // 2. 準備本次報到紀錄 (Log)
    $final_parents = array_filter(explode('|', $selected_parents));
    if ($new_parent !== "") $final_parents[] = $new_parent;
    $log_parents = implode('|', $final_parents);

    $final_children = array_filter(explode('|', $selected_children));
    if ($new_child !== "") $final_children[] = $new_child;
    $log_children = implode('|', $final_children);

    // 3. 寫入 Log 資料表
    $log_stmt = $conn->prepare("INSERT INTO check_in_logs (member_id, checked_parents, checked_children, check_in_time) VALUES (?, ?, ?, NOW())");
    $log_stmt->bind_param("iss", $member_id, $log_parents, $log_children);

    if ($log_stmt->execute()) {
        // 回傳成功給前端，顯示歡迎訊息
        $display_name = !empty($final_parents) ? $final_parents[0] : "親愛的";
        echo json_encode(["status" => "success", "user_name" => $display_name]);
    } else {
        echo json_encode(["status" => "error", "message" => "寫入報到紀錄失敗：" . $conn->error]);
    }
    exit;
}

// --- 功能 C：電話查詢 ---
$phone = $_POST['phone'] ?? '';
if (!empty($phone) && $action !== 'final_checkin' && $action !== 'update_remark') {
    $stmt = $conn->prepare("SELECT id, parent_name, child_name FROM members WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode([
            "status" => "old_member_select",
            "data" => [
                "id" => $user['id'],
                "parent_name" => $user['parent_name'] ?? '',
                "child_name" => $user['child_name'] ?? ''
            ]
        ]);
        exit;
    } else {
        echo json_encode(["status" => "need_register"]);
        exit;
    }
}

// --- 功能 D：新會員註冊流程 (當 action 為空且有姓名時) ---
$name = $_POST['name'] ?? '';
if (!empty($name)) {
    // ... (這部分維持你原本的註冊代碼，但記得最後要回傳正確 JSON)
    $phone = $_POST['phone'] ?? '';
    $district = $_POST['district'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $relationship = $_POST['relationship'] ?? '';
    $adult_male = (int)($_POST['adult_male'] ?? 0);
    $adult_female = (int)($_POST['adult_female'] ?? 0);
    $child_count = (int)($_POST['child_count'] ?? 0);
    $languages = $_POST['languages'] ?? '';
    $c_names = $_POST['child_name'] ?? '';
    $c_birthdays = $_POST['child_birthday'] ?? '';
    $c_genders = $_POST['child_gender'] ?? '';

    // 計算年齡邏輯
    $birthday_array = explode('|', $c_birthdays);
    $exact_ages = [];
    $age_groups = [];
    foreach ($birthday_array as $bd) {
        if (empty($bd) || $bd == "0000-00-00") {
            $exact_ages[] = "未知";
            $age_groups[] = "未知";
            continue;
        }
        $diff = (new DateTime($bd))->diff(new DateTime('today'));
        $total_m = ($diff->y * 12) + $diff->m;
        $exact_ages[] = "{$diff->y}歲{$diff->m}個月";
        if ($total_m < 6) $age_groups[] = "未滿6個月";
        elseif ($total_m < 12) $age_groups[] = "6個月~未滿1歲";
        else $age_groups[] = "{$diff->y}歲~未滿" . ($diff->y + 1) . "歲";
    }

    $sql = "INSERT INTO members (parent_name, phone, district, purpose, relationship, adult_male, adult_female, child_count, languages, child_name, child_birthday, child_gender, child_exact_age, child_age_group) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($sql);
    $insert_stmt->bind_param("sssssiiissssss", $name, $phone, $district, $purpose, $relationship, $adult_male, $adult_female, $child_count, $languages, $c_names, $c_birthdays, $c_genders, implode('|', $exact_ages), implode('|', $age_groups));

    if ($insert_stmt->execute()) {
        $new_id = $conn->insert_id;
        $conn->query("INSERT INTO check_in_logs (member_id, checked_parents, checked_children, check_in_time) VALUES ($new_id, '$name', '$c_names', NOW())");
        echo json_encode(["status" => "success", "user_name" => explode('|', $name)[0]]);
    } else {
        echo json_encode(["status" => "error", "message" => "註冊失敗：" . $conn->error]);
    }
}

$conn->close();
