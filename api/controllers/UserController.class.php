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

		// 将整个$res用户信息都给jwt
		unset($res['password']);
		// 加密
		$user = encrypt(json_encode($res),config("encrypt.key"),config("encrypt.iv"));
		// 密码正确，则生成token
		$user_token = $model->createToken($user);
	
		Response::returnData(200,"ok",['accesstoken'=>$user_token]);

	}

	



}