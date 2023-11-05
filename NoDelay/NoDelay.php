<?php
// 检查访问 IP 是否在配置文件内的 API
function checkIPInConfig($ip) {
  $config = file_get_contents('IP.json'); // 读取配置文件内容
  $configData = json_decode($config, true); // 解析 JSON 数据为关联数组

  if (is_array($configData) && array_key_exists('allowed_ips', $configData)) {
    $allowedIps = $configData['allowed_ips'];
    if (in_array($ip, $allowedIps)) {
      return true; // IP 在配置文件内
    }
  }

  return false; // IP 不在配置文件内
}

// 获取访问 IP 地址
$ip = $_SERVER['REMOTE_ADDR'];

if (checkIPInConfig($ip)) {
  // IP 在配置文件内的逻辑代码
  echo "true";
} else {
  // IP 不在配置文件内的逻辑代码
  echo "false";
}
?>