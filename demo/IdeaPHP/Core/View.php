<?php
  class  View{
	
	 var $masterLocationFormats;
	

	//查找视图文件，并返回文件的路径
	 function findView($controllerContext,$viewName){
		
		$controllerName=$controllerContext->routeData['controller'];
		$this->viewLocationFormats=$controllerContext->viewLocationFormats;

		$sourceFile;
		$viewFile;
		$found=false;
		foreach ($this->viewLocationFormats as $vlf){
			$_sourceFile=str_ireplace('{1}',$controllerName,$vlf);
			$_sourceFile=str_ireplace('{0}',$viewName,$_sourceFile);
			$_sourceFile=ROOT.$_sourceFile; 
			if(file_exists($_sourceFile)){
				$sourceFile=$_sourceFile;
				
				$_viewFile=str_ireplace('{1}',$controllerName,$vlf);
				$_viewFile=str_ireplace('{0}',$viewName,$_viewFile);
				$_viewFileArr=explode('.',$_viewFile);				
				$_viewFile=$_viewFileArr[0].'.php';
				$viewFile=ROOT.'~cache\\'.$_viewFile;
				$found=true;
				break;
			}
		}
		
		if(!$found){
			throw new Exception('没有找到相关视图文件');					
		}
		
		
		if(file_exists($viewFile)){
			return  $viewFile;
		}
		
        		$viewDir=ROOT.'~cache\views\\'.$controllerName;
		if(!file_exists($viewDir)){		
			 if(!$this->createFolder($viewDir)){
				echo  '无法创建目录'.$viewDir;
			 }
		}
		$razor=new Razor();
		$razor->generateViewFile($sourceFile,$viewFile);
		return $viewFile; 
	}

	 private function createFolder($path){
	   if (!file_exists($path)) {
			$this->createFolder(dirname($path));   
			mkdir($path, 0777);
	   }
	   return true;
	}

	
	
}


?>