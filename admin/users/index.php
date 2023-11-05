<?php
require_once ('/www/wwwroot/whitelist.hln-network.xyz/config.php');

// 创建数据库连接
global $config;
$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['database']);
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

// 获取白名单中的所有玩家名称、QQ和过期时间戳
$query = "SELECT playerName, QQ, expired_at FROM whitelist";
$result = $conn->query($query);

// 构建玩家名称、QQ和过期时间的字典
$player_qq_dict = array();
while ($row = $result->fetch_assoc()) {
    $playerName = $row['playerName'];
    $qq = $row['QQ'];
    $expiredTimestamp = $row['expired_at'];
    if (!isset($player_qq_dict[$playerName])) {
        $player_qq_dict[$playerName] = array();
    }
    $player_qq_dict[$playerName][] = array('qq' => $qq, 'expired_at' => $expiredTimestamp);
}

// 关闭数据库连接
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <!-- 引入Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>User List</h1>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>QQ</th>
                    <th>Player Name</th>
                    <th>Expired Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($player_qq_dict as $playerName => $dataList) : ?>
                    <?php foreach ($dataList as $data) : ?>
                        <?php $qq = $data['qq']; ?>
                        <?php $expiredTimestamp = $data['expired_at']; ?>
                        <?php $expiredTime = date('Y-m-d H:i:s', $expiredTimestamp); ?>
                        <tr>
                            <td><?php echo $qq; ?></td>
                            <td><?php echo $playerName; ?></td>
                            <td><?php echo $expiredTime; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- 引入Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>