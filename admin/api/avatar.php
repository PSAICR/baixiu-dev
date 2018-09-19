<?php 

require_once '../../config.php';
/**
*接收传来的email，查询对应的图片地址，发送给客户端
*/

// 1.接收邮箱
if(empty($_GET['email'])){
	exit('缺少必要的参数');
}

// 2.根据邮箱查询img地址
$email = $_GET['email'];

$conn = mysqli_connect(BX_DB_HOST,BX_DB_USER,BX_DB_PASS,BX_DB_NAME);
if(!$conn){
	exit('数据库连接失败');
}

$res = mysqli_query($conn, "select avatar from users where email = '{$email}' limit 1;");
if(!$res){
	exit('查询失败');
}

$row = mysqli_fetch_assoc($res);

// 3.发送给客户端
echo $row['avatar'];

// 断开连接
mysqli_free_result($res);
mysqli_close($conn);

