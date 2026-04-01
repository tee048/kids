<?php
$conn = new mysqli("localhost", "root", "", "kids_club");
if ($conn->connect_error) die("連線失敗");

// 支援繁體中文的 CSV Header
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=親子館入館紀錄_' . date('Ymd') . '.csv');

// 輸出 UTF-8 BOM，讓 Excel 開啟時不會亂碼
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// 設定 Excel 第一列的標題
fputcsv($output, array('入館日期', '入館時間', '家長姓名', '手機號碼', '居住區域', '入館目的', '大人(男)', '大人(女)', '幼兒人數', '常用語言'));

// 抓取所有歷史紀錄 (不限當天，方便館方統計)
$sql = "SELECT c.check_in_time, m.parent_name, m.phone, m.district, m.purpose, m.adult_male, m.adult_female, m.child_count, m.languages 
        FROM check_in_logs c
        JOIN members m ON c.member_id = m.id 
        ORDER BY c.check_in_time DESC";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $date = date('Y-m-d', strtotime($row['check_in_time']));
    $time = date('H:i:s', strtotime($row['check_in_time']));
    
    fputcsv($output, array(
        $date,
        $time,
        $row['parent_name'],
        $row['phone'],
        $row['district'],
        $row['purpose'],
        $row['adult_male'],
        $row['adult_female'],
        $row['child_count'],
        $row['languages']
    ));
}

fclose($output);
$conn->close();
?>