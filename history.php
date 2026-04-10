<?php
$conn = new mysqli("localhost", "root", "", "kids_club");

// 接收搜尋參數
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$where_clause = "";

// 判斷日期區間邏輯
if (!empty($start_date) && !empty($end_date)) {
    $where_clause = " WHERE DATE(c.check_in_time) BETWEEN '$start_date' AND '$end_date' ";
} elseif (!empty($start_date)) {
    $where_clause = " WHERE DATE(c.check_in_time) >= '$start_date' ";
} elseif (!empty($end_date)) {
    $where_clause = " WHERE DATE(c.check_in_time) <= '$end_date' ";
}

// 查詢歷史清單
$sql = "SELECT m.parent_name, m.phone, m.adult_male, m.adult_female, m.child_count, 
               m.child_gender, c.check_in_time 
        FROM check_in_logs c
        JOIN members m ON c.member_id = m.id 
        $where_clause
        ORDER BY c.check_in_time DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>歷史紀錄查詢 | 中壢過嶺親子館</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .search-box { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .search-box form { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        input[type="date"] { padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-search { background: #48a187; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;}
        .btn-export { background: #3c8326; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-weight: bold;font-size: 14px; }
        .table-container { background: white; border-radius: 10px; overflow-x: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: center; }
        th { background: #48a187; color: white; }
        .date-tag { background: #e9ecef; padding: 3px 8px; border-radius: 4px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header-section">
        <h1 style="color: #468b4c;">歷史紀錄查詢</h1>
        <a href="admin_panel.php" style="text-decoration: none; color: #666;">⬅ 返回管理面板</a>
    </div>

    <div class="search-box">
        <form method="GET">
            <strong>起始日期：</strong>
            <input type="date" name="start_date" value="<?php echo $start_date; ?>">
            <strong>至 結束日期：</strong>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>">
            <button type="submit" class="btn-search">搜尋</button>
            
            <a href="export_excel.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn-export">匯出 Excel</a>
            
            <?php if($start_date || $end_date): ?>
                <a href="history.php" style="color: #dc3545; font-size: 13px;">清除篩選</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <table>
            <tr>
                <th>進館日期時間</th>
                <th>家長姓名</th>
                <th>手機號碼</th>
                <th>人數 (成/幼)</th>
                <th>幼兒性別</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): 
                $gender_display = str_replace('|', ' / ', $row['child_gender']);
            ?>
            <tr>
                <td>
                    <span class="date-tag"><?php echo date('Y-m-d', strtotime($row['check_in_time'])); ?></span><br>
                    <strong><?php echo date('H:i:s', strtotime($row['check_in_time'])); ?></strong>
                </td>
                <td><?php echo htmlspecialchars($row['parent_name']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo ($row['adult_male']+$row['adult_female'])." / ".$row['child_count']; ?></td>
                <td><?php echo htmlspecialchars($gender_display); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>