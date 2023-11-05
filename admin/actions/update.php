<!DOCTYPE html>
<html>
<head>
    <title>生成激活码</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new ClipboardJS('.btn-copy');
        });
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .activation-code {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">生成激活码</h1>
        
    <?php
    require_once ('/www/wwwroot/whitelist.hln-network.xyz/config.php');
        function isLoggedIn() {
    return isset($_COOKIE['username']);
}
    if (!isLoggedIn()) {
  // 用户未登录，重定向到 login-plz.html
  header('Location: /admin/login-plz.html');
  exit();
}

    // 创建数据库连接
    global $config;
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['database']);
        if ($conn->connect_error) {
        die("数据库连接失败: " . $conn->connect_error);
    }

    function generateActivationCode($prefix, $suffix, $length) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $activationCode = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, strlen($characters) - 1);
            $activationCode .= $characters[$randomIndex];
        }

        return $prefix . '-' . $activationCode . '-' . $suffix;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $prefix = $_POST["prefix"];
        $suffix = $_POST["suffix"];
        $quantity = $_POST["quantity"];
        $length = $_POST["length"];

        // 生成激活码并插入数据库
        $activationCodes = array();

        for ($i = 0; $i < $quantity; $i++) {
            $activationCode = generateActivationCode($prefix, $suffix, $length);
            $status = 0;

            // 防止 SQL 注入攻击
            $activationCode = $conn->real_escape_string($activationCode);

            // 插入激活码和状态
            $insertResult = $conn->query("INSERT INTO activation_codes (activation_code, status) VALUES ('$activationCode', '$status')");
            if (!$insertResult) {
                echo "错误：无法插入激活码: $activationCode<br>\n";
            } else {
                $activationCodes[] = $activationCode;
            }
        }

        // 输出生成的激活码
        echo "<h2>生成的激活码：</h2>";
                echo "<div class='activation-codes'>";
                foreach ($activationCodes as $index => $code) {
                echo "<div class='activation-code'>$code</div>";

    // 在最后一个激活码后面添加复制按钮
                if ($index === count($activationCodes) - 1) {
                    echo "<button class='btn-copy' data-clipboard-text='" . implode('
', $activationCodes) . "'>复制全部激活码</button>";
    }
}
echo "</div>";
    }
    ?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="mt-4">
            <div class="mb-3">
                <label for="prefix" class="form-label">前缀：</label>
                <input type="text" name="prefix" id="prefix" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="suffix" class="form-label">后缀：</label>
                <input type="text" name="suffix" id="suffix" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">数量：</label>
                <input type="number" name="quantity" id="quantity" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="length" class="form-label">长度：</label>
                <input type="number" name="length" id="length" required class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">生成激活码</button>
        </form>

    <?php
    // 关闭数据库连接
    $conn->close();
    ?>
    
    </div>
</body>
</html>