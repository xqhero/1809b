<?php
namespace controllers;

use models\UserModel;


class IndexController extends UserCommonController {

	public function actionDisplay(){

		// 如果来源不是www.apitest.com 拒绝访问
		// if(request()->header("Referer") !== "http://www.apitest.com/index.php?c=front&a=header"){
		// 	exit("deny access");
		// }
		
		var_dump($this->user);
	}

}