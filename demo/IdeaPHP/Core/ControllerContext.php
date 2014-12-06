<?php
class ControllerContext
{
	var $controller;
	var $httpContext;
	var $routeData;
	var $viewLocationFormats;
	var $request;
	
	 function __construct($routeData)
	 {
		 //控制器名称
		$controllerName=$routeData['controller'];
		$controllerClassName=$controllerName.'Controller';

		$this->controller= new $controllerClassName();
		$this->routeData=$routeData;
		
		$req=new HttpRequest();
		$this->request=$req;
		
		$this->controller->routeData=$routeData;
		$this->controller->controllerContext=$this;
		$this->controller->request =$req;
		
		
	 }
	 
	
	
	
}
