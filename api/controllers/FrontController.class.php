<?php
namespace controllers;

use models\UserModel;
use libs\Http;

class FrontController {

	public function actionHeader(){
		include "../views/header.html";
	}

	public function actionTest(){

		$res = Http::postHttp("http://www.api.com/index.php?c=index&a=display",["noncestr"=>'5Bky07Dg$SvLsxg4BZI8',"timestamp"=>"1560995317","sign"=>"0ab0d2802c6188d161716ccd4cdee999a57a6647"],false,["Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOlsiYXVkaWVuY2VfMSIsImF1ZGllbmNlXzIiXSwiZXhwIjoxNTYxMDA1NDI5LCJpYXQiOjE1NjA5OTgyMjksImlzcyI6Inpob3VndW9xaWFuZyIsImp0aSI6IjEiLCJuYmYiOjE1NjA5OTgyMjksInN1YiI6Imh0dHA6XC9cL3d3dy5hcGl0ZXN0LmNvbSIsInVzZXJfaWQiOiIxIn0.o22oIGgr4kfJYHmoF9Q9AI2-peg63pvAeAXaDrjw3_Q","x-api-token: leningEducation"]);
		var_dump($res);

	}

	public function actionComponent(){

		// $model = new UserModel();
		// $res = $model->query("select * from __table__ where id > :id",["id"=>0]);
		// var_dump($res);

		include "../views/component.html";

	}

	public function actionIndex(){

		include "../views/huge.html";
	}

	public function actionJs(){
		$callback = $_GET['jsoncallback'];
		$model = new UserModel();
		$data = $model->query("select * from __table__ where id=1");
		$data = json_encode($data);
		echo $callback."('".$data."')";
	}

	public function actionEncrypt(){
		include "../views/encrypt.html";
	}


}