<?php
// 模拟数据库中的用户数据
$users = [
  ['username' => 'MKyiwuQwQ', 'password' => '201031'],
  ['username' => 'Moxxuan', 'password' => '123456']
];

function isLoggedIn() {
  return isset($_COOKIE['username']);
}

// 处理登录请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 获取POST请求中的用户名和密码
  $username = $_POST['username'];
  $password = $_POST['password'];

  // 在数据库中查找匹配的用户
  $user = null;
  foreach ($users as $u) {
    if ($u['username'] === $username && $u['password'] === $password) {
      $user = $u;
      break;
    }
  }

  if ($user) {
    // 登录成功，设置Cookie
    setcookie('username', $user['username'], time() + (86400 * 30)); // 设置有效期为30天
    header('Location: /admin/KZP5hUX7XB'); // 重定向到主页或其他受限页面
    exit();
  } else {
    // 登录失败
    $message = '用户名或密码错误';
  }
}

// 检查登录状态
if (isLoggedIn()) {
  // 用户已登录，重定向到主页或其他受限页面
  header('Location: /admin/KZP5hUX7XB'); // 重定向到主页或其他受限页面
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>登录</title>

  <!-- 引入Bootstrap CSS -->
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
</head>
<body>
<div class="container mt-5">
  <h1>登录</h1>

  <?php if(isset($message)): ?>
    <div class="alert alert-danger" role="alert"><?php echo $message; ?></div>
  <?php endif; ?>

  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="form-group">
      <label for="username">用户名:</label>
      <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="form-group">
      <label for="password">密码:</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">登录</button>
  </form>
</div>

<!-- 引入Bootstrap JS（需要在body结束标签前引入） -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>