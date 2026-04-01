<?php
$conn = new mysqli("localhost", "root", "", "kids_club");
if ($conn->connect_error) die("連線失敗");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=過嶺親子館入館紀錄_' . date('Ymd') . '.csv');

// 輸出 UTF-8 BOM 避免 Excel 亂碼
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

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
        ORDER BY c.check_in_time DESC";

$result = $conn->query($sql);

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
}

fclose($output);
$conn->close();
?>