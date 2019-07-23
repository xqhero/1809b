<?php
namespace controllers;

use libs\{ Response,Upload};
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;

class UploadController 
{

	// 普通的multipart/form-data方式上传
	public function actionUpload(){

		// var_dump($_FILES);
		// 	exit;
		$upload = new Upload();
		$upload->setConfig("ext",['jpg','jpeg','png']);
		$upload->setConfig("mimeType",['image/jpeg','image/png','image/gif']);
		$res = $upload->uploadOne();
		if(!$res){
			Response::returnData(1007,'文件上传失败',['error'=>$upload->getError()]);
		}

		Response::returnData(1008,"文件上传成功",$res);

		//echo "<script>parent.add('".json_encode($res)."');</script>";

	}

	// base64 方式上传
	public function actionUploadByBase64(){

		// 
		$file = $_POST['file'];
		$name = $_POST['name'];
		$size = $_POST['size'];

		$upload = new Upload();
		$upload->setConfig("ext",['jpg','jpeg','png']);
		$upload->setConfig("mimeType",['image/jpeg','image/png','image/gif']);
		$res = $upload->uploadByStream($name,$size,$file);
		if(!$res){
			Response::returnData(1007,'文件上传失败',['error'=>$upload->getError()]);
		}

		Response::returnData(1008,"文件上传成功",$res);

	}

	public function actionUploadByStream(){

		// $file = file_get_contents("php://input");
		// file_put_contents("./uploads/a.jpg", $file);
		echo "hello";
		file_put_contents("php://output", "hello");
	}

	public function actionJson(){
		$data = file_get_contents("php://input");
		var_dump($data);
	}

	public function actionXml(){

		$data = file_get_contents("php://input");
		var_dump($data);
	}


	public function actionUploadByQiniu(){
		// 接收文件的名称
		// 接收文件的内容
		$file_name = $_POST['filename'];
		$file_contents = base64_decode($_POST['contents']);

		$accessKey = config('qiniu.accessKey');
		$secretKey = config('qiniu.secretKey');
		$bucketName = config('qiniu.bucketName');
		$domain = config('qiniu.domain');

    	$auth = new Auth($accessKey, $secretKey);
    	$token = $auth->uploadToken($bucketName);
    	$upManager = new UploadManager();
    	list($ret, $error) = $upManager->put($token, $file_name, $file_contents);
    	if($error){
    		Response::returnData(500,"INNER ERROR");
    	}

    	Response::returnData(200,'OK',['path'=>$domain."/".$ret['key']]);
	}

}