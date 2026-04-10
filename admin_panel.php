<?php
// 1. 建立資料庫連線
$conn = new mysqli("localhost", "root", "", "kids_club");
if ($conn->connect_error) die("連線失敗: " . $conn->connect_error);

$today = date('Y-m-d');

// 2. 抓取今日日報資訊
$duty_res = $conn->query("SELECT * FROM daily_duty WHERE duty_date = '$today'");
$duty = $duty_res->fetch_assoc();
$morning_staff = $duty['morning_staff'] ?? '';
$afternoon_staff = $duty['afternoon_staff'] ?? '';
$db_c02_4f = $duty['count_02_4f'] ?? 0;
$db_c02_5f = $duty['count_02_5f'] ?? 0;

// 3. 抓取即時紀錄 (加入 m.relationship)
$sql = "SELECT m.id as member_id, c.checked_parents, c.checked_children, m.phone, 
               c.remark, c.floor, c.channel, c.check_in_time, c.log_id,
               m.child_name, m.child_gender, m.relationship
        FROM check_in_logs c 
        JOIN members m ON c.member_id = m.id 
        WHERE DATE(c.check_in_time) = '$today'
        ORDER BY c.check_in_time DESC";
$result = $conn->query($sql);

$total_adults = 0;
$total_children = 0;
$total_boys = 0;
$total_girls = 0;
$count_4f = 0;
$count_5f = 0;
$rows = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $parents_arr = array_filter(explode('|', $row['checked_parents'] ?? ''));
        $children_arr = array_filter(explode('|', $row['checked_children'] ?? ''));
        $row['p_count'] = count($parents_arr);
        $row['c_count'] = count($children_arr);
        $total_adults += $row['p_count'];
        $total_children += $row['c_count'];

        // 統計邏輯：只有維持在 4F 或 5F 的兩組會被計算，已離館的不算在內
        if ($row['floor'] === '4F') $count_4f++;
        elseif ($row['floor'] === '5F') $count_5f++;

        $all_names = explode('|', $row['child_name'] ?? '');
        $all_genders = explode('|', $row['child_gender'] ?? '');
        foreach ($children_arr as $c_name) {
            $idx = array_search(trim($c_name), $all_names);
            if ($idx !== false) {
                $gender = $all_genders[$idx] ?? '';
                if ($gender === '男') $total_boys++;
                elseif ($gender === '女') $total_girls++;
            }
        }
        $rows[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>管理後台 | 中壢過嶺親子館</title>
    <meta http-equiv="refresh" content="10">
    <style>
        body {
            font-family: sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .duty-box {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 15px;
            align-items: center;
            border-left: 5px solid #468b4c;
        }

        .duty-box label {
            font-weight: bold;
            color: #555;
            font-size: 14px;
        }

        .duty-input {
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 80px;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex: 1;
            min-width: 100px;
            text-align: center;
        }

        .card h3 {
            margin: 0;
            color: #777;
            font-size: 24px;
        }

        .card p {
            margin: 10px 0;
            text-align: center;
            font-size: 34px;
            font-weight: bold;
            color: #48a187;
        }

        .sub-input-box {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #eee;
            font-size: 15px;
            color: #666;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow-x: auto;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: center;
            font-size: 14px;
        }

        th {
            background: #48a187;
            color: white;
        }

        .floor-select,
        .channel-select {
            padding: 4px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .remark-input {
            width: 150px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .save-btn {
            background: #48a187;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        /* 補回的按鈕樣式 */
        .action-btns {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-export {
            text-decoration: none;
            background: #3c8326;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-history {
            text-decoration: none;
            background: #3c688e;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="header-top">
        <div>
            <h1 style="color: #468b4c; margin: 0;">今日入館資訊</h1>
            <p style="color: #888; margin: 5px 0 0;"><?php echo date('Y/m/d'); ?> 管理後臺</p>
            <div class="action-btns">
                <a href="export_excel.php" class="btn-export">匯出 Excel</a>
                <a href="history.php" class="btn-history">歷史紀錄</a>
            </div>
        </div>
        <div class="duty-box">
            <div>
                <label>上午值班：</label>
                <input type="text" id="m_staff" class="duty-input" value="<?php echo htmlspecialchars($morning_staff); ?>">
            </div>
            <div>
                <label>下午值班：</label>
                <input type="text" id="a_staff" class="duty-input" value="<?php echo htmlspecialchars($afternoon_staff); ?>">
            </div>
            <button class="save-btn" onclick="saveDuty()">儲存日報資料</button>
        </div>
    </div>

    <div class="stats">
        <div class="card">
            <h3>今日成人總數</h3>
            <p><?php echo $total_adults; ?> 人</p>
        </div>

        <div class="card">
            <h3>今日幼兒總數</h3>
            <p><?php echo $total_children; ?> 人</p>
            <div style="font-size: 16px; color: #666; margin-top: 8px;">
                ( 男 <?php echo $total_boys; ?> / 女 <?php echo $total_girls; ?> )
            </div>
        </div>

        <div class="card">
            <h3>總入館組數</h3>
            <p><?php echo count($rows); ?> 組</p>
        </div>

        <div class="card" style="border-top: 4px solid #3c688e;">
            <h3>4F 組數</h3>
            <p><?php echo $count_4f; ?> 組</p>
            <div class="sub-input-box">
                0-2專區：<input type="number" id="c02_4f" class="duty-input" value="<?php echo $db_c02_4f; ?>" style="width: 50px;"> 組
            </div>
        </div>

        <div class="card" style="border-top: 4px solid #3c688e;">
            <h3>5F 組數</h3>
            <p><?php echo $count_5f; ?> 組</p>
            <div class="sub-input-box">
                0-2專區：<input type="number" id="c02_5f" class="duty-input" value="<?php echo $db_c02_5f; ?>" style="width: 50px;"> 組
            </div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>入館時間</th>
                    <th>家長姓名</th>
                    <th>手機</th>
                    <th>成人人數</th>
                    <th>幼兒人數</th>
                    <th>幼兒姓名</th>
                    <th>關係</th>
                    <th>樓層</th>
                    <th>預約管道</th>
                    <th>備註</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo date('H:i', strtotime($row['check_in_time'])); ?></td>
                        <td><strong><?php echo str_replace('|', '<br>', htmlspecialchars($row['checked_parents'])); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo $row['p_count']; ?></td>
                        <td><span style="color: #48a187; font-weight: bold;"><?php echo $row['c_count']; ?></span></td>
                        <td><?php echo str_replace('|', ', ', htmlspecialchars($row['checked_children'])); ?></td>
                        <td><?php echo str_replace('|', ', ', htmlspecialchars($row['relationship'])); ?></td>
                        <td>
                            <select class="floor-select" id="floor_<?php echo $row['log_id']; ?>">
                                <option value=""></option>
                                <option value="4F" <?php echo ($row['floor'] == '4F') ? 'selected' : ''; ?>>4F</option>
                                <option value="5F" <?php echo ($row['floor'] == '5F') ? 'selected' : ''; ?>>5F</option>
                                <option value="已離館" <?php echo ($row['floor'] == '已離館') ? 'selected' : ''; ?>>已離館</option>
                            </select>
                        </td>
                        <td>
                            <select class="channel-select" id="channel_<?php echo $row['log_id']; ?>">
                                <option value=""></option>
                                <option value="網路預約" <?php echo ($row['channel'] == '網路預約') ? 'selected' : ''; ?>>網路預約</option>
                                <option value="現場報名" <?php echo ($row['channel'] == '現場報名') ? 'selected' : ''; ?>>現場報名</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="remark-input" id="remark_<?php echo $row['log_id']; ?>" value="<?php echo htmlspecialchars($row['remark'] ?? ''); ?>">
                            <button class="save-btn" onclick="saveData(<?php echo $row['log_id']; ?>)">✔</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        async function saveDuty() {
            const data = new URLSearchParams({
                action: 'update_daily_duty',
                morning_staff: document.getElementById('m_staff').value,
                afternoon_staff: document.getElementById('a_staff').value,
                count_02_4f: document.getElementById('c02_4f').value,
                count_02_5f: document.getElementById('c02_5f').value
            });
            try {
                const response = await fetch('checkin_logic.php', {
                    method: 'POST',
                    body: data
                });
                const res = await response.json();
                if (res.status === 'success') alert('日報資料儲存成功');
            } catch (error) {
                alert('儲存發生錯誤');
            }
        }

        async function saveData(logId) {
            const data = new URLSearchParams({
                action: 'update_remark',
                log_id: logId,
                remark: document.getElementById('remark_' + logId).value,
                floor: document.getElementById('floor_' + logId).value,
                channel: document.getElementById('channel_' + logId).value
            });
            try {
                const response = await fetch('checkin_logic.php', {
                    method: 'POST',
                    body: data
                });
                const res = await response.json();
                if (res.status === 'success') alert('已儲存備註');
            } catch (error) {
                alert('通訊錯誤');
            }
        }
    </script>
</body>

</html>