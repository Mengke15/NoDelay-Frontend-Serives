<?php
    // 引入 config.php 文件
    require_once ('/www/wwwroot/whitelist.hln-network.xyz/config.php');
    function isLoggedIn() {
    return isset($_COOKIE['username']);
}
    if (!isLoggedIn()) {
  // 用户未登录，重定向到 login-plz.html
  header('Location: /admin/login-plz.html');
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ADMIN HTML</title>

    <!-- 引入Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">ADMIN HTML</h1>
        <div class="d-flex justify-content-center mt-3">
            <a class="btn btn-primary me-2" href="<?php echo $url; ?>admin/actions/update.php">生成卡密</a>
            <a class="btn btn-primary me-2" href="<?php echo $url; ?>admin/actions/bind">添加用户</a>
            <a class="btn btn-primary me-2" href="<?php echo $url; ?>admin/users">查看用户</a>
            <a class="btn btn-primary me-2" href="<?php echo $url; ?>admin/cards">查看卡密</a>
            <a class="btn btn-danger me-2" href="<?php echo $url; ?>admin/actions/database.php">重置数据库</a>
        </div>
    </div>

    <!-- 引入Bootstrap JS（需要在body结束标签前引入） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>