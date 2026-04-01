<?php
$conn = new mysqli("localhost", "root", "", "kids_club");

// 查詢今日進館清單
$sql = "SELECT m.parent_name, m.phone, m.adult_male, m.adult_female, m.child_count, 
               m.child_gender, c.check_in_time 
        FROM check_in_logs c
        JOIN members m ON c.member_id = m.id 
        WHERE DATE(c.check_in_time) = CURDATE()
        ORDER BY c.check_in_time DESC";

$result = $conn->query($sql);

// 統計今日總人數
$total_sql = "SELECT SUM(m.adult_male + m.adult_female) as total_adults, SUM(m.child_count) as total_children,
                     GROUP_CONCAT(m.child_gender SEPARATOR '|') as all_genders
              FROM check_in_logs c 
              JOIN members m ON c.member_id = m.id 
              WHERE DATE(c.check_in_time) = CURDATE()";
$total_data = $conn->query($total_sql)->fetch_assoc();

// 初始化性別統計變數
$boy_count = 0;
$girl_count = 0;

// 拆解所有性別字串並統計
if (!empty($total_data['all_genders'])) {
    $gender_array = explode('|', $total_data['all_genders']);
    foreach ($gender_array as $g) {
        if ($g === '男') $boy_count++;
        if ($g === '女') $girl_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>管理後台 | 中壢過嶺親子館</title>
    <meta http-equiv="refresh" content="5">
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
        .card { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex: 1; min-width: 150px; text-align: center; }
        .card h3 { margin: 0; color: #777; font-size: 14px; }
        .card p { margin: 10px 0 0; font-size: 24px; font-weight: bold; color: #48a187; }
        .card .sub-text { font-size: 14px; color: #666; margin-top: 5px; font-weight: normal; }
        
        .table-container { background: white; border-radius: 10px; overflow-x: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: center; font-size: 15px; }
        th { background: #48a187; color: white; white-space: nowrap; }
        tr:hover { background: #fdfaf0; }
        
        .btn-export { text-decoration: none; background: #28a745; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; display: inline-block; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 style="color: #468b4c;">今日入館即時概況</h1>
        <a href="export_excel.php" class="btn-export">📥 匯出完整 Excel 紀錄</a>
    </div>
    
    <div class="stats">
        <div class="card">
            <h3>今日成人總數</h3>
            <p><?php echo (int)$total_data['total_adults']; ?> <span style="font-size: 14px;">人</span></p>
        </div>
        <div class="card">
            <h3>今日幼兒總數</h3>
            <p><?php echo (int)$total_data['total_children']; ?> <span style="font-size: 14px;">人</span></p>
            <div class="sub-text">男：<?php echo $boy_count; ?> | 女：<?php echo $girl_count; ?></div>
        </div>
        <div class="card">
            <h3>總進館組數</h3>
            <p><?php echo $result->num_rows; ?> <span style="font-size: 14px;">組</span></p>
        </div>
    </div>

    <div class="table-container">
        <table>
            <tr>
                <th>入館時間</th>
                <th>家長姓名</th>
                <th>手機號碼</th>
                <th>大人 (男/女)</th>
                <th>幼兒數</th>
                <th>幼兒性別</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): 
                    $gender_display = str_replace('|', ' / ', $row['child_gender']);
                ?>
                <tr>
                    <td><?php echo date('H:i:s', strtotime($row['check_in_time'])); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['parent_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo $row['adult_male']." / ".$row['adult_female']; ?></td>
                    <td><span style="color: #48a187; font-weight: bold;"><?php echo $row['child_count']; ?></span></td>
                    <td><?php echo htmlspecialchars($gender_display); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="padding: 40px; color: #999;">今日尚無人報到</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>