<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "kids_club");

if ($conn->connect_error) die(json_encode(["status" => "error", "message" => "連線失敗"]));

$phone = $_POST['phone'] ?? '';
$name = $_POST['name'] ?? '';

// 1. 檢查會員
$check = $conn->query("SELECT id, parent_name FROM members WHERE phone = '$phone'");

if ($check->num_rows > 0) {
    // 老朋友：紀錄進館
    $user = $check->fetch_assoc();
    $member_id = $user['id'];
    $conn->query("INSERT INTO check_in_logs (member_id) VALUES ($member_id)");
    echo json_encode(["status" => "success", "user_name" => $user['parent_name']]);
} else {
    if (empty($name)) {
        // 通知前端顯示完整問卷
        echo json_encode(["status" => "need_register"]);
    } else {
        // 新朋友：註冊並紀錄進館
        $district = $_POST['district'] ?? '';
        $purpose = $_POST['purpose'] ?? '';
        $relationship = $_POST['relationship'] ?? '';
        $adult_male = (int)$_POST['adult_male'];
        $adult_female = (int)$_POST['adult_female'];
        $child_count = (int)$_POST['child_count'];
        $resp_type = $_POST['respondent_type'] ?? '';
        $langs = $_POST['languages'] ?? '';

        $sql = "INSERT INTO members (parent_name, phone, district, purpose, relationship, adult_male, adult_female, child_count, respondent_type, languages) 
                VALUES ('$name', '$phone', '$district', '$purpose', '$relationship', $adult_male, $adult_female, $child_count, '$resp_type', '$langs')";
        
        if ($conn->query($sql)) {
            $new_id = $conn->insert_id;
            $conn->query("INSERT INTO check_in_logs (member_id) VALUES ($new_id)");
            echo json_encode(["status" => "success", "user_name" => $name]);
        } else {
            echo json_encode(["status" => "error", "message" => "資料儲存失敗: " . $conn->error]);
        }
    }
}
$conn->close();
?>