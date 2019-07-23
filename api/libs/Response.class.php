<?php
namespace libs;


/*
	用来做各种响应, [code,messag,data]
*/
class Response {

	public static function setResponseHeader($code,$contentType){
		header("HTTP/1.1 $code ".self::getHttpStatusMessage($code));
		header("Content-Type: {$contentType}");
	}

	public static function restfulResponse($code=200,$message="",$data=[]){

		$contentType = request()->header("Accept") ?: "application/json";
		// 设置响应的header头
		self::setResponseHeader($code,$contentType);
		//
		switch($contentType){
			case "text/html":

			break;
			case "text/xml":
			case "application/xml":
				// $xml = new \SimpleXMLElement("students");
				// $xml->addChild("student");
				$xml = "<students></students>";
				$data = $xml;
			break;
			default:
			  $data = json_encode($data,JSON_UNESCAPED_UNICODE);
		}
		if($data){
			echo $data;
		}
		exit;
	}

	public static function returnData($code,$message,$data=[]){

		$dt = '';

		$format = isset($_GET['format']) ? trim($_GET['format']) : "json";

		switch($format){

			case "xml" :
				$dt = self::xml($code,$message,$data);
				break;
			case "array":
				$dt = self::genData($code,$message,$data);
				break;

			default:
				$dt = self::json($code,$message,$data);
		}

		if(is_array($dt)){
			var_dump($dt);
			exit;
		}
		echo $dt;
		exit;
	}

	// 生成通用的响应数组
	public static function genData($code,$message,$data=[]){

		return [

			"code" => $code,
			"message" => $message,
			"data" => $data
		];
	}

	// 响应json格式数据
	public static function json($code,$message,$data=[]){

		// 先得到通用数组
		$data = self::genData($code,$message,$data);
		//header("Content-Type: application/json; charset=utf-8");
		return json_encode($data,JSON_UNESCAPED_UNICODE);
	} 

	// 响应xml格式的数据
	public static function xml($code,$message,$data=[]){

		$data = self::genData($code,$message,$data);

		// 生成xml格式
		$xml = "<? xml version='1.0' encoding='utf8' ?>";
		$xml .= "<root>";
		$xml .= "<code>".$data['code']."</code>";
		$xml .= "<message>".$data['message']."</message>";
		$xml .= "<data>";

		$xml .= self::genXml($data['data']);

		$xml .= "</data></root>";
		return $xml;

	}

	// 根据data生成xml数据
	private static function genXml($data){

		/*
		<? xml version='1.0' encoding='utf8' ?>
		<root>
			<name>张三</name>
			<age>18</age>
			<hobby>漂亮的小姐姐</hobby>
		</root>
		*/
		$xml = "";
		foreach($data as $key=>$v){
			if(is_array($v)){
				$xml .= "<{$key}>";
				$xml .= self::genXml($v);
				$xml .= "</{$key}>";
			}else{
				$xml .= "<{$key}>{$v}</{$key}>";
			}
		}
		return $xml;
	}


	public static function getHttpStatusMessage($statusCode){
        $httpStatus = array(
            100 => 'Continue',  
            101 => 'Switching Protocols',  
            200 => 'OK',
            201 => 'Created',  
            202 => 'Accepted',  
            203 => 'Non-Authoritative Information',  
            204 => 'No Content',  
            205 => 'Reset Content',  
            206 => 'Partial Content',  
            300 => 'Multiple Choices',  
            301 => 'Moved Permanently',  
            302 => 'Found',  
            303 => 'See Other',  
            304 => 'Not Modified',  
            305 => 'Use Proxy',  
            306 => '(Unused)',  
            307 => 'Temporary Redirect',  
            400 => 'Bad Request',  
            401 => 'Unauthorized',  
            402 => 'Payment Required',  
            403 => 'Forbidden',  
            404 => 'Not Found',  
            405 => 'Method Not Allowed',  
            406 => 'Not Acceptable',  
            407 => 'Proxy Authentication Required',  
            408 => 'Request Timeout',  
            409 => 'Conflict',  
            410 => 'Gone',  
            411 => 'Length Required',  
            412 => 'Precondition Failed',  
            413 => 'Request Entity Too Large',  
            414 => 'Request-URI Too Long',  
            415 => 'Unsupported Media Type',  
            416 => 'Requested Range Not Satisfiable',  
            417 => 'Expectation Failed',  
            500 => 'Internal Server Error',  
            501 => 'Not Implemented',  
            502 => 'Bad Gateway',  
            503 => 'Service Unavailable',  
            504 => 'Gateway Timeout',  
            505 => 'HTTP Version Not Supported');
        return ($httpStatus[$statusCode]) ? $httpStatus[$statusCode] : $status[500];
    }

}