<?php
session_start();
function findFriend($friendfind){
	$xml = simplexml_load_file('users.xml');
	foreach($xml->user as $result){
		if($result->name==$friendfind){
			return true;
		}
	}
	return false;
}

function addFriendToXml($uname,$fname){
	$xml = simplexml_load_file('users.xml');	
	foreach($xml->user as $result){	
		if($result->name==$uname){
			if($result->friends==""){
				$result->friends=$fname;
			}else{
				$temp=$result->friends;
				$result->friends=$temp.",".$fname;
			}
		}elseif ($result->name==$fname){
			if($result->friends==""){
				$result->friends=$uname;

			}else{
				$temp=$result->friends;
				$result->friends=$temp.",".$uname;
			}
		}else{
				
		}
	}
	$xml->asXML('users.xml');
}
function deleteFriendFromXml($uname,$fname){
	$xml = simplexml_load_file('users.xml');
	$findFriendInMyList=false;
	$findMeInFriendList=false;
	foreach($xml->user as $result){
		if($result->name==$uname){
			$friends=$result->friends;
			$singleFriend=explode(",", $friends);
			foreach($singleFriend as $printFriend){
				if($printFriend==$fname){
					$findFriendInMyList=true;
				}
			}
		}elseif($result->name==$fname){
			$friends=$result->friends;
			$singleFriend=explode(",", $friends);
			foreach($singleFriend as $printFriend){
				if($printFriend==$uname){
					$findMeInFriendList=true;
				}
			}
		}
	}
	if($findFriendInMyList&&$findMeInFriendList){
		foreach($xml->user as $result){
			if($result->name==$uname){
				$friends=$result->friends;
				$singleFriend=explode(",", $friends);
				$temp="";
				foreach($singleFriend as $printFriend){
					if($printFriend!=$fname){
						$temp=$temp.",".$printFriend;
					}
				}
				$result->friends=$temp;
			}elseif($result->name==$fname){
				$friends=$result->friends;
				$singleFriend=explode(",", $friends);
				$temp="";
				foreach($singleFriend as $printFriend){
					if($printFriend!=$uname){
						$temp=$temp.",".$printFriend;
					}
				}
				$result->friends=$temp;
			}
		}
		$xml->asXML('users.xml');
		return true;
	}else{
		return false;
	}
}

if(isset($_POST['addfriendname'])){
	if(!isset($_SESSION['username'])){
		echo "Miss login data";
	}elseif(findFriend($_POST['addfriendname'])){
		addFriendToXml($_SESSION['username'], $_POST['addfriendname']);
		echo "Success, friend list will be refreshed when you login next time";
	}else{
		echo "Cannot find this user";
	}
	
	
	
}elseif (isset($_POST['deletefriendname'])){
	if(!isset($_SESSION['username'])){
		echo "Miss login data";
	}elseif(findFriend($_POST['deletefriendname'])){
		if(deleteFriendFromXml($_SESSION['username'], $_POST['deletefriendname'])){
			echo "Success, friend list will be refreshed when you login next time";
		}else{
			echo "fail";
		}
	}else{
		echo "Cannot find this user";
	}
}else{
	
}

?>