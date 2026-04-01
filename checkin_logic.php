<?php
header('Content-Type: application/json');

// 資料庫連線：請確保您的資料庫名稱為 kids_club
$conn = new mysqli("localhost", "root", "", "kids_club");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "資料庫連線失敗"]));
}

// 接收來自前端的資料
$phone = $_POST['phone'] ?? '';
$name = $_POST['name'] ?? ''; // 家長姓名

// 1. 檢查會員是否存在 (依手機號碼)
$stmt = $conn->prepare("SELECT id, parent_name FROM members WHERE phone = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // 【老朋友】：已註冊過，直接紀錄本次進館
    $user = $result->fetch_assoc();
    $member_id = $user['id'];
    $user_name = $user['parent_name'];
    
    // 寫入進館紀錄表格 check_in_logs
    $conn->query("INSERT INTO check_in_logs (member_id) VALUES ($member_id)");
    
    echo json_encode([
        "status" => "success", 
        "user_name" => $user_name, 
        "member_id" => $member_id
    ]);
} else {
    // 【新朋友或資訊不足】：如果只有電話沒有姓名，叫前端顯示問卷
    if (empty($name)) {
        echo json_encode(["status" => "need_register"]);
    } else {
        // 抓取所有動態問卷欄位 (包括多個幼兒組合後的字串)
        $district = $_POST['district'] ?? '';
        $purpose = $_POST['purpose'] ?? '';
        $relationship = $_POST['relationship'] ?? '';
        $adult_male = (int)($_POST['adult_male'] ?? 0);
        $adult_female = (int)($_POST['adult_female'] ?? 0);
        $child_count = (int)($_POST['child_count'] ?? 0);
        $respondent_type = $_POST['respondent_type'] ?? '';
        $languages = $_POST['languages'] ?? '';
        
        // 幼兒詳細資訊 (例如 "小明|小華")
        $child_name = $_POST['child_name'] ?? '';
        $child_birthday = $_POST['child_birthday'] ?? '';
        $child_gender = $_POST['child_gender'] ?? '';

        // 執行註冊：存入 members 資料表
        $sql = "INSERT INTO members (parent_name, phone, district, purpose, relationship, adult_male, adult_female, child_count, respondent_type, languages, child_name, child_birthday, child_gender) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $insert_stmt = $conn->prepare($sql);
        $insert_stmt->bind_param("sssssiiisssss", 
            $name, $phone, $district, $purpose, $relationship, 
            $adult_male, $adult_female, $child_count, 
            $respondent_type, $languages, 
            $child_name, $child_birthday, $child_gender
        );
        
        if ($insert_stmt->execute()) {
            $new_id = $conn->insert_id;
            
            // 註冊完後，同步寫入一筆「本日進館紀錄」
            $conn->query("INSERT INTO check_in_logs (member_id) VALUES ($new_id)");
            
            echo json_encode([
                "status" => "success", 
                "user_name" => $name, 
                "member_id" => $new_id
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "註冊失敗：" . $conn->error]);
        }
    }
}

$conn->close();
?>