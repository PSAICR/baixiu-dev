<?php 

require_once 'config.php';

session_start();

/**
*获取用户信息，没有则跳转到登陆页面
*/
function bx_get_current_user() {
	if(empty($_SESSION['current_login_user'])){
	  // 未登录跳转到登录页
	  header('Location: /admin/login.php');
	  exit();
	}
	return $_SESSION['current_login_user'];
}

//连接数据库
//多条数据查询
function bx_fetch_all($sql) {
	$conn = mysqli_connect(BX_DB_HOST,BX_DB_USER,BX_DB_PASS,BX_DB_NAME);
	if(!$conn){
		exit('连接失败');
	}

	$conn->query("set names utf8");

	$query = mysqli_query($conn,$sql);
	if(!$query){
		return false;
	}

	while($row = mysqli_fetch_assoc($query)){
		$result[] = $row;
	}

	mysqli_free_result($query);
	mysqli_close($conn);

	return $result;
}

//单条数据查询
function bx_fetch_one($sql) {
	$res = bx_fetch_all($sql);
	return isset($res[0])?$res[0]:null;
}

// 数据库增删改操作
function bx_execute($sql) {
	$conn = mysqli_connect(BX_DB_HOST,BX_DB_USER,BX_DB_PASS,BX_DB_NAME);
	if(!$conn){
		exit('连接失败');
	}

	$conn->query("set names utf8");

	$query = mysqli_query($conn,$sql);
	if(!$query){
		return false;
	}

	// 对于增删修改类的操作都是获取受影响行数
  	$affected_rows = mysqli_affected_rows($conn);

	return $affected_rows;
}
