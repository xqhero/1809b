<?php
namespace controllers;

use libs\Log;


class EncryptController
{
	protected $key = "";

	public function __construct(){
		$this->key = createRandString(1024);
	}

	public function actionHash(){

		// md5
		echo "<br>",md5("123456",false); // 32位
		echo "<br>",md5("123456",true),"<br>";

		echo "<br>",md5_file("./uploads/5d09da39c395bmh5C4.jpg");

		// sha1
		echo "<br>",sha1("123456",false); // 40位
		echo "<br>",sha1("123456",true); // 20长度
		echo "<br>",sha1_file("./uploads/5d09da39c395bmh5C4.jpg");

		// hash
		echo "<br>这是hash函数测试";
		echo "<br>",hash("md5","123456");
		echo "<br>",hash("sha1","123456");
		echo "<br>",hash("sha256","123456");
		echo "<br>",hash("sha256","./uploads/5d09da39c395bmh5C4.jpg");

		// hash_hmac
		echo "<br>这是hash_hmac系列函数测试";
		echo "<br>",hash_hmac("md5","123456","1810b");
		echo "<br>",hash_hmac("sha1","123456","lening");
		echo "<br>",hash_hmac("sha256","123456","孙玉娟");
		echo "<br>",hash_hmac("sha512","123456","兰迪");
		echo "<br>",hash_hmac_file("sha512","./uploads/5d09da39c395bmh5C4.jpg","周老师真帅气");

		// crypt  返回最多13个字符
		echo "<br>这是crypt测试";
		$hashed_password = crypt("123456","ab");
		echo "<br>",$hashed_password;
		// 验证密文是否为明文加密后的内容
		if(hash_equals($hashed_password, crypt("123456", $hashed_password))){
			echo "<br>","这的确是123456使用crypt加密后的结果";
		}

		// password_hash  password_verify

		// random_bytes
		$bytes = random_bytes(5);
		var_dump($bytes);
		// random_int
		var_dump(random_int(100, 999));
		var_dump(rand(100,999));

		// Mcrypt php7.0以下使用这个库实现堆成和非对称加密

		// openssl
		// php 7.0以上统一使用openssl实现加密解密

		//dd(openssl_get_cipher_methods());

		// 对称加密
		echo "<br><br><br>";
		echo "<br>这是对称加密ECB模式演示:<br>";
		$encrypt = $this->encrypt("123456","this is a test");
		var_dumP($encrypt);
		echo "<br>这是对称解密ECB模式演示:<br>";
		$decrypt = $this->decrypt($encrypt,"this is a test");
		var_dumP($decrypt);
		echo "<br>这是对称加密CBC模式演示:<br>";
		// $iv = createRandString(8);
		$iv = bin2hex(random_bytes(8));
		// var_dump("iv为:",$iv);

		$encrypt = $this->encryptCBC("123456","this is a test",$iv);
		var_dumP($encrypt);
		echo "<br>这是对称解密CBC模式演示:<br>";
		// $key = createRandString(16);
		$decrypt = $this->decryptCBC($encrypt,"this is a test",$iv);
		var_dumP($decrypt);





	}

	public function actionIndex(){
		// 错误报告的方式
		// $rt = base64_encode(random_bytes(16));
		// echo substr(str_replace(["=","/","+"], "", $rt), -16);
		// exit;
		//trigger_error("this is a error message",E_USER_WARNING);
		
		$arr = ["username"=>1,"password"=>2];
		setLogs("encrypt","this is testing in EncryptController",$arr);
		$arr = ["username"=>1,"password"=>2];

		echo ($res = serialize($arr));
		exit;
		var_dump(unserialize($res));
		exit;
		$_txt = $_GET['txt'];

		$encrypt = base64_encode($_txt ^ $this->key);

		echo "<br>",$encrypt;

		$decrypt = base64_decode($encrypt) ^ $this->key;

		echo "<br>",$decrypt;

		$_txt = $_GET['res'];

		$encrypt = base64_encode($_txt ^ $this->key);

		echo "<br>",$encrypt;

		$decrypt = base64_decode($encrypt) ^ $this->key;

		echo "<br>",$decrypt;
	}

