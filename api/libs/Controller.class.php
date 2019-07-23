<?php
namespace libs;


class Controller {

	protected $key = '';

	protected $noncestr = '';
	protected $timestamp = '';
	protected $signature = '';

	// 身份认证 + 数据安全保护  = 签名

	public function __construct(){

		$this->key = config('signature.key');

		$this->checkSign(); 

	} 

	// get签名认证
	protected function checkSign(){
		// 获取随机数
		$noncestr = request()->all('noncestr');
		// 获取时间戳
		$timestamp = request()->all('timestamp');
		// 获取客户端传递过来的签名
		$signature = request()->all('sign');


		$noncestr && $this->noncestr = $noncestr;
		$timestamp && $this->timestamp = $timestamp;
		$signature && $this->signature = $signature;

		if(!$this->noncestr || !$this->timestamp || !$this->signature) {
			Response::returnData(400,'Bad Request');
		}

		// 判断传递过来的时间戳是否符合要求
		if($this->timestamp + 60 < time()){
			Response::returnData(1009,'EXPIRE Signature');
		}
		
		$this->compareSign();

	}


	protected function compareSign(){

		// 获取所有的参数
		$all = request()->except(['c','a','sign','access_token']);
		// 生成一个数组
		$signArr = ["timestamp"=>$this->timestamp, "noncestr"=>$this->noncestr,"key"=>$this->key];
		$signArr = $signArr + $all;
		// 进行字典排序
		sort($signArr, SORT_STRING);
		$signature = sha1(implode( $signArr ));

		if ($this->signature !== $signature ){
			Response::returnData(1001,'Signature UnAuthorization');
		}
	}




}