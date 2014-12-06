<?php

// 不依赖任何文件

class Route{
	
	//解析$_SERVER['PATH_INFO']
	public static function parsePathInfo($routeTable){
		$routeData= array();
		$pathInfo=!empty($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'';
		$pathInfo=trim($pathInfo,'/');
	    $arrPathInfo=explode('/',$pathInfo);
	    //控制器
		$controller	=strtolower(empty($arrPathInfo[0])?'home':$arrPathInfo[0]);
		//动作
		$action=strtolower(empty($arrPathInfo[1])?'index':$arrPathInfo[1]);
		
		if(count($arrPathInfo)<2){
			 $routeData['controller']=$controller;
			 $routeData['action']=$action;
			 return $routeData;
		}
		
		$isMatch;
		foreach($routeTable as $key=>$val){
			$isMatch=self::isMatch($key,$pathInfo);		
			if($isMatch){
				$routeData=self::getRouData($key,$val,$pathInfo);
				break;
			}
		}
		
		
		if($isMatch){
			$routeData['controller']=$controller;
			$routeData['action']=$action;
			return $routeData;
		}else{
			throw  new IdeaException("没有找到匹配的路由规则");
		}
		
		
	}
	
	//检测pathinfo 和 指定路由 是否匹配
	private static  function isMatch($route,$pathInfo){
		
		    $arrPathInfo=explode('/',$pathInfo);
			$arrRoute=explode('/',$route);
			$arrPath_cnt=count($arrPathInfo);
			$arrRoute_cnt=count($arrRoute);

			if($arrPath_cnt>$arrRoute_cnt){//Url参数个数大于路由参数个数
				return false;
			}
			
			for($i=0;$i<$arrPath_cnt;$i++){
				$pathItem=$arrPathInfo[$i];
				$routeItem=$arrRoute[$i];
				
				//路由参数中有字符常量
				if($routeItem[0]!='{'&& $routeItem[count($routeItem)-1]!='}'){
					if($routeItem!=$pathItem){
						return false;
					}
				}else{
					return true;
				}
			}
			return true;
	}
	
	//获得路由的值
	private static  function  getRouData($routeKey,$routeVal,$pathInfo){
		   
			$routeData;
		    $arrPathInfo=explode('/',$pathInfo);
			$arrRouteKey=explode('/',$routeKey);
			$arrPath_cnt=count($arrPathInfo);
			$arrRouteKey_cnt=count($arrRouteKey);
		
		
			
			for($i=0;$i<$arrRouteKey_cnt;$i++){
				$key= $arrRouteKey[$i];
				if($key[0]!='{'){
					continue;
				}
				
				$key= trim($arrRouteKey[$i],'{');
				$key=trim($key,'}');

				if($i<=$arrPath_cnt-1){
					$routeData[$key]= $arrPathInfo[$i];
				}elseif(isset($routeVal[$key])){
					$routeData[$key]= $routeVal[$key];
				}else {
					$routeData[$key]=null;
				}
			}
			return $routeData;
	}
	
}


