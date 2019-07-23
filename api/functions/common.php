<?php


function dd(...$var){
	echo "<pre>";
	foreach($var as $v){
		var_dump($var);
	}

	exit;

}


function request(){
	static  $request = null;

	if(!$request instanceof libs\Request) {
		$request = new libs\Request;
	}
	
	return $request;
}

function config($name=""){
	$config = libs\Config::getInstance();
	return $config->getConfig($name);
}

// 加密
function encrypt($data,$key,$iv){
	return openssl_encrypt($data, "AES-128-CBC", $key, 0, $iv);
}
// 解密
function decrypt($data,$key,$iv){
	return openssl_decrypt($data, "AES-128-CBC", $key, 0, $iv);
}



function setLog($name,$data,$info=[]){
	static $logger = null;
	if(!$logger instanceof \Monolog\Logger){
		$logger = new \Monolog\Logger($name);
	}
	$file = LOG_PATH."/".date("Y-m-d").".log";
	if(!file_exists($file)){
		file_put_contents($file, "");
	}
	$logger->pushHandler(new \Monolog\Handler\StreamHandler($file, Monolog\Logger::DEBUG));
	$logger->addInfo($data,$info);
}


function sendMail($body="",$subject="",$from="",$to="",$server="",$port="",$username="",$password=""){
		$server || $server = config("mailer.server");
		$port || $port = config("mailer.port");
		$username || $username = config("mailer.username");
		$password || $password = config("mailer.password");
		$subject || $subject = config("mailer.subject");
		$from || $from = config("mailer.from");
		$to || $to = config("mailer.to");
		$body || $body = config("mailer.body");

		$transport = (new Swift_SmtpTransport($server, $port))
		->setUsername($username)
		->setPassword($password);

		$mailer = new Swift_Mailer($transport);
		$msg = new Swift_Message($subject);

  		$msg->setFrom([$from=>"xqhero"])
  		->setTo([$to])
		->setBody(
		    $body,
		    'text/html'
		);
		$mailer->send($msg);
}