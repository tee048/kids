<?php
// 強制顯示錯誤，方便除錯
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "kids_club");
if ($conn->connect_error) die("連線失敗");

// 接收區間參數
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$where_clause = "";
$filename = "過嶺親子館入館紀錄";

if (!empty($start_date) && !empty($end_date)) {
    $where_clause = " WHERE DATE(c.check_in_time) BETWEEN '$start_date' AND '$end_date' ";
    $filename .= "_{$start_date}_to_{$end_date}";
} elseif (!empty($start_date)) {
    $where_clause = " WHERE DATE(c.check_in_time) >= '$start_date' ";
} elseif (!empty($end_date)) {
    $where_clause = " WHERE DATE(c.check_in_time) <= '$end_date' ";
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename . '.csv');

// 輸出 UTF-8 BOM 避免 Excel 亂碼
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// 1. 設定全新標題列 (加入「關係」欄位)
fputcsv($output, array(
    '日期', '時間', '當次入館家長', '大人實到數', '當次入館幼兒', '幼兒實到數', '手機號碼', '區域', 
    '幼兒1姓名', '幼兒1出生日期', '幼兒1歲數', '幼兒1年齡區間', '幼兒1性別', 
    '幼兒2姓名', '幼兒2出生日期', '幼兒2歲數', '幼兒2年齡區間', '幼兒2性別', 
    '幼兒3姓名', '幼兒3出生日期', '幼兒3歲數', '幼兒3年齡區間', '幼兒3性別', 
    '常用語言', '填答者身分別', '關係', '樓層', '報名管道', 
    '上午值班', '下午值班', '4F 0-2專區', '5F 0-2專區', '館方備註'
));

// 2. 執行查詢：加入 m.relationship
$sql = "SELECT c.check_in_time, c.checked_parents, c.checked_children, c.remark as log_remark, 
               c.floor, c.channel,
               m.child_name, m.child_birthday, m.child_exact_age, m.child_age_group, m.child_gender,
               m.phone, m.district, m.languages, m.respondent_type, m.relationship,
               d.morning_staff, d.afternoon_staff, d.count_02_4f, d.count_02_5f
        FROM check_in_logs c
        JOIN members m ON c.member_id = m.id 
        LEFT JOIN daily_duty d ON DATE(c.check_in_time) = d.duty_date
        $where_clause
        ORDER BY c.check_in_time DESC";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $checked_parents = $row['checked_parents'] ?? '';
        $checked_children = $row['checked_children'] ?? '';
        
        $p_arr = array_filter(explode('|', $checked_parents));
        $c_arr = array_filter(explode('|', $checked_children));
        
        $p_count = count($p_arr);
        $c_count = count($c_arr);
        
        $parents_str = str_replace('|', ', ', $checked_parents);
        $children_str = str_replace('|', ', ', $checked_children);

        // 拆解幼兒資料
        $all_names   = explode('|', $row['child_name'] ?? '');
        $all_bdays   = explode('|', $row['child_birthday'] ?? '');
        $all_e_ages  = explode('|', $row['child_exact_age'] ?? '');
        $all_groups  = explode('|', $row['child_age_group'] ?? '');
        $all_genders = explode('|', $row['child_gender'] ?? '');

        $child_data = [];
        foreach ($c_arr as $c_name) {
            $c_name_trim = trim($c_name);
            $idx = array_search($c_name_trim, $all_names);
            if ($idx !== false) {
                $child_data[] = [$c_name_trim, $all_bdays[$idx] ?? '', $all_e_ages[$idx] ?? '', $all_groups[$idx] ?? '', $all_genders[$idx] ?? ''];
            } else {
                $child_data[] = [$c_name_trim, '', '', '', ''];
            }
        }

        $child_output = [];
        for ($i = 0; $i < 3; $i++) {
            if (isset($child_data[$i])) {
                $child_output = array_merge($child_output, $child_data[$i]);
            } else {
                $child_output = array_merge($child_output, ['', '', '', '', '']);
            }
        }

        // 3. 組合最終匯出的這行資料
        $line = array(
            date('Y-m-d', strtotime($row['check_in_time'])), 
            date('H:i:s', strtotime($row['check_in_time'])), 
            $parents_str,
            $p_count,
            $children_str,
            $c_count,
            $row['phone'], 
            $row['district']
        );

        $line = array_merge($line, $child_output);
        
        // 依序填入後方欄位資料
        $line[] = $row['languages'];
        $line[] = $row['respondent_type'] ?? '';
        $line[] = str_replace('|', ', ', $row['relationship'] ?? ''); // 新增：關係 (處理多筆分隔號)
        $line[] = $row['floor'] ?? '';   
        $line[] = $row['channel'] ?? ''; 
        
        // 帶入當天的值班資訊與專區組數
        $line[] = $row['morning_staff'] ?? '';
        $line[] = $row['afternoon_staff'] ?? '';
        $line[] = $row['count_02_4f'] ?? '0';
        $line[] = $row['count_02_5f'] ?? '0';
        
        $line[] = $row['log_remark'] ?? ''; 

        fputcsv($output, $line);
    }
}

fclose($output);
exit;
?>