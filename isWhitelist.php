<?php
require_once ('/www/wwwroot/whitelist.hln-network.xyz/config.php');

$playerName = isset($_GET['playerName']) ? $_GET['playerName'] : '';

function echoWhitelist($playerName){
    global $config;
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['database']);
    if ($conn->connect_error) {
        die("连接数据库失败：" . $conn->connect_error);
    }

    // 查询UUID
    $uuid = getUUID($playerName);

    $stmt = $conn->prepare("SELECT playerName FROM whitelist WHERE playerName = ?");
    if (!$stmt) {
        die("预处理失败：" . $conn->error);
    }
    $stmt->bind_param("s", $uuid);
    if (!$stmt->execute()) {
        die("执行查询失败：" . $stmt->error);
    }
    $stmt->store_result();
    $totalRows = $stmt->num_rows;
    $stmt->close();
    $conn->close();

    return $totalRows > 0 ? $playerName : false;
}

// 获取UUID函数
function getUUID($playerName) {
    $url = "https://api.mojang.com/users/profiles/minecraft/" . $playerName;
    $json = file_get_contents($url);
    $data = json_decode($json, true);
    if (isset($data['id'])) {
        return $data['id'];
    } else {
        return null;
    }
}

$result = echoWhitelist($playerName);
if($result) {
    echo $result;
}
?>