<?php

class Application{
	
	// //是否启用Razor视图引擎
	// public static  $razor=true;
	
	
	public static $configs=array(
		//自定义路由表
		'routeTable'=>array(
				'blog/{action}/{cateId}'=>array('controller'=>'blog','action'=>'Index','cateId'=>1),
				'{controller}/{action}'=>array('controller'=>'Home','action'=>'Index'),
		),
		
		//自定义试图规则
		// 'viewLocationFormats'=>array(
					// "Views/{1}/{0}.phphtml",
					// "Views/{1}/Controls/{0}.phphtml",
					// "Views/{1}/{0}.phphtml",                
					// "Views/Shared/{0}.phphtml",
					// "Views/Shared/Controls/{0}.phphtml",
					// "Views/Shared/{0}.phphtml",
		// ),
		
		//是否开启调试模式，反之则为发布模式。再调试模式下性能会降低。
		'debug' =>true,
	
	);
			

}






