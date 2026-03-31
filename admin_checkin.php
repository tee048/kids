<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "kids_club");

$phone = $_POST['phone'] ?? '';
$name = $_POST['name'] ?? '';

// 1. 檢查這個電話是否已經註冊過
$user_query = $conn->query("SELECT * FROM members WHERE phone = '$phone'");

if ($user_query->num_rows > 0) {
    // 【老朋友】
    $user = $user_query->fetch_assoc();
    $member_id = $user['id'];
    $user_name = $user['parent_name'];
    
    // 直接寫入報到紀錄
    $conn->query("INSERT INTO check_in_logs (member_id) VALUES ($member_id)");
    
    echo json_encode(["status" => "success", "user_name" => $user_name]);
} else {
    // 【可能是新朋友】
    if (empty($name)) {
        // 如果還沒填姓名，通知前端顯示姓名欄位
        echo json_encode(["status" => "need_register"]);
    } else {
        // 已經填了姓名，進行註冊 + 報到
        $conn->query("INSERT INTO members (parent_name, phone) VALUES ('$name', '$phone')");
        $new_id = $conn->insert_id;
        $conn->query("INSERT INTO check_in_logs (member_id) VALUES ($new_id)");
        
        echo json_encode(["status" => "success", "user_name" => $name]);
    }
}
$conn->close();
?>