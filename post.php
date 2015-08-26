<?php
session_start();
if(isset($_SESSION['username'])){
	$text = $_POST['text'];
	$from=$_SESSION['username'];
	$to=$_POST['friend'];
	date_default_timezone_set('America/Los_Angeles');
	$time=date("Y-m-d G:i:s");
	
	$xml = simplexml_load_file('log.xml');
	$newChild=$xml->addChild("log");
	$newChild['from']=$from;
	$newChild['to']=$to;
	$newChild['time']=$time;
	$newChild['msg']=$text;
	$xml->asXML('log.xml');
	
	$xml = simplexml_load_file('users.xml');
	foreach($xml->user as $result){	
		if($result->name==$to){
			$result['newMessage'].=','.$from;
		}
		
	}
}
?>