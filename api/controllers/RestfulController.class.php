<?php
namespace controllers;

use libs\Response;
use models\UserModel;

class RestfulController 
{

	public function actionIndex(){
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		switch($method){
			case 'get':
				$this->list();
			break;
			case 'post':
				$this->store();
				break;
			case 'put':
				$this->save();
				break;
			case 'delete':
				$this->delete();
				break;
			default:
				$this->default();
		}
	}

	public function default(){

	}
	
}