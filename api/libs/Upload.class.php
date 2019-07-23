<?php
namespace libs;

class Upload {

	protected $config = [
		"ext" => [],
		"mimeType" => [],
		"size" => -1,
		"savePath"=>"./uploads",
		"transferBybase64" => false,
	];

	private $errno = 0;

	public function __construct($config = []){

		$this->config = array_merge($this->config,$config); 
	}

	// 设置单个项
	public function setConfig($name,$value){
		$this->config[$name] = $value;
	}

	// 获取配置项目
	public function getConfig($name){
		return $this->config[$name];
	}
	// 
	// 基于base64上传的方法
	public function uploadByStream($name,$size,$file){

		// 1. 文件的大小是否符合要求
		if(!$this->checkSize($size)){
			$this->errno = 8;
			return;
		}

		// 2. 文件的后缀是否符合要求
		if(!$this->checkExts($name)){
			$this->errno = 9;
			return;
		}
		// 4.1 判断上传目录是否存在，不存在创建
		$this->checkUploadDir();

		// 4.2将临时文件移动到指定的上传目录
		$filename = $this->newFileName($name);

		file_put_contents($this->config['savePath'].DIRECTORY_SEPARATOR.$filename,base64_decode($file));
		// 5. 返回数据
		// 返回的数据 name 、 size 
		$result = [
			"name" => $name,
			"size" => $size,
			"savePath" => $this->config['savePath'],
			"filename" => $filename
		];
		return $result;
	}

	//上传单个文件
	public function uploadOne($fieldName="file"){
		$file = "";
		foreach($_FILES as $v){
			$file = $v;
		}
		return $this->upload($file);
	}
	// 上传多个文件
	public function uploadAll(){
		$results = [];
		foreach($_FILES as $v){
			$files = $v; 			
		}

		foreach($files['name'] as $key=>$v){
			$file = [

				"name"=>$v,
				"tmp_name"=>$files['tmp_name'][$key],
				"type" => $files['type'][$key],
				"error" => $files['error'][$key],
				"size" => $files['size'][$key]
			];
			$result = $this->upload($file);
			if(!$result){
				continue;
			}
			array_push($results,$result);
		}
		return $results;
	}

	// 核心上传方法
	public function upload($file=[]){

		$result = [];
		// 0. 文件上传是否出错error
		if($file['error']){
			$this->errno = $file['error'];
			return;
		}

		// 1. 文件的大小是否符合要求
		if(!$this->checkSize($file['size'])){
			$this->errno = 8;
			return;
		}

		// 2. 文件的后缀是否符合要求
		if(!$this->checkExts($file['name'])){
			$this->errno = 9;
			return;
		}

		// 3. 文件的类型是否符合要求
		if(!$this->checkType($file['tmp_name'],$file['type'])){
			$this->errno = 10;
			return;
		}

		// 4.1 判断上传目录是否存在，不存在创建
		$this->checkUploadDir();

		// 4.2将临时文件移动到指定的上传目录
		$filename = $this->newFileName($file['name']);
		if(!is_uploaded_file($file['tmp_name'])){
			$this->errno = 12;
			return;
		}
		move_uploaded_file($file['tmp_name'], $this->config['savePath'].DIRECTORY_SEPARATOR.$filename);
		// 5. 返回数据
		// 返回的数据 name 、 size 
		$result = [
			"name" => $file['name'],
			"size" => $file['size'],
			"savePath" => $this->config['savePath'],
			"filename" => $filename
		];
		return $result;
	}

	protected function checkUploadDir(){
		if(!is_dir($this->config['savePath'])){
			if(!mkdir($this->config['savePath'],0777,true)){
				$this->errno = 11;
			}
		}
	}


	protected function newFileName($name){
		$ext = $this->getExt($name);
		return uniqid().createRandString(5).'.'.$ext;
	}

	protected function checkType($name,$type){
		// 最好的方式直接通过函数获取真实文件的mime类型
		$mime = mime_content_type($name);
		if($mime == $type){
			return in_array($type, $this->config['mimeType']);
		}
		return false;
	}

	protected function checkSize($size){

		// $config['size'] $size
		if($this->config['size'] == -1){
			return true;
		}
		return $this->config['size'] > $size ? true : false;

	}

	protected function checkExts($name){
		// 得到文件的后缀
		$ext = $this->getExt($name);
		// 
		return in_array($ext, $this->config['ext']);
	}

	protected function getExt($name){
		return pathinfo($name,PATHINFO_EXTENSION);
	}

	public function getError($errno=""){
		$errno = $errno ? $errno : $this->errno;
		$errstr = '';
		switch($errno){
			case UPLOAD_ERR_CANT_WRITE:
				$errstr = "文件写入失败";
			break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$errstr = "找不到临时文件夹";
			break;
			case UPLOAD_ERR_NO_FILE:
				$errstr = "没有文件被上传";
			break;
			case UPLOAD_ERR_PARTIAL:
				$errstr = "只有部分文件被上传";
			break;
			case UPLOAD_ERR_FORM_SIZE:
				$errstr = "上传文件的大小超过了MAX_FILE_SIZE";
			break;
			case UPLOAD_ERR_INI_SIZE:
				$errstr = "上传文件超过PHP规定的大小";
			break;
			case 8:
				$errstr = "上传文件超出了配置大小";
			break;
			case 9:
				$errstr = "文件后缀不符合要求";
			break;
			case 10:
				$errstr = "文件mime类型不符合要求";
			break;
			case 11:
				$errstr = "创建上传目录失败";
			break;
			case 12:
				$errstr = "非法上传文件";
			break;
			case UPLOAD_ERR_OK:

			break;
		}
		return $errstr;
	}

}