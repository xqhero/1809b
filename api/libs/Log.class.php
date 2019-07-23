<?php
namespace libs;

class Log  
{

	// 单一实例
	private static $instance;
	// 存放日志文件的目录
	protected  $dir = LOG_PATH;
	// 存放的数据格式
	protected  $format = "[%s]-[%s]:%s\r\n";

	private function __construct(){

	}

	public static function getInstance(){
		if(!self::$instance instanceof self){
			self::$instance = new self;
		}
		return self::$instance;
	}
	private function __clone(){

	}

	public function setDir($dir){
		$this->dir = $dir;
		return $this;
	}

	public function setFormat($format){
		if($format){
			$this->format = $format;
		}
		return $this;
	}

	public function info($message=""){

		$this->checkDir();
		$filename = $this->createFile();
		// 生成格式
		$message = $this->formatMessage($message);
		file_put_contents($this->dir.DIRECTORY_SEPARATOR.$filename, $message,FILE_APPEND);

	}

	protected function checkDir(){
		if(!is_dir($this->dir)){
			mkdir($this->dir,0777,true);
		}
	}

	protected function createFile(){
		$filename = date("Y-m-d").".log";
		return $filename;
	}

	protected function formatMessage($message){
		return sprintf($this->format,date("Y-m-d H:i:s"),"info",$message);
	}
}