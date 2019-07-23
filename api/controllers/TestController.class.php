<?php
namespace controllers;

use libs\{ HttpClient , Response , Upload, Http ,Config};

use Qiniu\Storage\UploadManager;
use Qiniu\Auth;

class TestController {


	public function actionConfig(){

		// $res = explode(".", "username.");
		// var_dump($res);
		// exit;
		$config = Config::getInstance();
		$cfg = $config->getConfig();
		var_dump($cfg);
		$config->setConfig('test',null);
		var_dump($config->getConfig());

	}
	public function actionIndex(){

		var_dump($_SERVER);
		exit;
		// $data = [
		// 	"username"=>"zhangsan",
		// 	"password"=>"123456"
		// ];
		//var_dump(createSign());
		//var_dump($this->createPassword("123456"));

		$str = "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOlsiYXVkaWVuY2VfMSIsImF1ZGllbmNlXzIiXSwiZXhwIjoxNTYwMTUwOTI5LCJpYXQiOjE1NjAxNDkxMjksImlzcyI6Inpob3VndW9xaWFuZyIsImp0aSI6IjEiLCJuYmYiOjE1NjAxNDkxMjksInN1YiI6Imh0dHA6XC9cL3d3dy5hcGl0ZXN0LmNvbSJ9.XWyBxK4m7xmTkyjohBRd71e3S5yJ7TH9Qq3I6rTWhMk";
		$arr = explode('.', $str);

		var_dump(base64_decode($arr[0]));
		var_dump(base64_decode($arr[1]));

	}

	public function createPassword($password){

		return password_hash($password,PASSWORD_DEFAULT);

	}	

	public function actionHttp(){

		// 调用fopenHttp()
		//$res = HttpClient::fsockopenHttp("http://www.apitest.com/index.php?c=test&a=post",["username"=>'zhansan',"password"=>"123456"]);
		$res = HttpClient::streamHttp("http://www.apitest.com/index.php?c=test&a=post",['username'=>'小姐姐','password'=>'小哥哥，我要...']);
		// $res = HttpClient::streamHttp("http://www.apitest.com/index.php?c=test&a=post");
		if(substr($res['status'][1],0,1) == '2'){
			echo $res['body'];
		}else{
			echo $res['status'][1],":",$res['status'][2];
		}
	}

	public function actionPost(){
		if($_SERVER['REQUEST_METHOD'] == "POST"){
			// var_dump($_FILES);
			// exit;
			$upload = new Upload();
			$upload->setConfig("ext",['jpg','jpeg','png']);
			$upload->setConfig("mimeType",['image/jpeg','image/png','image/gif']);
			$res = $upload->uploadAll();
			if(!$res){
				echo  $upload->getError();
				exit;
			}
			var_dump($res);
		}
		include "../views/post.html";
		// $str = json_encode(["username"=>"zhangsan","password"=>123456]);
		// //header("Content-Type: application/json");
		// header("Content-Length: ".strlen($str));
		// // Response::returnData(200,"ok");
		// echo $str;
	}


	public function actionTest(){
		// 使用stream_socket_client建立tcp连接
		$fp = stream_socket_client("tcp://www.apitest.com:80", $errno, $errstr, 30);
		fwrite($fp, "GET /index.php?c=test&a=post HTTP/1.1\r\nHost: www.apitest.com\r\nAccept: */*\r\n\r\n");

		$content = stream_get_contents($fp);
		var_dump($content);
	}

	// 测试上传接口1 直接从服务器读取
	public function actionUpload(){
		//1. 使用curl测试 
		// 使用CURLFile创建一个文件
		$cfile = new \CURLFile('./33226.jpg','image/jpeg','33226.jpg');
		$data['file'] = $cfile;
		$res = Http::postHttp("http://www.apitest.com/index.php?c=upload&a=upload",$data,true); 
		var_dump($res);
	}


	// 从客户端读取
	public function actionUpload2(){
		if($_SERVER['REQUEST_METHOD'] == "POST"){

			$file = $_FILES['file'];
			// 
			//将客户上传的文件提交给我们指定上传接口，比如上传到七牛、阿里云

				$cfile = new \CURLFile($file['tmp_name'],$file['type'],$file['name']);
				$data['file'] = $cfile;
				$res = Http::postHttp("http://www.apitest.com/index.php?c=upload&a=upload",$data,true); 
				var_dump($res);
				exit;

			}
			include "../views/post.html";
	}


	public function actionUpload3(){

		$data = "----ABC\r\n";
		$data .= "Content-Disposition: form-data; name=\"username\"\r\n\r\n";
		$data .= "zhouguoqiang\r\n";

		$data .= "----ABC\r\n";
		$data .= "Content-Disposition: form-data; name=\"file\"; filename=\"33226.jpg\"\r\n";
		$data .= "Content-Type: image/jpeg\r\n\r\n";

		$data .= file_get_contents("./33226.jpg");
		$data .= "\r\n----ABC--\r\n\r\n";
		// fsockopen

		// $fs = fsockopen("www.apitest.com","80",$errno,$errstr,30);

		// $package = "POST /index.php?c=upload&a=upload HTTP/1.1\r\n";
		// $package .= "Host: www.apitest.com\r\n";
		// $package .= "Content-Type: multipart/form-data; boundary=--ABC\r\n";
		// $package .= "Content-Length: ".strlen($data)."\r\n";
		// $package .= "\r\n";

		// $package .= $data;

		// fwrite($fs, $package);

		// $res = stream_get_contents($fs);
		// var_dump($res);

		$res = HttpClient::fsockopenHttp("http://www.apitest.com/index.php?c=upload&a=upload",$data,true);
		var_dump($res);
	}

	public function actionUploadByQiniu(){
		$res = HttpClient::fopenHttp("http://www.apitest.com/index.php?c=upload&a=uploadByQiniu",[
				"filename"=>"33226.jpg",
				"contents" => base64_encode(file_get_contents("./33226.jpg"))
			]);
		var_dump($res);
	}

}