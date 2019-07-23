<?php
namespace controllers;

use libs\Controller;
use models\UserModel;
use libs\Response;

//extends Controller
class UserController {

	// 实现用户的登录

	public function actionLogin(){

		$username = request()->all('username');
		$password = request()->all('password');

		// 验证是否有该用户
		$model = new UserModel();
		$res = $model->getUserInfoByUsername($username);

		if(!$res){
			Response::returnData(1002,'NOT EXIST USERNAME');
		}

		// 验证密码
		// 密码加密 password_hash
		if(!password_verify($password,$res['password'])) {
			// 密码错误
			Response::returnData(1003,"WRONG PASSWORD");
		}

		// 密码正确，则生成token
		$user_token = $this->createToken(30);
		$expiretime = time() + 7200;
		// 存到数据表中
		$res = $model->exec("update __table__ set usertoken=?,expiretime=? where username=?",[$user_token,$expiretime,$username]);

		if(!$res){
			Response::returnData(500,"INTERNAL ERROR");
		}

		Response::returnData(200,"ok",['accesstoken'=>$user_token,"expiretime"=>$expiretime]);

	}

	protected function createToken($len = 30){
		// 先得到唯一的微妙级别
		$rand = uniqid();// 返回长度为13的字符串
		$rand = $rand . createRandString($len-strlen($rand));
		return $rand;
	}


}