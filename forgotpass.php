<?php
include("include/session.php");
/**
 * ForgotPass.php
 *
 * This page is for those users who have forgotten their
 * password and want to have a new password generated for
 * them and sent to the email address attached to their
 * account in the database. The new password is not
 * displayed on the website for security purposes.
 *
 * Note: If your server is not properly setup to send
 * mail, then this page is essentially useless and it
 * would be better to not even link to this page from
 * your website.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 26, 2004
 */
if($session->logged_in){
	header("Location: main.php");
	
}
else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="styling/style.css"/>
<title>Forgot Pass | <? echo $session->web_name ?></title>
</head>
<body>
<div id="wrapper">
    <div id="main_one">
        <div class="content-box">
            
<?
/**
 * Forgot Password form has been submitted and no errors
 * were found with the form (the username is in the database)
 */
if(isset($_SESSION['forgotpass'])){
   /**
    * New password was generated for user and sent to user's
    * email address.
    */
   if($_SESSION['forgotpass']){?>
      <div class="content-box-header">
      <h2>New Password Generated</h2></div>
      <div class="content-box-content">
      	<p>There was an error sending you the email with the new password,<br />
        so your password has not been changed. <a href=\"main.php\">Main</a></p>
      </div>
   <?
   }
   /**
    * Email could not be sent, therefore password was not
    * edited in the database.
    */
   else{?>
       <div class="content-box-header">
       <h2>New Password Failure</h2></div>
       <div class="content-box-content">
       <p>Your new password has been generated and sent to the email<br />
       associated with your account. <a href=\"main.php\">Main</a></p>
       </div>
   <?
   }
       
   unset($_SESSION['forgotpass']);
}
else{

/**
 * Forgot password form is displayed, if error found
 * it is displayed.
 */
?>

<div class="content-box-header">
       <h2>Forgot Password</h2>
</div>
<div class="content-box-content">
       
        A new password will be generated for you and sent to the email address<br>
        associated with your account, all you have to do is enter your
        username.<br><br>
        <? echo $form->error("user"); ?>
        <form action="process.php" method="POST">
        <b>Username:</b> <input type="text" name="user" maxlength="30" value="<? echo $form->value("user"); ?>">
        <input type="hidden" name="subforgot" value="1">
        <input type="submit" value="Get New Password">
        </form>
</div>
<?
}
}
?>

</div>
</div>
</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.corner.js"></script>
<script type="text/javascript" src="js/document.js"></script>
</body>
</html>
