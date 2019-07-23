<?php
namespace libs;

class Config {

	private $config=[];

	private static $instance;


	private function __construct($path=""){
		$paths = [
			CONFIG_PATH,
			$path
		];
		$configs = [];
		// 循环得到这两个路径下的文件
		foreach($paths as $path){
			$files = $this->getFile($path);
			foreach($files as $v){
				$configs = array_merge($configs,$this->parseFile($v));
			} 
		}
		$this->config = $configs; 
	} 

	private function __clone(){

	}

	public static function getInstance(){

		if(!self::$instance instanceof self){
			self::$instance = new self;
		}
		return self::$instance;
	}

	protected function parseFile($filename){
		return parse_ini_file($filename,true);
	}

	protected function getFile($dir){

		$files = [];

		if(is_dir($dir)){
			if(($fd = opendir($dir))!== false ){

				while(($file = readdir($fd))!= false){
					$path = $dir.DIRECTORY_SEPARATOR.$file;
					if($file != '.' && $file !=".."){
						if(is_dir($path)){
							$fileArr =  $this->getFile($path);
							foreach($fileArr as $v){
								$files[] = $v;
							}
						}else{
							$files[] = $path;
						}
					}
				}
			}
		}
		return $files;
	}

	public function setConfig($name="",$value=""){

		if($name){
			$key = "";
			$name = explode(".", $name); 
			if(count($name) == 1 || $name[1]==""){
				$key = $name[0];
				$this->config[$key] = $value;
			}else{
				$this->config[$name[0]][$name[1]] = $value;
			}
		}
	}

	public function getConfig($name=""){

		if($name){
			$key = "";
			$name = explode(".", $name); 
			if(count($name) == 1 || $name[1]==""){
				$key = $name[0];
				return  isset($this->config[$key]) ? $this->config[$key] : null;
			}
			return  isset($this->config[$name[0]][$name[1]]) ? $this->config[$name[0]][$name[1]] : null;
		}
		return $this->config;
	}

}