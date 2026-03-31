<?php
// 1. 連接資料庫
$conn = new mysqli("localhost", "root", "", "kids_club");

$msg = "歡迎光臨親子館，請輸入手機號碼報到";
$show_reg_form = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    
    // 2. 檢查是否註冊過
    $result = $conn->query("SELECT * FROM members WHERE phone = '$phone'");
    
    if ($result->num_rows > 0) {
        // 老朋友：直接紀錄進館
        $user = $result->fetch_assoc();
        $member_id = $user['id'];
        $conn->query("INSERT INTO check_in_logs (member_id) VALUES ($member_id)");
        $msg = "報到成功！歡迎回來，" . $user['parent_name'] . " 家長。";
    } else {
        // 新朋友：準備顯示註冊欄位
        $msg = "查無資料，請填寫基本資訊完成註冊與報到。";
        $show_reg_form = true;
    }
}
?>

<!-- 前端 HTML 部分 -->
<!DOCTYPE html>
<html>
<head><title>館內快速報到</title></head>
<body>
    <h2>親子館入口報到</h2>
    <p><?php echo $msg; ?></p>
    
    <form method="POST">
        <input type="text" name="phone" placeholder="請輸入電話" required value="<?php echo $_POST['phone'] ?? ''; ?>">
        
        <?php if ($show_reg_form): ?>
            <!-- 新朋友才看的到姓名欄位 -->
            <input type="text" name="parent_name" placeholder="請輸入您的姓名" required>
            <button type="submit" name="action" value="register">提交註冊並報到</button>
        <?php else: ?>
            <button type="submit">確認報到</button>
        <?php endif; ?>
    </form>
</body>
</html>