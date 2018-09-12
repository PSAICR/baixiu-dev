<?php 

// 载入配置文件
require_once '../config.php';

session_start();

function login(){
  // 1.接收并校验
  // 2.持久化
  // 3.响应

  if(empty($_POST['email'])){
    $GLOBALS['err_message'] = '请填写邮箱';
    return;
  }
  if(empty($_POST['password'])){
    $GLOBALS['err_message'] = '请填写密码';
    return;
  }

  $email = $_POST['email'];
  $password = $_POST['password'];

  // 连接数据库校验
  $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if(!$conn){
    exit('<h1>数据连接失败</h1>');
  }

  $query = mysqli_query($conn,"select * from users where email ='{$email}' limit 1");
  if(!$query){
    $GLOBALS['err_message'] = '登陆失败，请重试';
    return;
  }

  $user = mysqli_fetch_assoc($query);
  if(!$user){
    // 用户名不存在
    $GLOBALS['err_message'] = '邮箱与密码不匹配';
    return;
  }

  if(md5($password) !== $user['password']){
    // 密码错误
    $GLOBALS['err_message'] = '邮箱与密码不匹配';
    return;
  }

  // 账号密码正确，存一个登陆标识
  $_SESSION['current_login_user'] = $user;

  // if($email !== 'admin@qq.com'){
  //   $GLOBALS['err_message'] = '邮箱与密码不匹配';
  //   return;
  // }
  // if($password !== '123456'){
  //   $GLOBALS['err_message'] = '邮箱与密码不匹配';
  //   return;
  // }

  // 检验成功，进行跳转
  header('location: /admin/');

}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  login();
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off" novalidate>
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if(isset($err_message)):?>
      <div class="alert alert-danger">
      <strong>错误！</strong> <?php echo $err_message; ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱"<?php echo empty($_POST['email']) ? ' autofocus' :  ' value='.$_POST['email'] ?>>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码"<?php echo empty($_POST['email']) ? '' :  ' autofocus' ?>>
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
</body>
</html>
