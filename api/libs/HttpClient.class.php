<?php
namespace libs;

class HttpClient {


	// 第一种使用fopen实现http的get post请求
	public static function fopenHttp($url,$data=""){
		$opts = [];
		if(is_array($data) && !empty($data)){
			// 进行post请求
			$data = http_build_query($data);
			$opts = array(
				  'http'=>array(
				    'method'=>"POST",
				    'header'=>"Content-Type: application/x-www-form-urlencoded\r\n".
				    	"Content-Length: ".strlen($data)."\r\n",
				    'content'=> $data
				  )
			);
		}
		$context = stream_context_create($opts);
		$fp = fopen($url,'r',false,$context);
		// 读取结果
		$content = stream_get_contents($fp);
		return $content;
	}

	// 第二种使用file实现http请求
	public static function fileHttp($url,$data=""){

		$opts = [];
		if(is_array($data) && !empty($data)){
			// 进行post请求
			$data = http_build_query($data);
			$opts = array(
				  'http'=>array(
				    'method'=>"POST",
				    'header'=>"Content-Type: application/x-www-form-urlencoded\r\n".
				    	"Content-Length: ".strlen($data)."\r\n",
				    'content'=> $data
				  )
			);
		}
		$context = stream_context_create($opts);
		$content = file($url,0,$context);
		// 读取结果
		$content = implode("\r\n",$content);
		return $content;


	}

	// 第三种使用file_get_contents实现http请求
	public static function fileGetContentsHttp($url,$data=""){

		$opts = [];
		if(is_array($data) && !empty($data)){
			// 进行post请求
			$data = http_build_query($data);
			$opts = array(
				  'http'=>array(
				    'method'=>"POST",
				    'header'=>"Content-Type: application/x-www-form-urlencoded\r\n".
				    	"Content-Length: ".strlen($data)."\r\n",
				    'content'=> $data
				  )
			);
		}
		$context = stream_context_create($opts);
		$content = file_get_contents($url,false,$context);
		return $content;
	}

	// 第四种方法 fsockopen
	public static function fsockopenHttp($url,$data="",$multipart=false,$port="80"){
		$method = 'GET';
		$parameter = parse_url($url);
		$path = $parameter['path'] ?? '/';
		if(isset($parameter['query'])){
			$path .= "?".$parameter['query'];
		}
		// 打开连接
		$fs = fsockopen($parameter['host'],$port,$errno,$errstr,30);
		if(!$fs){
			// 抛出错误信息
		}
		// 编写HTTP报文
		$httpStr = "GET ".$path." HTTP/1.1\r\n";
		if(!empty($data)){
			$method = "POST";
			$httpStr = "POST ".$parameter['path'].'?'.$parameter['query']." HTTP/1.1\r\n";
			// 进行post请求
			$multipart || ($data = is_array($data) ? http_build_query($data) : $data);

			$httpStr .= "Content-Length: ".strlen($data)."\r\n";
			
			$httpStr .=  $multipart ? "Content-Type: multipart/form-data; boundary=--ABC\r\n" : "Content-Type: application/x-www-form-urlencoded\r\n";
			
		}
		
		$httpStr.= "Host: ".$parameter['host']."\r\n";
		$httpStr.= "Accept: */*\r\n";
		$httpStr.= "\r\n";

		if($method == "POST"){
			$httpStr .= $data;
		}
		// 发送HTTP报文
		fwrite($fs,$httpStr);

		// 接收响应
		$contents = stream_get_contents($fs);
		//var_dump($contents);
		//return;
		return self::parseHttpResponsePackage($contents);
		//return self::getMessage(htmlspecialchars($contents));
	}


	public static function streamHttp($url,$data="",$port="80"){
		$method = 'GET';
		$parameter = parse_url($url);
		$path = $parameter['path'] ?? '/';
		if(isset($parameter['query'])){
			$path .= "?".$parameter['query'];
		}
		// 打开连接
		$socket = stream_socket_client("tcp://".$parameter['host'].":".$port,$errno,$errstr,30);
		if(!$socket){
			// 抛出错误信息
		}
		// 编写HTTP报文
		$httpStr = "GET ".$path." HTTP/1.1\r\n";
		if(is_array($data) && !empty($data)){
			$method = "POST";
			$httpStr = "POST ".$parameter['path'].'?'.$parameter['query']." HTTP/1.1\r\n";
			// 进行post请求
			$data = http_build_query($data);
			$httpStr .= "Content-Length: ".strlen($data)."\r\n";
			$httpStr .= "Content-Type: application/x-www-form-urlencoded\r\n";
		}
		
		$httpStr.= "Host: ".$parameter['host']."\r\n";
		$httpStr.= "Accept: */*\r\n";
		$httpStr.= "\r\n";

		if($method == "POST"){
			$httpStr .= $data;
		}
		// 发送HTTP报文
		fwrite($socket,$httpStr);

		// 接收响应
		$contents = stream_get_contents($socket);
		return self::parseHttpResponsePackage($contents);

	}

	protected static function parseHttpResponsePackage($contents=""){

		// 按照空行进行分隔得到报文头以及报文实体
		list($http_header,$http_body) = explode("\r\n\r\n",$contents);
		// 得到起始行
		$http_header = explode("\r\n", $http_header);

		list($schema,$code,$codeInfo) = explode(" ", $http_header[0]);
		unset($http_header[0]);
		
		$headers = [];
		foreach($http_header as $v){
			list($key,$value)=explode(": ", $v);
			$headers[$key] = $value;
		}
		//var_dump($headers);
		// 得到内容
		$body = "";
		if(isset($headers['Transfer-Encoding'])) {
			while($http_body){
				// 进行分割
				$httpBody = explode("\r\n", $http_body,2);
				$chunkedSize = intval($httpBody[0],16);
				$body .= substr($httpBody[1],0,$chunkedSize);
				$http_body = substr($httpBody[1],$chunkedSize+2);
			}
		}else{

			$body = $http_body;
		}
		// 返回响应头和内容数组
		return ["status"=>[$schema,$code,$codeInfo],"header"=>$headers,"body"=>$body];
	}
}