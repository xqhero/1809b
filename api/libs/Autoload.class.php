<?php

class Autoload{


	// 1. __autoload() 魔术函数 全局只能有一个
	// 2. spl_autoload_register 注册自动加载函数，你可以注册任意多个

	public function __construct(){

		// 注册自动加载类的函数
		spl_autoload_register([$this,"_autoload"]);

		// 加载函数库
		$this->loadFuns();

		// 注册异常处理机制
		set_exception_handler([$this,"exceptionHandler"]);
		set_error_handler([$this,"errorHandler"]);
		register_shutdown_function([$this,"shutdownHandler"]);
	}

	protected function _autoload($className){

		$ext = ".class.php";
		$file = str_replace("\\", DIRECTORY_SEPARATOR, $className).$ext;
		$file = APP_PATH.DIRECTORY_SEPARATOR."../".$file;
		//echo $file;
		if(file_exists($file)){
			include_once $file;
		}
	}

	// 自动加载函数库
	protected function loadFuns($path=''){
		$pathes = [
			FUNC_PATH,
			$path
		];
		foreach($pathes as $path) {

			if($path && is_dir($path)){
				if($dir = opendir($path)){
					while(($file = readdir($dir))!==false){
						if($file != '.' && $file != '..') {
							include $path.DIRECTORY_SEPARATOR.$file;
						}
					}
				}
			}
		}
	} 

	public function exceptionHandler(Throwable $exception){
		
		$message = $exception->getMessage()." in ".$exception->getFile().' on line '.$exception->getLine();
		Libs\Log::getInstance()->setDir(__DIR__.'/../logs/customlog')->setFormat("[time: %s][level: %s][info:%s]")->info($message);

		//sendMail($message,"系统发生错误");
		//setLog("myapp",$message);
		libs\Response::restfulResponse(500,"",['error'=>"内部错误"]);

	}

	public function errorHandler($errno, $errstr, $errfile, $errline){
		
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}

	public function shutdownHandler(){
		 //var_dump(error_get_last());
		 //exit;
		if(!is_null($error = error_get_last()) && $this->isFatal($error['type'])){
			$this->exceptionHandler(new ErrorException( $error['message'], $error['type'], 0, $error['file'], $error['line']));
		}
	}

	protected function isFatal($type)
	{
    // 以下错误无法被 set_error_handler 捕获: E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING
     return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
	}
}

new Autoload();