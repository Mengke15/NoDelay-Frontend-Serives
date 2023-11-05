<?php
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
    <title>Activation Codes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Activation Codes</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Activation Code</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="activationCodesTable">
                <!-- Activation code rows will be inserted here -->
            </tbody>
        </table>
    </div>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        // 使用 Axios 获取激活码数据
        axios.get('find.php')
            .then(function (response) {
                var activationCodes = response.data;

                // 将激活码数据插入表格中
                var tableBody = document.getElementById('activationCodesTable');
                activationCodes.forEach(function (code) {
                    var row = document.createElement('tr');
                    var codeCell = document.createElement('td');
                    var statusCell = document.createElement('td');

                    codeCell.textContent = code.activation_code;
                    statusCell.textContent = code.status;

                    row.appendChild(codeCell);
                    row.appendChild(statusCell);

                    tableBody.appendChild(row);
                });
            })
            .catch(function (error) {
                console.log(error);
            });
    </script>
</body>
</html>