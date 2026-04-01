<?php
$conn = new mysqli("localhost", "root", "", "kids_club");

// 查詢今日進館清單
$sql = "SELECT m.parent_name, m.phone, m.adult_male, m.adult_female, m.child_count, c.check_in_time 
        FROM check_in_logs c
        JOIN members m ON c.member_id = m.id 
        WHERE DATE(c.check_in_time) = CURDATE()
        ORDER BY c.check_in_time DESC";

$result = $conn->query($sql);

// 統計今日總人數
$total_sql = "SELECT SUM(m.adult_male + m.adult_female) as total_adults, SUM(m.child_count) as total_children 
              FROM check_in_logs c 
              JOIN members m ON c.member_id = m.id 
              WHERE DATE(c.check_in_time) = CURDATE()";
$total_res = $conn->query($total_sql)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>管理後台 | 中壢過嶺親子館</title>
    <meta http-equiv="refresh" content="5">
    <style>
        body {
            font-family: sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex: 1;
            text-align: center;
        }

        .card h3 {
            margin: 0;
            color: #777;
            font-size: 14px;
        }

        .card p {
            margin: 10px 0 0;
            font-size: 24px;
            font-weight: bold;
            color: #e2ad39;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #e2ad39;
            color: white;
        }

        tr:hover {
            background: #fdfaf0;
        }
    </style>
</head>

<body>
    <h1>今日入館即時概況</h1>

    <div class="stats">
        <div class="card">
            <h3>今日成人總數</h3>
            <p><?php echo (int)$total_res['total_adults']; ?> 人</p>
        </div>
        <div class="card">
            <h3>今日幼兒總數</h3>
            <p><?php echo (int)$total_res['total_children']; ?> 人</p>
        </div>
        <div class="card">
            <h3>總進館組數</h3>
            <p><?php echo $result->num_rows; ?> 組</p>
        </div>
    </div>

    <div style="margin-bottom: 20px; text-align: right;">
        <a href="export_excel.php" style="text-decoration: none; background: #49ad60; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; display: inline-block;">
            匯出 Excel
        </a>
    </div>

    <table>
        <tr>
            <th>入館時間</th>
            <th>家長姓名</th>
            <th>手機</th>
            <th>大人(男/女)</th>
            <th>幼兒數</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo date('H:i:s', strtotime($row['check_in_time'])); ?></td>
                <td><?php echo $row['parent_name']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo "♂ " . $row['adult_male'] . " / ♀ " . $row['adult_female']; ?></td>
                <td><?php echo $row['child_count']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>