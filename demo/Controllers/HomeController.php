<?php


class HomeController extends BaseController {

	function Index(){
	  $this->viewData['title']='First Title';
	  $this->viewData['list']=array('A','B','C','D');
	  
	 
	  
	  $this->View();
 }
 
	function PersonList(){
	echo '1231313';
	$p1=new Person();
	$p1->name="徐明祥1";
	$p1->age=20;
	$p1->sex="男";
	
	$p2=new Person();
	$p2->name="徐明祥2";
	$p2->age=21;
	$p2->sex="女";
	
	$pers=array(
		$p1,$p2
	);
	echo $pers[0]->name;
	
	$this->viewData['pers']=$pers;
	echo 'aaaaaaaaaaaaaaaaa';
	$this->partialView();
 }
 
	function PersonList2(){
		$p1=new Person();
		$p1->name="徐明祥1";
		$p1->age=20;
		$p1->sex="男";
		
		
		$p2=new Person();
		$p2->name="徐明祥2";
		$p2->age=21;
		$p2->sex="女";
		
		$pers=array(
			$p1,$p2
		);
		echo $pers[0]->name;
		
		$this->viewData['pers']=$pers;
		$this->View();
	}
 
	function TestContent(){
		 $this->Content('{"sex":"男","name":"徐明祥"}');
	 }
 
	function TestJson() {
		$per= new person();
		$per->sex="男";
		$per->name="xmx";
		$this->Json($per);
	 }

	function TestSetCache(){
	
		Cache::set('aa', '我是aa');
		echo  Cache::get('aa');
	
	}

 
 }



class person
{
	var $sex;
	var $name;
}