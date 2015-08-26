<?php 
session_start();
?>
<?php
//If receive logout message, logout the user, set the user to offline then go back to login page.
if(isset($_GET['logout'])){
	$xml = simplexml_load_file('users.xml');
	foreach($xml->user as $result){
			if($result->name==$_SESSION['username']){
			$result['online']=0;
		}
	}
	$xml->asXML('users.xml');
	session_destroy();
	header("Location: index.php"); 
}

//Get the current system time and return it
function getTime(){
	date_default_timezone_set('America/Los_Angeles');
	return date("Y-m-d G:i:s");
}

//Check the register information, if correct, insert the information into the users.xml
function checkRegister($username,$password,$repeat){
	if (!$username) {
		echo '<script>alert("Username cannot be empty!");</script>';
	}elseif (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
		echo '<script>alert("Username can only contain letters and numbers");</script>';
	}elseif (!trim($password)) {
		echo '<script>alert("Password cannot be empty!");</script>';
	}elseif ($password!=$repeat) {
		echo '<script>alert("Two password mismatched!");</script>';
	}else{
		$xml = simplexml_load_file('users.xml');
		$newChild=$xml->addChild("user");
		$name=$newChild->addChild("name",$username);
		$pw=$newChild->addChild("password",$password);
		$f=$newChild->addChild("friend","");
		$newChild['newMessage']="";
		$newChild['online']=0;
		$xml->asXML('users.xml');
		return true;
	}
	return false;
}

//Check the login information, if username and password are correct, login and set user to online
function checkLogin($username,$password){
	if (!$username) {
		echo '<script>alert("Username cannot be empty!");</script>';
	}elseif (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
		echo '<script>alert("Username can only contain letters and numbers");</script>';
	}elseif (!trim($password)) {
		echo '<script>alert("Password cannot be empty!");</script>';
	}else{
		$xml = simplexml_load_file('users.xml');
		foreach($xml->user as $result){
			if($result->name==$username&&$result->password==$password){
				if($result['online']==1){
					return false;
				}else{
					$result['online']=1;
					$xml->asXML('users.xml');
					return true;
				}
			}
		}
	}
	return false;
}

//Display the register form
function registerForm(){
echo '
<link rel="stylesheet" type="text/css" href="css/normalize.css" />
<link rel="stylesheet" type="text/css" href="css/demo.css" />
<link rel="stylesheet" type="text/css" href="css/componentRegister.css" />
<link href=\'http://fonts.googleapis.com/css?family=Raleway:200,400,800\' rel=\'stylesheet\' type=\'text/css\'>
</head>
<body>
<div class="container demo-1">
<div class="content">
<div id="large-header" class="large-header">
<canvas id="demo-canvas"></canvas>
<section class="login-form-wrap">
<h1>Register</h1>
<form class="login-form" action="index.php" method="post">
<label><input type="usernameR" name="usernameR" required placeholder="Username"></label>
<label><input type="passwordR" name="passwordR" required placeholder="Password"></label>
<label><input type="repeatpassword" name="repeatpassword" required placeholder="Repeat Password"></label>
<input type="submit" name="registerSubmit" id="registerSubmit" value="Register">
<input type="submit" name="registerCancel" id="registerCancel" value="Cancel" />
</form>
</section>
</div>

</div><!-- /container -->
<script src="js/TweenLite.min.js"></script>
<script src="js/EasePack.min.js"></script>
<script src="js/rAF.js"></script>
<script src="js/demo-1.js"></script>
';
}

