<?php
require_once('/www/wwwroot/whitelist.hln-network.xyz/config.php');

// 引入 Cloudflare API 相关的信息
$cf_api_key = '7bf50dd3c96c899dec5cc644eb719910da580';
$cf_email = 'lang44774770@163.com';
$cf_zone_id = '6a910bd82c8c3fb8a0e2e1a65f0ba607';

// 连接数据库
$conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['database']);
if (!$conn) {
    die("连接数据库失败：" . mysqli_connect_error());
}

// 获取过期记录的信息
$expired_records = array();
$sql = "SELECT playerName FROM whitelist WHERE expired_at <= " . time();
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $expired_records[] = $row['playerName'];
    }
}

// 删除过期记录
$sql = "DELETE FROM whitelist WHERE expired_at <= " . time();
if (mysqli_query($conn, $sql)) {
    echo "True";
} else {
    echo "False：" . mysqli_error($conn);
}

// 关闭数据库连接
mysqli_close($conn);

// 删除相应的 Cloudflare CNAME 记录
if (!empty($expired_records)) {
    $headers = array(
        'X-Auth-Email: ' . $cf_email,
        'X-Auth-Key: ' . $cf_api_key,
        'Content-Type: application/json'
    );

    foreach ($expired_records as $record_name) {
        $url = "https://api.cloudflare.com/client/v4/zones/$cf_zone_id/dns_records?type=CNAME&name=$record_name.mcip.link";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if (isset($response_data['success']) && $response_data['success']) {
            // 记录成功删除
            echo "Cloudflare CNAME记录 $record_name.mcip.link 已删除";
        } else {
            echo "Cloudflare API请求失败：" . $response_data['errors'][0]['message'];
        }
    }
}
?>
