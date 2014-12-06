<?php
class HttpRequest {

	public function queryString(){
		return  $_GET;
	}
	
	public function form(){
		return  $_POST;
	}
	
	//获取  System.Web.HttpRequest.QueryString、
	//		System.Web.HttpRequest.Form、
	//		System.Web.HttpRequest.ServerVariables
    //		System.Web.HttpRequest.Cookies 项的组合集合。
	public function params(){
		 return  $_REQUEST;
	}
	
	
	
	public function getUrlReferrer(){
		return isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
	}
	
	

	
	
	
}