//Display the login form
function loginForm(){
echo'
<link rel="stylesheet" type="text/css" href="css/normalize.css" />
<link rel="stylesheet" type="text/css" href="css/demo.css" />
<link rel="stylesheet" type="text/css" href="css/component.css" />
<link href=\'http://fonts.googleapis.com/css?family=Raleway:200,400,800\' rel=\'stylesheet\' type=\'text/css\'>
</head>
<body>
	<div class="container demo-1">
		<div class="content">
			<div id="large-header" class="large-header">
				<canvas id="demo-canvas"></canvas>
				<section class="login-form-wrap">
				  <h1>Chat Room</h1>
				  <form class="login-form" action="index.php" method="post">
					  <label><input type="username" name="name" required placeholder="Username"></label>
					  <label><input type="password" name="password" required placeholder="Password"></label>
					  <input type="submit" name="enter" id="enter" value="Login">
					  <input type="submit" name="register" id="register" value="Register" />
				  </form>
				</section>
			</div>
		</div><!-- /container -->
	<script src="js/TweenLite.min.js"></script>
	<script src="js/EasePack.min.js"></script>
	<script src="js/rAF.js"></script>
	<script src="js/demo-1.js"></script>
	</div>
';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<title>Chat Room</title>

<?php
//Check the status with $_POST variables, then switch to different pages or show alert
if(isset($_POST['registerSubmit'])){
	if(checkRegister($_POST['usernameR'], $_POST['passwordR'], $_POST['repeatpassword'])){
		loginForm();
	}else{
		registerForm();
	}
}elseif(isset($_POST['register'])){
	registerForm();
}elseif(!isset($_POST['name'])){
	loginForm();
}elseif(checkLogin($_POST['name'], $_POST['password'])||isset($_POST['selectFriend'])){
	$_SESSION['username']=$_POST['name'];
	if(isset($_POST['selectFriend'])){
		$_SESSION['friend']=$_POST['selectFriend'];
	}
?>
	<link rel="stylesheet" type="text/css" href="css/css.css">
	<script src="jquery-2.1.1.js"></script>
	</head>
	<body>
	<div class="big-box">
	<div class="left-box">
	<div class="chatbox" id="chatbox">
<?php 
//Show welcome message
if(!isset($_SESSION['friend'])){
	echo 'Welcome, <b>'.$_SESSION['username'].'</b> System time is:'.getTime().'<br>Select your friend to start chatting.';
}
?>
		</div>
		<div class="input-field">
			<form name="message" action="">
			<input name="usermsg" type="text" id="usermsg" size="80" />
			<input type="image" id="submitmsg" src="send.png"/>
			</form>
		</div>
	</div>
	<div class="right-box">
	
<ul>
<?php 
//Show users' friends list
$xml = simplexml_load_file('users.xml');
foreach($xml->user as $result){
	if($result->name==$_SESSION['username']){
		$friends=$result->friends;
		$singleFriend=explode(",", $friends);
		$count=1;
		foreach($singleFriend as $printFriend){
			echo '<div><input type="radio" name="radio" id="radio'.$count.'" class="radio" value="'.$printFriend.'"/><label class="radiolabel" for="radio'.$count.'">'.$printFriend.'</label></div>'; 
		$count+=1;
		}
	}
}
?>
		</ul>
		<div class="control-panel">
			<nav>
				<ul>
					<li id="addfriend">Add</li>
					<li id="deletefriend">Delete</li>
					<li id="exit">Exit</li>
				</ul>
			</nav>
	
		</div>
	</div>
	</div>
<div class="hide-body">
            <h1>Add Friend</h1>
			<input size="70" type="text" id="addfriendname" required placeholder="Input your friend's name"><br>
            <input type="button" value="Add" id="addfriendsubmit">
            <input type="button" value="Cancel" id="cancelfriend">
        </div>
        <div class="hide-body2"><h1>Delete Friend</h1>
			<input size="70" type="text" id="deletefriendname" required placeholder="Input your friend's name"><br>
            <input type="button" value="Delete" id="deletefriendsubmit">
            <input type="button" value="Cancel" id="cancelfriend2">
        </div>
        <div class="body-color"></div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript">
//Starting of Javacript Code
$(document).ready(function(){
	var username="";
	var friendseleted="";
	//Check which friend does user want to chat with
	$(".radio").click(function(){
		friendseleted=$('input:radio:checked').val();
		StandardTaxRate();
	});
	//Log out the user
	$("#exit").click(function(){
		var exit = confirm("Are you sure you want to end the session?");
		if(exit==true){window.location = 'index.php?logout=true';}
	});
	//Add friend, parse user input to server 
	$("#addfriendsubmit").click(function(){
		$.post("operatefriend.php", {addfriendname: $("#addfriendname").val()},
				   function(data){
				     alert(data);
				   });
		    $('.body-color').fadeOut(100);
	        $('.hide-body').slideUp(200);  
	});
	//Delete friend, parse user input to server
	$("#deletefriendsubmit").click(function(){
		$.post("operatefriend.php", {deletefriendname: $("#deletefriendname").val()},
				   function(data){
				     alert(data);
				   });
		    $('.body-color').fadeOut(100);
	        $('.hide-body2').slideUp(200); 
	});
	//Cancel add friend
	$("#cancelfriend").click(function(){
		$('.body-color').fadeOut(100);
        $('.hide-body').slideUp(200);        
	});
	//Cancel delete friend
	$("#cancelfriend2").click(function(){
		$('.body-color').fadeOut(100);
        $('.hide-body2').slideUp(200);        
	});
	//Start add friend
	$("#deletefriend").click(function(){
		$('.body-color').fadeIn(100);
        $('.hide-body2').slideDown(200);
	});
	//Start delete friend
	$("#addfriend").click(function(){
		$('.body-color').fadeIn(100);
        $('.hide-body').slideDown(200);
	});
	//Send a message to a friend
	$("#submitmsg").click(function(){
		var clientmsg = $("#usermsg").val();
		if(friendseleted==""){
			alert("You should select a friend to start chatting!");
		}else{
			$.post("post.php", {text: clientmsg,friend: friendseleted});
			$("#usermsg").attr("value", "");
		}
		StandardTaxRate()
		return false;
	});
	setInterval (StandardTaxRate, 2500);

	//Periodly check if there is new message coming
	function StandardTaxRate()
	{
		if(friendseleted!=""){
			$(".chatbox").html("");
		}

// 		$(".radiolabel").css("background-color","yellow");
		$.ajax({
            url:'getUsername.php',
            cache:false,
            success:function(data){
            	username=data;
            	
            }
        });
		
	    $.ajax({
	        url: "log.xml",
	        dataType: 'xml',
	        error: function(xml)
	        {
	            alert("Can't find log file!");
	        },
	        success: function(xml)
	        {
		        var counter=0;
	        	var chatBoxContent="";
	            $(xml).find("log").each(function()
	            {
		            var from = $(this).attr("from");
		            var to = $(this).attr("to");
		            var time = $(this).attr("time");
		            var msg = $(this).attr("msg");
		            if(username==to&&friendseleted==from){
						chatBoxContent+="<div class=\"wraper1\"><div style=\"height:10px;\" ></div><div style=\"text-align: left;padding-left:20px\"><div class=\"bubble\"><div class=\"content\">"+time+"  "+from+"<br>"+msg+"</div></div></div></div>";
		            }else if(username==from&&friendseleted==to){
						chatBoxContent+="<div class=\"wraper2\"><div style=\"height:10px;\" ></div><div style=\"text-align: right;padding-right:20px\"><div class=\"bubble\"><div class=\"content\">"+time+"  "+from+"<br>"+msg+"</div></div></div></div>";
						
					}
	            });
	    	    $(".chatbox").html(chatBoxContent);
	    	    var div = document.getElementById('chatbox');
	    	    div.scrollTop = div.scrollHeight;
//                 $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); 
	        }
	    });

// 	    $.ajax({
// 	        url: "usser.xml",
// 	        dataType: 'xml',
// 	        error: function(xml)
// 	        {
// 	            alert("Can't find users file!");
// 	        },
// 	        success: function(xml)
// 	        {
// 		        var counter=0;
// 	        	var chatBoxContent="";
// 	            $(xml).find("user").each(function()
// 	            {
// 		            var nm = $(this).attr("newMessage");
// 		            var arr = new Array();
// 		            arr = nm.split(',');
// // 		            $message=explode(",", $(this).attr("newMessage"));
// 		    		$count=1;
// 		    		foreach($singleFriend as $printFriend){
// 		    			echo '<div><input type="radio" name="radio" id="radio'.$count.'" class="radio" value="'.$printFriend.'"/><label class="radiolabel" for="radio'.$count.'">'.$printFriend.'</label></div>'; 
// 		    		$count+=1;
// 		    		}
		    		
// 		            var to = $(this).attr("to");
// 		            var time = $(this).attr("time");
// 		            var msg = $(this).attr("msg");
// 		            if(username==to&&friendseleted==from){
// 						chatBoxContent+="<div class=\"wraper1\"><div style=\"height:10px;\" ></div><div style=\"text-align: left;padding-left:20px\"><div class=\"bubble\"><div class=\"content\">"+time+"  "+from+"<br>"+msg+"</div></div></div></div>";
// 		            }else if(username==from&&friendseleted==to){
// 						chatBoxContent+="<div class=\"wraper2\"><div style=\"height:10px;\" ></div><div style=\"text-align: right;padding-right:20px\"><div class=\"bubble\"><div class=\"content\">"+time+"  "+from+"<br>"+msg+"</div></div></div></div>";
						
// 					}
// 	            });
// 	    	    $(".chatbox").html(chatBoxContent);
// 	    	    var div = document.getElementById('chatbox');
// 	    	    div.scrollTop = div.scrollHeight;
// //                 $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); 
// 	        }
// 	    });
	    
	}
});
</script>
<?php
}else{
	echo '<script>alert("Your username is already logon!");</script>';
	loginForm();
}
?>
</body>
</html>