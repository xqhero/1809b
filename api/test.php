<?php




// 测试登录接口

// http://www.1810b.com/api/login.php

// username password

// 使用CURL来请求接口

// 1. curl 初始化
$ch = curl_init();
// 2. 设置参数
// curl_setopt($ch,CURLOPT_URL, "http://www.1810b.com/api/login.php");
// curl_setopt($ch,CURLOPT_POST,true);
// curl_setopt($ch,CURLOPT_POSTFIELDS,"username=zhangsan&password=1234");

// curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
// curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

curl_setopt_array($ch, [
	CURLOPT_URL => "http://www.1810b.com/api/login.php",
	// 请求地址
	CURLOPT_POST => false,
	// 是否为post请求，为false那就是get请求
	CURLOPT_POSTFIELDS => "username=zhangsan&password=123",
	// post传递的值，可以是数组，可以是urdencode的字符串
	CURLOPT_FOLLOWLOCATION => true, // 如果设置为false，则不会进行301跳转，设置true，则返回最终跳转后的内容
	CURLOPT_RETURNTRANSFER => true, // 接口返回的数据是直接输出还是返回给变量

	// https请求，
	CURLOPT_SSL_VERIFYPEER => false, // 是否验证对等的两端
	CURLOPT_SSL_VERIFYHOST => false  // 是否验证主机

	// 设置header
	CURLOPT_HTTPHEADER => [
		"x-api-token: 1810a",
		"Authorization: token",
	],
]);
// 3. 执行


$res = curl_exec($ch);

// 可能会发生错误
if(!$res){
	echo curl_error($ch);
	exit;
}

// 4. 关闭
curl_close($ch);

// var_dump(json_decode($res,true));
var_dump($res);