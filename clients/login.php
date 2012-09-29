<?php
include("../include/session.php");
if($session->logged_in){
	header("Location: main.php");

}
else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Login | <? echo $session->client_web_name ?></title>
    <link href="css/960.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/reset.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/text.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/login.css" rel="stylesheet" type="text/css" media="all" />
</head>

<body>
<div class="container_16">
  <div class="grid_6 prefix_5 suffix_5">
   	  <h1><a href="gan-web.gr/clients" target="_blank" style="color: #FFFFFF">Client Administration - Login</a></h1>
    	<div id="login">
    	  <p class="tip">Type Username and Password to Login into Gan Clients!</p>
               
    	  <form id="form1" name="form1"  action="../process.php" method="POST">
    	    <p>
    	      <label><strong>Username</strong>
				<input type="text" name="user" class="inputText" maxlength="30" value="<? echo $form->value("user"); ?>"/><p class="error"><? echo $form->error("user"); ?></p>
    	      </label>
  	      </p>
    	    <p>
    	      <label><strong>Password</strong>
			  <input type="password" name="pass" class="inputText" maxlength="30" value="<? echo $form->value("pass"); ?>"/><p class="error"><? echo $form->error("pass"); ?></p>
  	        </label>
    	    </p>
			
			<input type="hidden" name="client_sublogin"  value="1"/>
			<input type="submit" class="black_button" value="Login"/>
    	  </form>
          <?
}?>
		  <br clear="all" />
    	</div> 
  </div>
</div>

</body>
</html>
