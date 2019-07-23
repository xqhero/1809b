<?php
namespace controllers;

class SignController 
{
	public function actionIndex(){

		$signArr = createSign();
		var_dump($signArr);
	}

}