<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "kids_club");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "資料庫連線失敗"]));
}

$action = $_POST['action'] ?? '';

// --- 功能 A：管理後台更新備註 ---
if ($action === 'update_remark') {
    $member_id = (int)($_POST['member_id'] ?? 0);
    $remark = $_POST['remark'] ?? '';
    $stmt = $conn->prepare("UPDATE members SET remark = ? WHERE id = ?");
    $stmt->bind_param("si", $remark, $member_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit;
}

// --- 功能 B：舊會員報到邏輯 (家長與幼兒邏輯完全對齊) ---
if ($action === 'final_checkin') {
    $member_id = (int)($_POST['member_id'] ?? 0);
    $selected_parents = $_POST['selected_parents'] ?? '';   
    $selected_children = $_POST['selected_children'] ?? ''; 
    $new_parent = trim($_POST['new_parent'] ?? '');
    $new_child = trim($_POST['new_child'] ?? '');
    
    if ($member_id > 0) {
        // 1. 處理手寫新增【家長】：串接至 Master 名單，並補齊佔位資訊
        if (!empty($new_parent)) {
            $placeholder_gender = "未提供"; // 若有 parent_gender 欄位可比照補齊
            $stmt = $conn->prepare("UPDATE members SET 
                parent_name = CONCAT(parent_name, '|', ?)
                -- 如果資料庫有 parent_gender 等欄位，請在此處比照幼兒邏輯 CONCAT 補齊
                WHERE id = ?");
            $stmt->bind_param("si", $new_parent, $member_id);
            $stmt->execute();
            
            // 更新本次報到的名單字串
            $selected_parents = (empty($selected_parents) ? "" : $selected_parents . "|") . $new_parent;
        }

        // 2. 處理手寫新增【幼兒】：串接名單並補齊 5 項元數據與計數
        if (!empty($new_child)) {
            $placeholder = "新成員";
            $stmt = $conn->prepare("UPDATE members SET 
                child_name = CONCAT(child_name, '|', ?), 
                child_birthday = CONCAT(child_birthday, '|', '0000-00-00'),
                child_gender = CONCAT(child_gender, '|', '未提供'),
                child_exact_age = CONCAT(child_exact_age, '|', '$placeholder'),
                child_age_group = CONCAT(child_age_group, '|', '$placeholder'),
                child_count = child_count + 1 
                WHERE id = ?");
            $stmt->bind_param("si", $new_child, $member_id);
            $stmt->execute();
            
            // 更新本次報到的名單字串
            $selected_children = (empty($selected_children) ? "" : $selected_children . "|") . $new_child;
        }

        // 3. 寫入本次進館 Log (存入當次勾選/新增的所有人名)
        $stmt = $conn->prepare("INSERT INTO check_in_logs (member_id, checked_parents, checked_children) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $member_id, $selected_parents, $selected_children);
        
        if ($stmt->execute()) {
            // 抓取本次報到名單的第一位家長姓名作為歡迎語
            $welcome_name = explode('|', $selected_parents)[0];
            echo json_encode(["status" => "success", "user_name" => $welcome_name]);
        } else {
            echo json_encode(["status" => "error", "message" => "紀錄失敗：" . $conn->error]);
        }
    }
    exit;
}

// --- 功能 C：電話查詢 ---
$phone = $_POST['phone'] ?? '';
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
            "parent_name" => $user['parent_name'], // 回傳以 | 分隔的多家長字串
            "child_name" => $user['child_name']   // 回傳以 | 分隔的多幼兒字串
        ]
    ]);
    exit;
} else {
    // --- 功能 D：新會員註冊流程 ---
    $name = $_POST['name'] ?? '';
    if (empty($name)) {
        echo json_encode(["status" => "need_register"]);
    } else {
        // 接收註冊資料
        $district = $_POST['district'] ?? '';
        $purpose = $_POST['purpose'] ?? '';
        $relationship = $_POST['relationship'] ?? '';
        $adult_male = (int)($_POST['adult_male'] ?? 0);
        $adult_female = (int)($_POST['adult_female'] ?? 0);
        $child_count = (int)($_POST['child_count'] ?? 0);
        $languages = $_POST['languages'] ?? '';
        
        // 處理幼兒多位資料 (串接字串)
        $c_names = $_POST['child_name'] ?? '';
        $c_birthdays = $_POST['child_birthday'] ?? '';
        $c_genders = $_POST['child_gender'] ?? '';

        // 計算年齡邏輯 (維持您原本的 DateTime 計算方式)
        $birthday_array = explode('|', $c_birthdays);
        $exact_ages = []; $age_groups = [];
        foreach ($birthday_array as $bd) {
            if (empty($bd) || $bd == "0000-00-00") {
                $exact_ages[] = "未知"; $age_groups[] = "未知";
                continue;
            }
            $diff = (new DateTime($bd))->diff(new DateTime('today'));
            $total_m = ($diff->y * 12) + $diff->m;
            $exact_ages[] = "{$diff->y}歲{$diff->m}個月";
            if ($total_m < 6) $age_groups[] = "未滿6個月";
            elseif ($total_m < 12) $age_groups[] = "6個月~未滿1歲";
            else $age_groups[] = "{$diff->y}歲~未滿" . ($diff->y + 1) . "歲";
        }

        $sql = "INSERT INTO members (parent_name, phone, district, purpose, relationship, adult_male, adult_female, child_count, languages, child_name, child_birthday, child_gender, child_exact_age, child_age_group) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $insert_stmt = $conn->prepare($sql);
        $insert_stmt->bind_param("sssssiiissssss", 
            $name, $phone, $district, $purpose, $relationship, 
            $adult_male, $adult_female, $child_count, $languages, 
            $c_names, $c_birthdays, $c_genders, implode('|', $exact_ages), implode('|', $age_groups)
        );
        
        if ($insert_stmt->execute()) {
            $new_id = $conn->insert_id;
            // 同時寫入 Log (初始註冊的人即為本次報到人)
            $conn->query("INSERT INTO check_in_logs (member_id, checked_parents, checked_children) VALUES ($new_id, '$name', '$c_names')");
            echo json_encode(["status" => "success", "user_name" => explode('|', $name)[0]]);
        } else {
            echo json_encode(["status" => "error", "message" => "註冊失敗：" . $conn->error]);
        }
    }
}
$conn->close();
?>