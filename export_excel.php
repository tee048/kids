<?php
// 強制顯示錯誤，方便除錯
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
} elseif (!empty($end_date)) {
    $where_clause = " WHERE DATE(c.check_in_time) <= '$end_date' ";
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename . '.csv');

// 輸出 UTF-8 BOM 避免 Excel 亂碼
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// 1. 設定全新的標題列：加入「當次實到」與「館方備註」
fputcsv($output, array(
    '日期', '時間', '當次入館家長', '大人實到數', '當次入館幼兒', '幼兒實到數', '手機號碼', '區域', 
    '幼兒1姓名', '幼兒1出生日期', '幼兒1歲數', '幼兒1年齡區間', '幼兒1性別', 
    '幼兒2姓名', '幼兒2出生日期', '幼兒2歲數', '幼兒2年齡區間', '幼兒2性別', 
    '幼兒3姓名', '幼兒3出生日期', '幼兒3歲數', '幼兒3年齡區間', '幼兒3性別', 
    '常用語言', '館方備註'
));

// 2. 執行查詢：精準抓取 check_in_logs 的紀錄
$sql = "SELECT c.check_in_time, c.checked_parents, c.checked_children, c.remark, m.* FROM check_in_logs c
        JOIN members m ON c.member_id = m.id 
        $where_clause
        ORDER BY c.check_in_time DESC";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // 處理當次入館名單與人數統計
        $checked_parents = $row['checked_parents'] ?? '';
        $checked_children = $row['checked_children'] ?? '';
        
        $p_arr = array_filter(explode('|', $checked_parents));
        $c_arr = array_filter(explode('|', $checked_children));
        
        $p_count = count($p_arr);
        $c_count = count($c_arr);
        
        $parents_str = str_replace('|', ', ', $checked_parents);
        $children_str = str_replace('|', ', ', $checked_children);

        // 拆解儲存在資料庫中的會員「所有」幼兒資料
        $all_names   = explode('|', $row['child_name'] ?? '');
        $all_bdays   = explode('|', $row['child_birthday'] ?? '');
        $all_e_ages  = explode('|', $row['child_exact_age'] ?? '');
        $all_groups  = explode('|', $row['child_age_group'] ?? '');
        $all_genders = explode('|', $row['child_gender'] ?? '');

        // 【核心邏輯】只抓出「當次有入館」的幼兒詳細資訊
        $child_data = [];
        foreach ($c_arr as $c_name) {
            $idx = array_search(trim($c_name), $all_names);
            if ($idx !== false) {
                // 如果在主檔有找到這個小孩，帶入詳細資料
                $child_data[] = [
                    $c_name,
                    $all_bdays[$idx] ?? '',
                    $all_e_ages[$idx] ?? '',
                    $all_groups[$idx] ?? '',
                    $all_genders[$idx] ?? ''
                ];
            } else {
                // 若為當次臨時新增的幼兒，僅輸出姓名，其餘留白
                $child_data[] = [$c_name, '', '', '', ''];
            }
        }

        // 準備幼兒輸出欄位 (固定保留 3 組欄位，共 15 格)
        $child_output = [];
        for ($i = 0; $i < 3; $i++) {
            if (isset($child_data[$i])) {
                $child_output[] = $child_data[$i][0];
                $child_output[] = $child_data[$i][1];
                $child_output[] = $child_data[$i][2];
                $child_output[] = $child_data[$i][3];
                $child_output[] = $child_data[$i][4];
            } else {
                // 名額未滿，以空字串補齊 Excel 格子
                array_push($child_output, '', '', '', '', '');
            }
        }

        // 組合最終匯出的這行資料
        $line = array(
            date('Y-m-d', strtotime($row['check_in_time'])), 
            date('H:i:s', strtotime($row['check_in_time'])), 
            $parents_str,     // 當次入館家長
            $p_count,         // 大人實到數
            $children_str,    // 當次入館幼兒
            $c_count,         // 幼兒實到數
            $row['phone'], 
            $row['district']
        );

        // 合併幼兒詳細資料 (15格)
        $line = array_merge($line, $child_output);

        // 加上語言與最新實裝的館方備註
        $line[] = $row['languages'];
        $line[] = $row['remark'] ?? ''; 

        // 寫入 CSV
        fputcsv($output, $line);
    }
}

fclose($output);
exit;
?>