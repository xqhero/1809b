<?php


function demo(){

	echo __FUNCTION__;
}


function createSign($data = []){

	// 生成随机数
	$randstr = createRandString();
	// 生成时间戳
	$timestamp = time();
	// key
	$key = "1810b";
	// 
	$signArr = ["timestamp"=>$timestamp,"noncestr"=>$randstr,'key'=>$key];
	$signArr = $signArr + $data;
	sort($signArr,SORT_STRING);
	$sign = sha1(implode( $signArr ));
	return [$randstr,$timestamp,$sign];
}


function createRandString($len=20){

	$randstr = "";
	$string = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$";
	for($i=0;$i<$len;$i++){

		// 生成随机数
		$index = rand(0,strlen($string)-1);
		$randstr .= $string[$index];
	}

	return $randstr;
}