<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "kids_club");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "資料庫連線失敗"]));
}

$phone = $_POST['phone'] ?? '';
$name = $_POST['name'] ?? ''; 

// 1. 檢查會員是否存在
$stmt = $conn->prepare("SELECT id, parent_name FROM members WHERE phone = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // 【舊會員】直接紀錄進館
    $user = $result->fetch_assoc();
    $member_id = $user['id'];
    $conn->query("INSERT INTO check_in_logs (member_id) VALUES ($member_id)");
    echo json_encode(["status" => "success", "user_name" => $user['parent_name'], "member_id" => $member_id]);
} else {
    // 【新會員】
    if (empty($name)) {
        echo json_encode(["status" => "need_register"]);
    } else {
        // 接收基本資料
        $district = $_POST['district'] ?? '';
        $purpose = $_POST['purpose'] ?? '';
        $relationship = $_POST['relationship'] ?? '';
        $adult_male = (int)($_POST['adult_male'] ?? 0);
        $adult_female = (int)($_POST['adult_female'] ?? 0);
        $child_count = (int)($_POST['child_count'] ?? 0);
        $respondent_type = $_POST['respondent_type'] ?? '';
        $languages = $_POST['languages'] ?? '';
        $child_name = $_POST['child_name'] ?? '';
        $child_gender = $_POST['child_gender'] ?? '';
        
        // --- 核心邏輯：處理幼兒生日與年齡計算 ---
        $raw_birthdays = $_POST['child_birthday'] ?? '';
        $birthday_array = explode('|', $raw_birthdays);
        
        $exact_ages = []; // 存放 "1歲5個月"
        $age_groups = []; // 存放 "未滿6個月" 等精細區間

        foreach ($birthday_array as $bd) {
            if (empty($bd)) continue;
            $birthDate = new DateTime($bd);
            $today = new DateTime('today');
            $diff = $birthDate->diff($today);
            
            $y = $diff->y; 
            $m = $diff->m; 
            $total_months = ($y * 12) + $m;

            // A. 計算歲數 (EX: 1歲5個月)
            $exact_ages[] = "{$y}歲{$m}個月";
            
            // B. 判定精細年齡區間 (依照您提供的範本標準)
            if ($total_months < 6) {
                $age_groups[] = "未滿6個月";
            } elseif ($total_months < 12) {
                $age_groups[] = "6個月~未滿1歲";
            } elseif ($y < 2) {
                $age_groups[] = "1歲~未滿2歲";
            } elseif ($y < 3) {
                $age_groups[] = "2歲~未滿3歲";
            } elseif ($y < 4) {
                $age_groups[] = "3歲~未滿4歲";
            } elseif ($y < 5) {
                $age_groups[] = "4歲~未滿5歲";
            } elseif ($y < 6) {
                $age_groups[] = "5歲~未滿6歲";
            } else {
                $age_groups[] = "6歲以上 (未入小學)";
            }
        }

        // 將陣列轉回 | 串接的字串以便存入資料庫
        $child_exact_age_text = implode('|', $exact_ages);
        $child_age_group_text = implode('|', $age_groups);

        // 2. 執行註冊 (欄位順序必須與 bind_param 嚴格對應)
        $sql = "INSERT INTO members (parent_name, phone, district, purpose, relationship, adult_male, adult_female, child_count, respondent_type, languages, child_name, child_birthday, child_gender, child_exact_age, child_age_group) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $insert_stmt = $conn->prepare($sql);
        // 總共 15 個參數 (sssssiiisssssss)
        $insert_stmt->bind_param("sssssiiisssssss", 
            $name, $phone, $district, $purpose, $relationship, 
            $adult_male, $adult_female, $child_count, 
            $respondent_type, $languages, 
            $child_name, $raw_birthdays, $child_gender, $child_exact_age_text, $child_age_group_text
        );
        
        if ($insert_stmt->execute()) {
            $new_id = $conn->insert_id;
<<<<<<< HEAD
            // 紀錄進館
            $conn->query("INSERT INTO check_in_logs (member_id) VALUES ($new_id)");
            echo json_encode(["status" => "success", "user_name" => $name, "member_id" => $new_id]);
=======
            
            // 註冊完後，同步寫入一筆「本日進館紀錄」
            $conn->query("INSERT INTO check_in_logs (member_id) VALUES ($new_id)");
            
            echo json_encode([
                "status" => "success", 
                "user_name" => $name, 
                "member_id" => $new_id
            ]);
>>>>>>> f307ccadecc1d6acc87192079c553ca5920d3e9f
        } else {
            echo json_encode(["status" => "error", "message" => "註冊失敗：" . $conn->error]);
        }
    }
}
<<<<<<< HEAD
=======

>>>>>>> f307ccadecc1d6acc87192079c553ca5920d3e9f
$conn->close();
?>