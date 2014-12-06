<?php
abstract class Controller{
 var $viewData;
 var $controllerContext;
 var $routeData;
 var $request;
 
 
	function view() {
 
	   	$viewName;
		//$controllerName=$this->routeData['controller'];
	    //获得参数
	    $args = func_get_args();
		//获得参数的个数
	    $argNum =count($args);
		if($argNum==1){
			 $viewName= $args[0];
			 $this-> viewOverload($viewName,$controllerName);
		    }else{
			$viewName= $this->routeData['action'];
			$viewData= $this->viewData;
			
			$renderAction=&HtmlHelper::renderAction;
			$view= new View();		
			$viewSourceFile=$view->findView($this->controllerContext,$viewName);
			require($viewSourceFile);
	}
 }
 
	private function viewOverload($viewName,$controllerName) {
		$viewData= $this->viewData;
		$view= new View();
		$viewSourceFile=$view->findView($controllerName,$viewName);
		require($viewSourceFile);
	}
 
	function partialView(){
		header("content-type:text/html; charset=utf-8");
		return $this-> View();
	}
 
	function json($data) {
		echo json_encode($data);
	}
 
	function content($content) {
		echo  $content;
	}
 
	public  function onActionExecuting($controllerContext){}
   
	public  function onActionExecuted( $controllerContext) {}
  
	//执行当前动作
	function executeAction(){		
		$args=array();
		$method =   new ReflectionMethod($this, $this->routeData['action']);
		$params =  $method->getParameters();
	  
		foreach ($params as $param){
			$name = $param->getName();
			if(isset($this->routeData[$name])) {
				$args[] =  $this->routeData[$name];
			}elseif(isset($_REQUEST[$name] )){
				$args[] =  $_REQUEST[$name];
			}elseif($param->isDefaultValueAvailable()){
				$args[] = $param->getDefaultValue();
			}
		}

		if(count($params)==0){
			$method->invoke($this);
		}else{
			$method->invokeArgs($this,$args);
		}
	}

  
}