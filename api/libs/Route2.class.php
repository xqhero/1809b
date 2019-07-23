<?php
namespace libs;

// 路由类
class Route {

	private $controller = "index" ;

	private $action = "index";


	// 解析路由
	// c 表示控制
	// a 表示方法名
	// 控制器名字后缀 Controller   index=》 IndexController 
	// 方法的名字前缀 action       index =》 actionIndex
	public function routeParse(){

		// var_dump($this->getRoute());
		// exit;
		list($c,$a) = $this->getRoute();
		$c !== "" && $this->controller = $c;
		$a !== "" && $this->action = $a;

		// 返回控制器和方法
		$this->controller = ucfirst($this->controller)."Controller";
		$this->action = "action".ucfirst($this->action);

		//var_dump($this->controller,$this->action,$_GET);
		return [$this->controller,$this->action];
	}

	protected function getRoute(){
		// 先获取地址栏中的值
		$c = request()->get('c');
		$a = request()->get('a'); 
		// 如果没有则在获取pathinfo的值
		$c || $a || list($c,$a) = $this->getPathinfo();
		$c || $a || list($c,$a) = $this->getRegUri();
		$c || $a || list($c,$a) = $this->getUri();
		return [$c,$a];
	}

	protected function getPathinfo(){

		// 先判断有没有pathinfo
		// queryString
		$pathinfo = $_SERVER['PATH_INFO'] ?? "";
		$controller = '';
		$action = '';
		if($pathinfo){
			// 得到处理参数
			$arr = explode("/", $pathinfo);
			//var_dump($arr);
			// "/index/index?c=a&b=h"
			$controller = $arr[1] ?? "";
			$action = $arr[2] ?? "";
			// 设置参数
			for($i=3;$i<count($arr);$i=$i+2){
				$_GET[$arr[$i]] = $arr[$i+1] ?? "";
			}
		}
		return [$controller,$action];
	}  

	protected function getRegUri(){

		$uri = $_SERVER['REQUEST_URI'];
		$controller = "";
		$action = "";
		// 先走正则
		$regexps = $this->getRegExp();
		foreach($regexps as $regexp=>$v){
			if(preg_match($regexp, $uri, $matches)){
				// 匹配到规则则得到controller 和 action
				
				$url = preg_replace($regexp, $v, $uri);
				
				// 进行处理
				list($controller,$action) = $this->paramFromQuery($url);
				break;
			}
		}
		return [$controller,$action];
	}

	protected function getUri(){

		$uri = $_SERVER['REQUEST_URI'];
		$controller = "";
		$action = "";
		// 普通方式
		$arr = explode("?", $uri);
		// 得到前半部分
		$path = explode("/", $arr[0]);
		$controller = $path[1] ?? "";
		$action = $path[2] ?? "";
		// 设置参数
		for($i=3;$i<count($path);$i=$i+2){
			$_GET[$path[$i]] = $path[$i+1] ?? "";
		}
		// 处理后面胡参数
		if(isset($arr[1])){
			$params = explode("&",$arr[1]);
			foreach($params as $param){
				$parameters = explode("=", $param);
				$_GET[$parameters[0]] = $parameters[1];
			}
		}
		return [$controller,$action];
	}

	protected function paramFromQuery($url){

		$url = explode("&", $url);
		$controller = '';
		$action = '';
		foreach($url as $v){
			$v = explode('=', $v);
			if($v[0] == 'c'){
				$controller = $v[1];
			}elseif($v[0] == 'a'){
				$action = $v[1];
			}else{
				$_GET[$v[0]] = $v[1];
			}
		}
		return [$controller,$action];
	}

	protected function getRegExp(){

		return [
			"#^/(\w+)/(\w+)\?(.*)$#"=>"c=$1&a=$2&$3",
			"#^/(\w+)/(\d+)\?(.*)$#"=>"c=$1&a=index&id=$2&$3",
			"#^/(\w+)/(\d+)$#"=>"c=$1&a=index&id=$2",
			"#^/(\w+)/(\w+)$#"=>"c=$1&a=$2",
			"#^/(\w+)$#"=>"c=$1",
		];
	}

}