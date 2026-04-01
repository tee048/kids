<?php
$conn = new mysqli("localhost", "root", "", "kids_club");
if ($conn->connect_error) die("連線失敗");

// 接收區間參數 (來自 history.php 或 admin_panel.php)
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$where_clause = "";
$filename = "過嶺親子館入館紀錄";

if (!empty($start_date) && !empty($end_date)) {
    $where_clause = " WHERE DATE(c.check_in_time) BETWEEN '$start_date' AND '$end_date' ";
    $filename .= "_{$start_date}_to_{$end_date}";
} elseif (!empty($start_date)) {
    $where_clause = " WHERE DATE(c.check_in_time) >= '$start_date' ";
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename . '.csv');

// 輸出 UTF-8 BOM 避免 Excel 亂碼
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

<<<<<<< HEAD
// 1. 設定標題列：每位幼兒佔 5 格 (姓名、生日、歲數、區間、性別)
fputcsv($output, array(
    '日期', '時間', '家長姓名', '手機', '區域', '男大人', '女大人', '總幼兒數', 
    '幼兒1姓名', '幼兒1出生日期', '幼兒1歲數', '幼兒1年齡區間', '幼兒1性別', 
    '幼兒2姓名', '幼兒2出生日期', '幼兒2歲數', '幼兒2年齡區間', '幼兒2性別', 
    '幼兒3姓名', '幼兒3出生日期', '幼兒3歲數', '幼兒3年齡區間', '幼兒3性別', 
    '常用語言'
));

// 2. 執行查詢
$sql = "SELECT c.check_in_time, m.* FROM check_in_logs c
        JOIN members m ON c.member_id = m.id 
        $where_clause
=======
// 設定標題列（預留 3 組幼兒欄位，這對 Excel 統計最友善）
fputcsv($output, array(
    '日期', '時間', '家長姓名', '手機', '區域', '男大人', '女大人', '總幼兒數', 
    '幼兒1姓名', '幼兒1生日', '幼兒1性別', 
    '幼兒2姓名', '幼兒2生日', '幼兒2性別', 
    '幼兒3姓名', '幼兒3生日', '幼兒3性別', 
    '常用語言'
));

$sql = "SELECT c.check_in_time, m.parent_name, m.phone, m.district, m.adult_male, m.adult_female, m.child_count, m.child_name, m.child_birthday, m.child_gender, m.languages 
        FROM check_in_logs c
        JOIN members m ON c.member_id = m.id 
>>>>>>> f307ccadecc1d6acc87192079c553ca5920d3e9f
        ORDER BY c.check_in_time DESC";

$result = $conn->query($sql);

<<<<<<< HEAD
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // 拆解儲存在資料庫中的字串 (以 | 分隔)
        $names   = explode('|', $row['child_name']);
        $bdays   = explode('|', $row['child_birthday']);   // 格式如 2021-05-01
        $e_ages  = explode('|', $row['child_exact_age']);  // 格式如 4歲11個月
        $groups  = explode('|', $row['child_age_group']);  // 格式如 3-6歲
        $genders = explode('|', $row['child_gender']);

        // 組合家長基礎資料 (8 個欄位)
        $line = array(
            date('Y-m-d', strtotime($row['check_in_time'])), 
            date('H:i:s', strtotime($row['check_in_time'])), 
            $row['parent_name'], 
            $row['phone'], 
            $row['district'], 
            $row['adult_male'], 
            $row['adult_female'], 
            $row['child_count']
        );

        // 3. 填充最多 3 組幼兒資料，每組固定填入 5 格
        for ($i = 0; $i < 3; $i++) {
            $line[] = $names[$i] ?? '';
            $line[] = $bdays[$i] ?? '';
            $line[] = $e_ages[$i] ?? '';
            $line[] = $groups[$i] ?? '';
            $line[] = $genders[$i] ?? '';
        }

        // 4. 最後補上常用語言 (1 個欄位)
        $line[] = $row['languages'];

        fputcsv($output, $line);
    }
=======
while ($row = $result->fetch_assoc()) {
    // 拆解用 | 串接的資料
    $names = explode('|', $row['child_name']);
    $birthdays = explode('|', $row['child_birthday']);
    $genders = explode('|', $row['child_gender']);

    // 組合基礎資料
    $line = array(
        date('Y-m-d', strtotime($row['check_in_time'])),
        date('H:i:s', strtotime($row['check_in_time'])),
        $row['parent_name'],
        $row['phone'],
        $row['district'],
        $row['adult_male'],
        $row['adult_female'],
        $row['child_count']
    );

    // 填充 3 組幼兒資料，如果沒有就留白
    for ($i = 0; $i < 3; $i++) {
        $line[] = $names[$i] ?? '';
        $line[] = $birthdays[$i] ?? '';
        $line[] = $genders[$i] ?? '';
    }

    // 最後加上語言
    $line[] = $row['languages'];

    fputcsv($output, $line);
>>>>>>> f307ccadecc1d6acc87192079c553ca5920d3e9f
}

fclose($output);
$conn->close();
?>