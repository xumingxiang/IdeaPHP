<?php

require_once(ROOT .'\Application.php');  
define('CONTROLLERS', ROOT . 'Controllers\\');
define('CORE',ROOT.'IdeaPHP\\Core');
define('LIBS',ROOT.'IdeaPHP\\Libs');


set_include_path(CONTROLLERS . PATH_SEPARATOR . get_include_path());
set_include_path(CORE . PATH_SEPARATOR . get_include_path());
set_include_path(LIBS . PATH_SEPARATOR . get_include_path());



//合并两个数组
$defaultConfigs=require_once('Configs.php');	
$configs=array_merge($defaultConfigs,Application::$configs);



//如果是提示模式，则删除模版缓存
if($configs['debug']){
	IO::deleteDir(ROOT . '~cache\\');
}



$routeData= Route::parsePathInfo($configs['routeTable']);
$controllerContext= new controllerContext($routeData);

$controllerContext->viewLocationFormats=$configs['viewLocationFormats'];
$controllerObj=$controllerContext->controller;	
$controllerObj->onActionExecuting($controllerContext);
$controllerObj->executeAction();
$controllerObj->onActionExecuted($controllerContext);




function __autoload($class){
	if (class_exists($class, false)){
		return;
	}
	$file=str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
	require_once($file);
}


