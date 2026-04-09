<?php
// 1. 建立資料庫連線
$conn = new mysqli("localhost", "root", "", "kids_club");

if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

// 2. 查詢今日進館清單 (從 log 抓取當次勾選的 checked_parents 與 checked_children)
$sql = "SELECT m.id as member_id, c.checked_parents, c.checked_children, m.phone, m.remark, c.check_in_time 
        FROM check_in_logs c
        JOIN members m ON c.member_id = m.id 
        WHERE DATE(c.check_in_time) = CURDATE()
        ORDER BY c.check_in_time DESC";

$result = $conn->query($sql);

// 3. 統計今日實到總人數 (根據當次勾選名單計算)
$total_adults = 0;
$total_children = 0;

// 先將結果存入陣列，方便下方表格重複使用，同時計算統計數據
$rows = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $parents_arr = array_filter(explode('|', $row['checked_parents'] ?? ''));
        $children_arr = array_filter(explode('|', $row['checked_children'] ?? ''));
        
        $row['p_count'] = count($parents_arr);
        $row['c_count'] = count($children_arr);
        
        $total_adults += $row['p_count'];
        $total_children += $row['c_count'];
        $rows[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>管理後台 | 中壢過嶺親子館</title>
    <meta http-equiv="refresh" content="30">
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
        .card { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex: 1; min-width: 150px; text-align: center; }
        .card h3 { margin: 0; color: #777; font-size: 14px; }
        .card p { margin: 10px 0 0; font-size: 24px; font-weight: bold; color: #48a187; }
        
        .table-container { background: white; border-radius: 10px; overflow-x: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: center; font-size: 15px; }
        th { background: #48a187; color: white; white-space: nowrap; }
        tr:hover { background: #fdfaf0; }
        
        .btn-export { text-decoration: none; background: #3c8326; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; display: inline-block; margin-bottom: 15px; }
        .remark-input { width: 120px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; }
        .save-btn { background: #48a187; color: white; border: none; padding: 5px 8px; border-radius: 4px; cursor: pointer; margin-left: 5px; }
    </style>
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 style="color: #468b4c;">今日入館即時概況</h1>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="export_excel.php" class="btn-export" style="margin-bottom: 0;">📥 匯出 Excel</a>
            <a href="history.php" style="text-decoration: none; background: #3c688e; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; display: inline-block;">🔍 歷史紀錄</a>
        </div>
    </div>
    
    <div class="stats">
        <div class="card">
            <h3>今日成人總數 (實到)</h3>
            <p><?php echo $total_adults; ?> <span style="font-size: 14px;">人</span></p>
        </div>
        <div class="card">
            <h3>今日幼兒總數 (實到)</h3>
            <p><?php echo $total_children; ?> <span style="font-size: 14px;">人</span></p>
        </div>
        <div class="card">
            <h3>總進館組數</h3>
            <p><?php echo count($rows); ?> <span style="font-size: 14px;">組</span></p>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>入館時間</th>
                    <th>當次入館家長</th>
                    <th>手機號碼</th>
                    <th>大人數</th>
                    <th>幼兒數</th>
                    <th>當次入館幼兒</th>
                    <th>館方備註</th> 
                </tr>
            </thead>
            <tbody>
                <?php if (count($rows) > 0): ?>
                    <?php foreach($rows as $row): ?>
                    <tr>
                        <td><?php echo date('H:i:s', strtotime($row['check_in_time'])); ?></td>
                        <td><strong><?php echo str_replace('|', '<br>', htmlspecialchars($row['checked_parents'])); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo $row['p_count']; ?></td>
                        <td><span style="color: #48a187; font-weight: bold;"><?php echo $row['c_count']; ?></span></td>
                        <td><?php echo str_replace('|', ', ', htmlspecialchars($row['checked_children'])); ?></td>
                        <td class="remark-text">
                            <input type="text" class="remark-input" id="remark_<?php echo $row['member_id']; ?>" value="<?php echo htmlspecialchars($row['remark'] ?? ''); ?>">
                            <button class="save-btn" onclick="saveRemark(<?php echo $row['member_id']; ?>)">✔</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="padding: 40px; color: #999;">今日尚無人報到</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    async function saveRemark(memberId) {
        const remarkValue = document.getElementById('remark_' + memberId).value;
        const data = new URLSearchParams({
            action: 'update_remark',
            member_id: memberId,
            remark: remarkValue
        });
        
        try {
            const response = await fetch('checkin_logic.php', { method: 'POST', body: data });
            const res = await response.json();
            if (res.status === 'success') {
                alert('備註已儲存');
            } else {
                alert('儲存失敗：' + res.message);
            }
        } catch (error) {
            alert('系統通訊錯誤');
        }
    }
    </script>
</body>
</html>