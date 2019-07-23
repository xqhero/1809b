<?php
// 允许的来源
// header("Access-Control-Allow-Origin: http://www.apitest.com");
header("Access-Control-Allow-Origin: http://www.1810b.com");
// 允许的提交方法
header("Access-Control-Allow-Methods: GET,POST,HEAD,PUT");
// 支持的自定义Header首部字段
header("Access-Control-Allow-Headers: Authorization,Content-Type,Content-Length,Accept,Accept-Encoding,Host,Referer,x-api-token");

header("Access-Control-Expose-Headers: Referer");


// 定义各种常量
define("APP_PATH",__DIR__);
define("FUNC_PATH",__DIR__.'/../functions');
define("CONFIG_PATH",__DIR__.'/../config');
define("LOG_PATH",__DIR__."/../logs");

// 设置错误处理
ini_set("display_errors", "On");
ini_set("log_errors","On");
ini_set("error_log",__DIR__."/../logs/error.log");

// 设置时区
// date_default_timezone_set("PRC");
// var_dump(ini_get("errors"));
// var_dump(ini_get_all());
 // exit;
// 1. 引入自动加载
require_once "../libs/Autoload.class.php";
// 加载composer为我们提供的自动加载类
require_once "../vendor/autoload.php";
// 2. 路由，得到操作哪个控制器，哪个方法
$route = new libs\Route();
list($controller,$action) = $route->routeParse();

// 实例化控制器
$controller = "controllers\\".$controller;
//try{
	(new $controller())->$action();
//}catch(Throwable $e){
	//libs\Response::returnData("400","Bad Request");
//}



