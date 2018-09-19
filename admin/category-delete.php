<?php

/**
 * 根据客户端传递过来的ID删除对应数据
 */

require_once '../functions.php';

if(empty($_GET['id'])){
	exit('数据错误');
}

// 类型转换，避免被sql注入
// (int)只能做单条删除的初级放注入，建议使用逻辑判断；
// $id = (int)$_GET['id'];
$id = $_GET['id'];

bx_execute('delete from categories where id in (' . $id . ');');

header('location: /admin/categories.php');