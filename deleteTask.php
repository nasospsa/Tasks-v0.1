<?php
include("include/session.php");
$selected_task=$_GET["t"];
$edit_taks = $session->getEditTasks_by_ID();
if (is_array($edit_taks)){
	if (in_array($selected_task,$edit_taks)) $found=true;
}

if((!$session->isAdmin() and !$session->isPA()) or !$session->logged_in or !$found){
   header("Location: main.php");
}

$task=$session->avail_tasks_straight[$selected_task];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Διαγραφή του task "<? echo $task[title] ?>" | <? echo $session->web_name ?></title>
<link rel="stylesheet" type="text/css" href="styling/style.css"/>
<link rel="stylesheet" type="text/css" href="js/jdpicker_1.0.3/jdpicker.css"/>
</head>

<body>
<div id="wrapper">
	<div id="main_one">
        <div class="deleteproject">
            <form name="deleteTask_form" action="process.php" method="POST">
                <h1>Διαγραφή <? echo $task[title] ?></h1>
                <div class="content-left">
                <p>Μαζί με το task, θα διαγραφούν όλα τα σχόλια και οι αναθέσεις,<br />
                είστε σίγουροι?</p>
                
                <table class="newproject_tbl">
                <col />
                <col />
                <tr>
                    <td style="text-align:right;">Τίτλος Task:</td>
                    <td><input type="text" disabled="disabled" name="title" value="<? echo $task[title] ?>" /></td>
                    <td><? echo $form->error("title"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Περιγραφή:</td>
                    <td><textarea disabled="disabled" rows="5" cols="40"><? echo $task[description] ?></textarea></td>
                    <td><? echo $form->error("desc"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Δημιουργήθηκε:</td>
                    <td><input disabled="disabled" type="text" value="<? echo $task[time_created] ?>" /></td>
                    <td><? echo $form->error("time_created"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Συγγραφέας:</td>
                    <td><input disabled="disabled" type="text" value="<? echo $task[author] ?>" /></td>
                    <td><? echo $form->error("author"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Project:</td>
                    <td><input disabled="disabled" type="text" value="<? echo $task[project_name] ?>" /></td>
                    <td><? echo $form->error("project"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Status:</td>
                    <td><input disabled="disabled" type="text" value="<? echo $task[status] ?>" /></td>
                    <td><? echo $form->error("status"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Προτεραιότητα:</td>
                    <td><input disabled="disabled" type="text" value="<? echo $task[priority] ?>" /></td>
                    <td><? echo $form->error("priority"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Υπεύθυνοι:</td>
                    <td><input disabled="disabled" type="text" value="<? echo $task[assigned_list] ?>" /></td>
                    <td><? echo $form->error("assignements"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Αριθμός Σχολίων:</td>
                    <td><input disabled="disabled" type="text" value="<? echo count($session->commentsoftask($selected_task)) ?>" /></td>
                    <td><? echo $form->error("priority"); ?></td>
                </tr>
                </table>
                <input type="hidden" name="subdeletetask" value="1" />
                <input type="hidden" name="id" value="<? echo $selected_task ?>" />
                <input type="hidden" name="task_title" value="<? echo $task[title] ?>" />
                <input type="hidden" name="project_title" value="<? echo $task[project_name] ?>" />
                
            	</div>
      			<div class="content-left">
                    <div class="delete final">
                        <input class="confirm" type="submit" value="" />
                        <br />
                        <span>Διαγραφή του Task</span>
                    </div>
                    <div>
                        <a class="return_btn" href="editTask.php?t=<? echo $selected_task ?>">Επιστροφή</a>
                    </div>
            	</div>
            
            	<div style="clear:both"></div>
            
        	</form>
        </div>
        
        
    </div>
    <div style="clear:both"></div>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.corner.js"></script>
<script type="text/javascript" src="js/jdpicker_1.0.3/jquery.jdpicker.js"></script>
<script type="text/javascript" src="js/document.js"></script>
</body>
</html>