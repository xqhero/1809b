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
		$token = request()->all('access_token');
		// 通过token获取用户的信息
		$model = new UserModel();
		// 通过token进行查找，得到用户的信息
		$user = $model->getUserInfoByToken($token);
		if(!$user){
			Response::returnData(401,"UNAUTHORIZATION");
		}

		// 验证token是否过期
		if(time()-$user['expiretime'] >  0 ) {
			Response::returnData(1004,"EXPIRE USRETOKEN");
		}

		$this->user = $user;
	}



}