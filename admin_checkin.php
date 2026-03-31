<?php
// 模擬管理員掃描 QR Code 後傳入 ID
$member_id = $_GET['id'] ?? null;

if ($member_id) {
    $conn = new mysqli("localhost", "root", "", "kids_club");
    
    // 檢查是否有此會員
    $result = $conn->query("SELECT parent_name FROM members WHERE id = $member_id");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // 寫入進館紀錄
        $conn->query("INSERT INTO check_in_logs (member_id) VALUES ($member_id)");
        echo "<h1>核准進館！</h1>";
        echo "歡迎回來，" . $user['parent_name'] . " 家長。";
    } else {
        echo "<h1>無效的身分碼！</h1>";
    }
    $conn->close();
} else {
    echo "請掃描 QR Code。";
}
?>