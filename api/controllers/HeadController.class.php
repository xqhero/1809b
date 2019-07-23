<?php
namespace controllers;

class HeadController 
{


	public function actionIndex(){

		$contentType = request()->header("Content-Type");
		var_dump($contentType);
	}

}