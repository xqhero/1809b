<?php

// 返回数据即可，无需关注页面的内容
// json xml 
// ["code"=>200,"message"=>"login success","data"=>[]]

// header("Location: https://www.baidu.com");
// exit;

// 判断报文头中是否包含指定的项
if(!isset($_SERVER['HTTP_X_API_TOKEN'])  ||  $_SERVER['HTTP_X_API_TOKEN'] != "1810b") {
	exit (json_encode(['error'=>"拒绝访问"]));
}

$response = [
	
	"code" => 200,
	"message" => 'ok',
	"data" => []
];

// exit(json_encode($response));
// 1. 获取值
$username = $_POST['username'];
$password = $_POST['password'];


// 2. 查表
$link = mysqli_connect("127.0.0.1","root","123456");
mysqli_select_db($link,"students");
mysqli_set_charset($link,"utf8");

$sql = "select * from user where username = '".$username."'";
$res = mysqli_query($link,$sql);
if($res){
	$row = mysqli_fetch_assoc($res);
	// 
	if($password == $row['password']){
		// 成功
		unset($row['password']);
		$response['data'] = $row;
	}else{
		// 弹出错误信息
		// 跳转回登录页面
		$response['code'] = -1;
		$response['message'] = "failed";
	}
}

echo json_encode($response);

