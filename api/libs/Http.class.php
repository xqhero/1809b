<?php
namespace libs;


/*
	封装CURL实现各种请求调用
*/
class Http {


	// post请求
	public static function postHttp($url="",$data=[],$multipart=false,$headers=[]){
		if(!$multipart){
			$data = http_build_query($data);
		}
		$options = [
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $data
		];
		if($headers){
			$options[CURLOPT_HTTPHEADER] = $headers;
		}
		
		if(self::isHttps($url)){
			$options[CURLOPT_SSL_VERIFYPEER] = false; // 是否验证对等的两端
			$options[CURLOPT_SSL_VERIFYHOST] = false; // 是否验证主机]]
		}

		return self::doHttp($options);
	}


	// get请求
	public static function getHttp($url="",$headers=[]){

		$options = [
			CURLOPT_URL => $url
		];
		if(self::isHttps($url)){
			$options[CURLOPT_SSL_VERIFYPEER] = false; // 是否验证对等的两端
			$options[CURLOPT_SSL_VERIFYHOST] = false; // 是否验证主机]]
		}
		if($headers){
			$options[CURLOPT_HTTPHEADER] = $headers;
		}

		return self::doHttp($options);
	} 

	// 通过url判断是否为https请求
	public static function isHttps($url){
		// 判断https
		if(strpos($url,"https://") == 0 ){
			return true;
		} 
		return false;
	}

    // 基本的curl请求
	public static function doHttp($options=[]){

		// 设置默认的参数
		$option = [
				CURLOPT_FOLLOWLOCATION => true, // 如果设置为false，则不会进行301跳转，设置true，则返回最终跳转后的内容
				CURLOPT_RETURNTRANSFER => true, // 接口返回的数据是直接输出还是返回给变量
		];

		$option = $option + $options;

		// 合并数组 array_merge 只能合并关联数组 并且后面的会替代前面的
		//          +  可以合并索引以及关联数组  并且如果key重复则保留前面的值
		// 1. 初始化
		$ch = curl_init();

		// 2. 设置参数

		curl_setopt_array($ch, $options);

		// 3. 执行curl
		$result = curl_exec($ch);
		if(!$result){
			$result = curl_error($ch);
		}

		curl_close($ch);
		return $result;
	}




}