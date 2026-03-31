<?php
$conn = new mysqli("localhost", "root", "", "kids_club");

// 查詢今天的進館紀錄 (使用 JOIN 結合兩張表)
$sql = "SELECT members.parent_name, members.phone, check_in_logs.check_in_time 
        FROM check_in_logs 
        JOIN members ON check_in_logs.member_id = members.id 
        WHERE DATE(check_in_logs.check_in_time) = CURDATE()
        ORDER BY check_in_logs.check_in_time DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>親子館管理後台</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>今日進館清單</h1>
    <table>
        <tr>
            <th>時間</th>
            <th>家長姓名</th>
            <th>電話</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['check_in_time']; ?></td>
            <td><?php echo $row['parent_name']; ?></td>
            <td><?php echo $row['phone']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>