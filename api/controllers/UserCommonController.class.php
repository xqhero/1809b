<?php
namespace controllers;

use libs\Controller;
use models\UserModel;
use libs\Response;

// 所有需要用户登录的接口都需要继承该类，该类用于验证usertoken(用户令牌)
class UserCommonController extends Controller
{
	protected $user;

	public function __construct(){

		// 执行父亲的构造方法
		parent::__construct();

		// 验证令牌
		$this->verifyToken();

	}

	protected function verifyToken(){
		// 获取从客户端传递过来的token,从get或者post中传递过来的token
		// $token = request()->all('access_token');
		//var_dump($_SERVER);
		$request = request();

		$token = $request->header('Authorization') ? $request->header('Authorization') : ($request->all('access_token') ? $request->all('access_token') : "");

		// 通过token获取用户的信息
		$model = new UserModel();
		// 验证JWT token是否正确
		$res = $model->verifyToken($token);
		// 通过结果来判断是否放行
		if(is_string($res)){
			Response::returnData(401,$res);
		}else if(is_null($res)){
			Response::returnData(1005,"NOT EXISTS USER");
		}
		$this->user = $res;
	}

}