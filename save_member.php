<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "kids_club"); // 請依環境修改密碼

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "連線失敗"]));
}

$name = $_POST['name'];
$phone = $_POST['phone'];

// 先檢查電話是否重複
$check = $conn->query("SELECT id FROM members WHERE phone = '$phone'");
if ($check->num_rows > 0) {
    $row = $check->fetch_assoc();
    echo json_encode(["status" => "success", "member_id" => $row['id']]);
} else {
    // 插入新會員
    $sql = "INSERT INTO members (parent_name, phone) VALUES ('$name', '$phone')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "member_id" => $conn->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "寫入失敗"]);
    }
}
$conn->close();
?>