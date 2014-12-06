<?php


class BlogController extends BaseController
{

	 function Index(){
		  $this->viewData['title']='First Title';
		  $this->viewData['list']=array('A','B','C','D');
		//  echo "blog/index\r\n";
		  $this->View("index");
	 }
	 
	  function Cate($cateId){
		  echo $cateId;
		  $this->viewData['title']='First Title';
		  $this->viewData['list']=array('A','B','C','D');
		  //echo "blog/cate";
		  $this->View();
	 }
 
 
}
