<?php
// 专门用于得到客户端传递过来的数据
namespace libs;

class Request{

	protected $headers = [];

	public function __construct(){
		// 获取header头
		$this->getHeaders();
	}

	protected function getHeaders(){
		$headers = [];
		// 如果有content-type 或者content-length
		isset($_SERVER['CONTENT_TYPE']) && $headers['Content-Type']= $_SERVER['CONTENT_TYPE'];
		isset($_SERVER['CONTENT_LENGTH']) && $headers['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
		// 其他的报文头
		foreach($_SERVER as $key=>$v){
			if(strpos($key,"HTTP_") === 0){
				$k = $this->dealHeader($key);
				if($k == "Authorization"){
					$v = str_replace("Bearer ", "", $v);
				}
				$headers[$k] = $v;
			}
		}
		$this->headers = $headers;
	}
	// 处理header头
	protected function dealHeader($key){
		$k = str_replace("HTTP_", "", $key);
		$k = explode("_", $k);
		$k = array_map(function($v){
			return ucfirst(strtolower($v));
		}, $k);
		$k = implode("-", $k);
		return $k;
	}
	// 获取所有元素
	public function all($name = "",$default = ''){

		parse_str(file_get_contents("php://input"),$data);

		$request =  $_POST + $_GET + $data;

		if($name){
			return $request[$name] ?? $default;
		}
		return $request;
	}

	// 返回除某些元素外的其他所有元素
	public function except($expect = []){
		$data = $this->all(); 
		if(is_array($expect)){
			foreach($expect as $v){
				unset($data[$v]);
			}
		}else if($expect!=""){
			unset($data[$expect]);
		}
		return $data;
	}

	public function get($name='',$default=''){

		if($name){
			return $_GET[$name] ?? $default;	
		}
		return $_GET;
	}

	public function post($name='',$default=''){

		if($name){
			return $_POST[$name] ?? $default;
		}
		return $_POST;
	}

	public function header($name=""){
		if($name){
			return $this->headers[$name] ?? null;
		}
		return $this->headers;
	}

}