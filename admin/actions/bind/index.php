<?php
// 引入 config.php 文件
require_once('/www/wwwroot/whitelist.hln-network.xyz/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $qq = $_POST['qq'];
    $playerName = $_POST['playerName'];
    $activationCode = $_POST['activationCode'];

    function bind($qq, $playerName, $activationCode) {
        global $config;
        $uuid = null;

        // 请求MojangAPI获取玩家uuid
        $url = "https://api.mojang.com/users/profiles/minecraft/$playerName";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            if ($data && isset($data['id'])) {
                $uuid = $data['id'];
            } else {
                return '当前MC玩家名不存在';
            }
        } else {
            return '当前MC玩家名不存在';
        }

        // 创建数据库连接
        $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['database']);
        if ($conn->connect_error) {
            die("数据库连接失败: " . $conn->connect_error);
        }

        // 判断是否存在未过期的记录
        $existingRecord = null;
        $stmt = $conn->prepare("SELECT * FROM whitelist WHERE QQ=? OR playerName=?");
        $stmt->bind_param("ss", $qq, $uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // 检查记录是否未过期
                if (strtotime($row['expired_at']) > time()) {
                    $existingRecord = $row;
                    break;
                }
            }
        }

        if ($existingRecord) {
            // 存在未过期的记录，计算新的过期时间
            $existingExpiredTime = strtotime($existingRecord['expired_at']);
            $newExpiredTime = $existingExpiredTime + 60 * 60 * 24 * $activationCode; // 新的过期时间

            // 更新数据库中对应的记录的过期时间
            $stmt = $conn->prepare("UPDATE whitelist SET expired_at=? WHERE playerName=?");
            $stmt->bind_param("ii", $newExpiredTime, $existingRecord['playerName']);
            $stmt->execute();
            $stmt->close();
        } else {
            // 不存在未过期的记录，进行插入操作
            $duration = explode("-", $activationCode)[2];
            $expiredTime = time() + 60 * 60 * 24 * $activationCode;

            $stmt = $conn->prepare("INSERT INTO whitelist (QQ, playerName, expired_at) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $qq, $uuid, $expiredTime);

            if ($stmt->execute()) {
                // 关闭数据库连接
                $conn->close();

                // Cloudflare API请求
                $cf_api_key = '7bf50dd3c96c899dec5cc644eb719910da580';
                $cf_email = 'lang44774770@163.com';
                $cf_zone_id = '6a910bd82c8c3fb8a0e2e1a65f0ba607';

                $cname_subdomain = $uuid;
                $cname_domain = 'mcip.link';
                $cname_content = 'sh-iepl.hln-boost.eu.org'; // 修改为你的目标域名

                $url = "https://api.cloudflare.com/client/v4/zones/$cf_zone_id/dns_records";
                $data = array(
                    'type' => 'CNAME',
                    'name' => $cname_subdomain,
                    'content' => $cname_content,
                    'ttl' => 1,
                    'proxied' => false
                );
                $headers = array(
                    'X-Auth-Email: ' . $cf_email,
                    'X-Auth-Key: ' . $cf_api_key,
                    'Content-Type: application/json'
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);
                curl_close($ch);

                if ($response) {
                    $response_data = json_decode($response, true);
                    if (isset($response_data['success']) && $response_data['success']) {
                        // 记录成功创建
                        return "成功绑定玩家ID: $playerName 到 QQ: $qq, 你的加速IP：$cname_subdomain.$cname_domain:61005";
                    } else {
                        return "Cloudflare API请求不存在";
                    }
                } else {
                    return "Cloudflare API请求不存在";
                }
            } else {
                $conn->close();
                return "内部错误，绑定失败!";
            }
        }
    }

    // 进行绑定操作并输出结果
    $message = bind($qq, $playerName, $activationCode);
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>玩家绑定</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }
    </style>
    <script>
        function bindPlayer() {
            var form = document.getElementById("bindForm");
            form.submit(); // 提交表单
        }
    </script>
</head>
<body>
<div class="container">
    <h1 class="mt-4">玩家绑定</h1>
    <?php if(isset($message)): ?>
    <?php if(strpos($message, '不存在') !== false): ?>
        <div class="alert alert-danger" role="alert"><?php echo $message; ?></div>
    <?php else: ?>
        <div class="alert alert-primary" role="alert"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php endif; ?>
    <form id="bindForm" method="POST">
        <div class="form-group">
            <label for="qq">QQ：</label>
            <input type="text" class="form-control" name="qq" id="qq" pattern="[0-9]+" required>
        </div>

        <div class="form-group">
            <label for="playerName">玩家名：</label>
            <input type="text" class="form-control" name="playerName" id="playerName" required>
        </div>

        <div class="form-group">
            <label for="activationCode">天数：</label>
            <input type="text" class="form-control" name="activationCode" id="activationCode" required>
        </div>

        <div class="form-group">
            <input type="button" class="btn btn-primary" value="绑定" onclick="bindPlayer()">
        </div>

    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>