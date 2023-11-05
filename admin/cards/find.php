<?php
require_once ('/www/wwwroot/whitelist.hln-network.xyz/config.php');

// 创建数据库连接
global $config;
$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['database']);
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    // 查询所有激活码和状态
    $result = $conn->query("SELECT * FROM activation_codes");
    $activationCodes = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $activationCodes[] = array(
                "activation_code" => $row["activation_code"],
                "status" => ($row["status"] == 1 ? "已使用" : "未使用")
            );
        }
    }

    // 返回 JSON 数据
    header('Content-Type: application/json');
    echo json_encode($activationCodes);
}

// 关闭数据库连接
$conn->close();
?>