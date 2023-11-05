<!DOCTYPE html>
<html>
<head>
    <title>执行数据库操作</title>
    <!-- 引入 Bootstrap 的 CSS 文件 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">执行数据库操作</h1>

        <?php
            function isLoggedIn() {
    return isset($_COOKIE['username']);
}
    if (!isLoggedIn()) {
  // 用户未登录，重定向到 login-plz.html
  header('Location: /admin/login-plz.html');
  exit();
}
        // 处理用户输入
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $confirm1 = $_POST["confirm1"];
            $confirm2 = $_POST["confirm2"];

            if ($confirm1 == "yes" && $confirm2 == "yes") {
                // 执行数据库操作的代码
                require_once ('/www/wwwroot/whitelist.hln-network.xyz/config.php');

                // 创建数据库连接
                global $config;
                $conn = new mysqli($config['servername'], $config['username'], $config['password']);

                // 检查连接是否成功
                if ($conn->connect_error) {
                    die("数据库连接失败: " . $conn->connect_error);
                }

                $database = $config['database'];
                $checkDBExistsQuery = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'";
                $result = $conn->query($checkDBExistsQuery);

                if ($result->num_rows > 0) {
                    // 数据库存在，进行删除操作
                    $dropDBQuery = "DROP DATABASE $database";
                    if ($conn->query($dropDBQuery) === true) {
                        echo "<p class='text-success'>数据库删除成功</p>";
                    } else {
                        echo "<p class='text-danger'>数据库删除失败: " . $conn->error . "</p>";
                        }
                    } else {
                    echo "<p class='text-info'>数据库不存在，无需删除</p>";
                }

                // 创建数据库
                $database = $config['database'];
                $sql = "CREATE DATABASE IF NOT EXISTS $database";
                if ($conn->query($sql) === true) {
                    echo "<p class='text-success'>数据库创建成功</p>";
                } else {
                    echo "<p class='text-danger'>数据库创建失败: " . $conn->error . "</p>";
                }

                // 选择数据库
                $conn->select_db($config['database']);

                // 创建 卡密 表
                $createCodesBase = "CREATE TABLE `activation_codes` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `activation_code` varchar(50) NOT NULL,
 `status` int(1) DEFAULT '0',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                if ($conn->query($createCodeBase) === true) {
                    echo "<p class='text-success'>卡密表创建成功</p>";
                } else {
                    echo "<p class='text-danger'>卡密表创建失败: " . $conn->error . "</p>";
                }

                $createUserBase = "CREATE TABLE `whitelist` (
 `QQ` varchar(20) NOT NULL,
 `playerName` varchar(50) NOT NULL,
 `expired_at` varchar(30) NOT NULL,
 PRIMARY KEY (`QQ`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                if ($conn->query($createCodeBase) === true) {
                    echo "<p class='text-success'>用户表创建成功</p>";
                } else {
                    echo "<p class='text-danger'>用户表创建失败: " . $conn->error . "</p>";
                }

                // 关闭数据库连接
                $conn->close();
            } else {
                echo "<p class='text-warning'>用户未确认执行操作</p>";
            }
        }
        ?>

        <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>" class="mt-4">
            <div class="mb-3">
                <label for="confirm1" class="form-label">是否确认执行此代码操作? (yes/no)</label>
                <input type="text" name="confirm1" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="confirm2" class="form-label">再次确认是否执行此代码操作? (yes/no)</label>
                <input type="text" name="confirm2" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">执行操作</button>
        </form>
    </div>

    <!-- 引入 Bootstrap 的 JavaScript 文件 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>