	protected function encrypt($data="",$key=""){

		// 使用openssl_encrypt进行加密
		// return openssl_encrypt($data,"DES-ECB3", $key);
		// 使用3des进行加密
		// return openssl_encrypt($data,"DES-EDE3", $key);
		return openssl_encrypt($data,"AES-128-ECB", $key);
	}
	protected function decrypt($data="",$key=""){
		// 使用openssl_encrypt进行加密
		//return openssl_decrypt($data,"DES-ECB", $key);
		// 使用3des进行解密
		// return openssl_decrypt($data,"DES-EDE3", $key);
		return openssl_decrypt($data,"AES-128-ECB", $key);
	}
	protected function encryptCBC($data="",$key="",$iv=""){
		// 使用openssl_encrypt进行加密
		//return openssl_encrypt($data,"DES-CBC", $key,0,$iv);
		//return openssl_encrypt($data,"DES-EDE3-CBC", $key,0,$iv);
		return openssl_encrypt($data,"AES-128-CBC", $key,0,$iv);
	}
	protected function decryptCBC($data="",$key="",$iv=""){
		// 使用openssl_encrypt进行加密
		// return openssl_decrypt($data,"DES-CBC", $key,0,$iv);
		//return openssl_decrypt($data,"DES-EDE3-CBC", $key,0,$iv);
		return openssl_decrypt($data,"AES-128-CBC", $key,0,$iv);
	}


	public function opensslRsa(){
		// 1. 生成公钥私钥对
		$config = [
			"config"=>"C:\phpStudy\PHPTutorial\Apache\conf\openssl.cnf",
			"private_key_bits"=>"2048",
		];
		$pk = openssl_pkey_new($config);
		
		// 得到私钥
		openssl_pkey_export($pk,$privateKey,null,$config);
		//var_dump($privateKey);

		$pk = openssl_pkey_get_details($pk);
		// 得到公钥
		$publicKey = $pk['key'];
		
		openssl_private_encrypt("兰迪真美", $encrypt, $privateKey);

		echo base64_encode($encrypt);

		openssl_public_decrypt($encrypt, $decrypt, $publicKey);

		echo "<br>",$decrypt;


	}

	public function actionRsa(){
		// list($publicKey,$privateKey) = $this->rsa();

		// var_dump($publicKey);
		// var_dump($privateKey);
		// echo "<br>待加密后的数据为:","101<br>";
		// echo "<br>加密后的数据为:","<br>";
		// $encrypt = $this->rsaEncrypt("101",$publicKey);

		// echo $encrypt;
		// echo "<br>解密后的数据为:","<br>";
		// $decrypt = $this->rsaDecrypt($encrypt,$privateKey);
		// echo $decrypt;
		$this->opensslRsa();
	}

	// 判断一个数字是否为质数
	protected function isPrime($value){
		$is = true;
		for($i=2;$i<=floor($value/2);$i++){
				if($value%$i == 0){
					$is = false;
					break;
				}
		}
		return $is;
	}

	protected function createPrime(){
		while(true){
			$key = mt_rand(2,100);
			if($this->isPrime($key)){
				break;
			}
		}
		return $key;
	}

	protected function isPrimePair($value1,$value2){
		$is = true;
		$min = min($value1,$value2);
		// 
		for($i=2;$i<=$min;$i++){
			if($value1%$i == 0 && $value2%$i==0){
				$is = false;
				break;
			}
		}
		return $is;
	}

	protected function getPrivateKey($N,$publicKey){

		for($privateKey=2;;$privateKey++){
			$product = gmp_mul($privateKey,$publicKey);
			if( gmp_mod($product,$N) == 1){
				break;
			}
		}
		return $privateKey;
	}
	// 自己生成公钥和私钥
	public function rsa(){
		// 1. 自定义两个数字，p  q (质数/素数)
		// $p = $this->createPrime();
		// $q = $this->createPrime();
		$p = 7;
		$q = 13;

		$N = $p * $q;
		$num = ($p-1)*($q-1);
		// 计算($p-1)*($q-1)
		while(true){
			$publicKey = mt_rand(2,$num-1);
			if($this->isPrimePair($publicKey,$num)){
				break;
			}
		}
		$privateKey = $this->getPrivateKey($num,$publicKey);
		
		return [[$N,$publicKey],[$N,$privateKey]];

	}

	protected function rsaEncrypt($data,$key){
		$res = gmp_strval(gmp_pow($data,$key[1]));
		//echo "<br>",$res,"<br>";
		return gmp_strval(gmp_mod($res,$key[0]));
	}

	protected function rsaDecrypt($data,$key){
		$res = gmp_strval(gmp_pow($data,$key[1]));
		var_dump($res);
		//echo "<br>",$res,"<br>";
		return gmp_strval(gmp_mod($res,$key[0]));
	}

}