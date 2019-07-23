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

		list($c,$a) = $this->getRoute();
		
		$c !== "" && $this->controller = $c;
		$a !== "" && $this->action = $a;

		// 返回控制器和方法
		$this->controller = ucfirst($this->controller)."Controller";
		$this->action = "action".ucfirst($this->action);

		return [$this->controller,$this->action];
	}

	protected function getRoute(){
		$controller = "";
		$action = "";
		// 直接通过地址中的参数c和a获取对应的控制器和方法
		list($controller,$action) = $this->getRouteByUrl();
		// 通过pathinfo来获取
		$controller || $action || list($controller,$action) = $this->getRouteByPathInfo();
		// 通过正则匹配来获取
		$controller || $action || list($controller,$action) = $this->getRouteByUri();

		// 通过通用规则来获取
		$controller || $action || list($controller,$action) = $this->getRouteByParams();

		return [$controller,$action];
	}

	protected function getRouteByUrl(){
		$c = request()->all('c',"");
		$a = request()->all('a',"");
		return [$c,$a];
	}

	protected function getRouteByPathInfo(){
		$controller = "";
		$action = "";
		$pathinfo = $_SERVER['PATH_INFO'] ?? "";
		// 如果确实存在
		if($pathinfo){
			$path = explode("/",$pathinfo);
			
			$controller = $path[1] ?? "";
			$action = $path[2] ?? "";

			for($i=3;$i<count($path);$i=$i+2){
				$_GET[$path[$i]] = $path[$i+1] ?? "";
			}
		}

		return [$controller,$action];
	}

	protected function getRouteByUri(){
		$controller = "";
		$action = "";
		$uri = $_SERVER['REQUEST_URI'];
		// 进行正则匹配
		$regs = $this->regExpForRoute();
		foreach($regs as $reg => $replace){
			if(preg_match($reg, $uri)){
				$newUri = preg_replace($reg, $replace, $uri);
				// 将字符串进行处理
				$params = explode("&", $newUri);
				foreach($params as $param){
					$p = explode("=", $param);
					if($p[0]=='c'){
						$controller = $p[1];
					}elseif($p[0] == 'a'){
						$action = $p[1];
					}else{
						$_GET[$p[0]] = $p[1];
					}
				}
				break;
			}
		}
		return [$controller,$action];
	}

	protected function getRouteByParams(){
		$controller = "";
		$action = "";
		$uri = $_SERVER['REQUEST_URI'];
		
		$uri = explode("?", $uri);
		// 处理?后半部分
		if(isset($uri[1])){
			$params = explode("&",$uri[1]);
			foreach($params as $v){
				$v = explode('=', $v);
				$_GET[$v[0]] = $v[1];
			}
		}
		// ?前半部分
		$path = explode('/', $uri[0]);
		$controller = $path[1] ?? "";
		$action = $path[2] ?? "";
		for($i=3;$i<count($path);$i+=2){
			$_GET[$path[$i]] = $path[$i+1] ?? "";
		}

		return [$controller,$action];
	}
	protected function regExpForRoute(){

		return [
			"#^/(\w+)/(\d+)\?(.*)$#"=>"c=$1&a=index&id=$2&$3",
			"#^/(\w+)/(\d+)$#" => "c=$1&a=index&id=$2",
			"#^/(\w+)/(\w+)\?(.*)$#" => "c=$1&a=$2&$3",
			"#^/(\w+)/(\w+)$#" => "c=$1&a=$2",
			"#^/(\w+)$#"=>"c=$1&a=index",
			"#^/(\w+)\?(.*)$#"=>"c=$1&a=index&$2",
		];
	}
}