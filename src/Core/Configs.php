
<?php

return array(
		//自定义路由表
		'routeTable'=>array(
				'{controller}/{action}'=>array('controller'=>'Home','action'=>'Index'),
		),
		
		//自定义试图规则
		 'viewLocationFormats'=>array(
			     "{0}.phphtml",
                "{0}.phphtml",                
                "Views/{1}/{0}.phphtml",
                "Views/{1}/Controls/{0}.phphtml",
                "Views/{1}/{0}.phphtml",                
                "Views/Shared/{0}.phphtml",
                "Views/Shared/Controls/{0}.phphtml",
                "Views/Shared/{0}.phphtml",
		 ),
		 
		 //是否开启调试模式，反之则为发布模式。再调试模式下性能会降低。
		 'debug'=>true,
	
	);