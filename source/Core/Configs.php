
<?php

return array(
		//�Զ���·�ɱ�
		'routeTable'=>array(
				'{controller}/{action}'=>array('controller'=>'Home','action'=>'Index'),
		),
		
		//�Զ�����ͼ����
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
		 
		 //�Ƿ�������ģʽ����֮��Ϊ����ģʽ���ٵ���ģʽ�����ܻή�͡�
		 'debug'=>true,
	
	);