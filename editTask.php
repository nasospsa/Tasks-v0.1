<?php
include("include/session.php");
$selectedtask=$_GET["t"];
if (is_array($session->getEditTasks_by_ID())){
	if (in_array($selectedtask,$session->getEditTasks_by_ID())) $found=true;
}
else $found=false;

if(!$session->logged_in or !$found){
   header("Location: main.php");
}
$task=$session->avail_tasks_straight[$selectedtask];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Επεξεργασία του task "<? echo $task[title] ?>" | <? echo $session->web_name ?></title>
<link rel="stylesheet" type="text/css" href="styling/style.css"/>
<link rel="stylesheet" type="text/css" href="js/jdpicker_1.0.3/jdpicker.css"/>
</head>

<body>

<div id="wrapper">
    <div id="mainmenu">
        <ul>
            <? if ($session->isWokring()){ ?>
            <li><a href="main.php?v=myTasks">My Tasks</a></li>
            <li id="current"><a href="main.php?v=All">All</a></li>
            <li><a href="main.php?v=Authored">Authored</a></li>
            <? }
            else {?>
            <li><a href="main.php?v=All">Current</a></li>
            <? }?>
            <li><a href="main.php?v=Completed">Completed</a></li>
            <? if($session->isSuperAdmin()){?>
            <li><a href="admin/admin.php">Admin Center</a></li>
            <? }?>
        </ul>
    </div>
    
	<div id="left">
        <div class="content-box">
            <div class="content-box-header side-header"><h3>User Panel</h3></div>
            <div class="content-box-content upanel">
                <div id="user">
                    Έχετε εισέλθει ως:<br />
                    <span id="username"><? echo $session->username ?></span><a href="useredit.php">(edit)</a>,<br />
                    <a href="process.php">logout</a><br /><br />
                    Last login:<br />
                    <? echo $session->time_last ?><br />
                    <a href="#">History</a>
                </div>
                <div id="selectTask">
                    <?
                    echo $task[project_name]; ?> :<br />
                    <?php
                        
                        $tasks = $session->avail_tasks[$task[project_name]];
                        if (count($tasks)>1){?>
                            <select name="task" id="task_select">
                            <?
                            foreach ($tasks as $task_option){
                                echo "<option ";
                                if ($task_option[id] == $selectedtask) {
                                    echo "selected ";
                                }
                                echo " value=\"$task_option[id]\">$task_option[title]</option>";
                                    
                                
                            }
                            unset($task_option);
                            unset($tasks);
                            ?>
                            </select>
                        <?
                        }
                        else if (count($tasks)==1) {
                            //reset($projects_array_tasks);
                            echo "<input disabled type=\"text\" value=\"".$tasks[0][title]."\" />";
                        }
                        else {
                            echo "<strong>No Tasks</strong>";
                        }
                    ?>	   
                </div>
                <div id="main_menu">
                    <a href="main.php">Back to Main Menu</a>
                </div>
                <?
                if($session->isSuperAdmin()){
                    ?>
                    <div id="admincenter">
                        <a href="admin/admin.php">Admin Center</a>
                    </div>
                    <?
                }
                ?>
            </div>
        </div>
    </div>

    <div id="center">
        <?
				if(isset($_SESSION['success'])){
				   /* Successful Message */
				   if ($_SESSION['success']) {?>
                   <div class="success">
                   	<span class="ico"></span>
                    <span class="message success_txt"><?  echo $_SESSION['header'] ?></span>
                   </div>
                   <? }
				   /* Failure Message*/
                   else  {?>
                   <div class="failure">
                   	<span class="ico"></span>
                    <span class="message fail_txt"><? echo $_SESSION['header'] ?></span>  
                   </div> 
				   <? 
				   }
				   unset($_SESSION['success']);
				   unset($_SESSION['header']);
				}
				?>
        
        <div class="content-box">
            <div class="content-box-header">
                <h2>Επεξεργασία <? echo $task[title]; ?></h2></div>
            <div class="content-box-content">
                <div>
                    <form name="editTask_form" action="process.php" method="POST">
                    <table class="newproject_tbl">
                    <col />
                    <col />
                    <tr>
                        <td style="text-align:right;">Τίτλος Task:</td>
                        <td><input type="text" name="task_title" value="<? echo $task[title] ?>" /></td>
                        <td><? echo $form->error("title"); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Περιγραφή:</td>
                        <td><textarea name="task_desc" rows="5" cols="40"><? echo $task[description] ?></textarea></td>
                        <td><? echo $form->error("desc"); ?></td>
                    </tr>
                   
                    <tr>
                        <td style="text-align:right;" colspan="2"><input class="green_btn confirm" type="submit" value="Αποθήκευση" /></td>
                        <td></td>
                    </tr>
                    </table>
                    <input type="hidden" name="subedittask" value="1" />
                    <input type="hidden" name="id" value="<? echo $task[id] ?>" />
                    <input type="hidden" name="project" value="<? echo $task[project_name] ?>" />
                    </form>
                </div>
                <? if ($session->isAdmin() or $session->isPA()){ ?>
                <div class="delete">
                    <a href="deleteTask.php?t=<? echo $task[id] ?>">
                        <img src="styling/images/x_button.png" width="100" height="100" /><br />
                        <span>Διαγραφή του Task</span>
                    </a>
                </div>
                <? }
				$avail_users=$session->getUsers_on_Project($task[project_name],SERIALIZED_NONCLIENT);
				$assigned=explode(", ",$task[assigned_list]);
				$avail_users[0]=ltrim($avail_users[0],"Self (");
	   			$avail_users[0]=rtrim($avail_users[0],")");
				$avail_users=$session->array_not_in_array($avail_users,$assigned);
				
				if ($key=array_search($session->username,$avail_users)){
                                    $avail_users[$key]="Self (".$avail_users[$key].")";
				}
				$key=-1;
				$key=array_search($session->username,$assigned);
				if ($key>-1){
                                    $assigned[$key]="Self (".$assigned[$key].")";
				}
				if (count($avail_users)>0){?>
                <div id="available_users_div">
                <? //var_dump($database->getProjectAdminTasks_ID($session->username)) ?>
                    <form action="process.php" method="POST">
                    <table>
                    <? $counter=0;
                    foreach ($avail_users as $user){
					$valuser=ltrim($user,"Self (");
					$valuser=rtrim($valuser,")");
                    $counter++;?>
                    <tr>
                    <td><? echo $user ?></td><td><input type="checkbox" name="add<? echo $counter ?>" value="<? echo $valuser ?>" /></td>
                    </tr>
                    <? }?>
                    </table>
                    <input type="hidden" name="task" value="<? echo $task[id]?>" />
                    <input type="hidden" name="sub_addto_task" value="1" />
                    <input type="submit" class="blue_btn confirm" value="Καταχώρηση Νέων Χρηστών" />
                    </form>
                </div>
                <? } 
                
                if (count($assigned)>0){?>
                <div id="remove_users_div">
                    <form action="process.php" method="POST">
                    <table>
                    <? $counter=0;
                    foreach ($assigned as $user){
					$valuser=ltrim($user,"Self (");
					$valuser=rtrim($valuser,")");
                    $counter++;?>
                    <td><? echo $user ?></td><td><input type="checkbox" name="remove<? echo $counter ?>" value="<? echo $valuser ?>" /></td>
                    </tr>
                    <? }?>
                    </table>
                    <input type="hidden" name="task" value="<? echo $task[id]?>" />
                    <input type="hidden" name="sub_removefrom_task" value="1"  />
                    <input type="submit" class="red_btn confirm" value="Διαγραφή Χρηστών" />
                    </form>
                </div>
                <? } ?>
                <br /><br />
                <a class="assign_more" href="editProject.php?p=<? echo $task[project_name] ?>">Assign Project to more..</a>
                <div style="clear:both"></div>
           </div>
		</div>
    </div>
    
    <div id="right">
        <div class="content-box">
            <div class="content-box-header side-header">
            	<h3>Gan-Web</h3>
            </div>
            <div class="content-box-content clock-tab">
                <script type="text/javascript">var clocksize='150px';</script>
                <script type="text/javascript" src="http://gheos.net/js/clock.js"></script>
                <br />
                <div id="miniclock"><noscript>Enable JS to see clock</noscript></div>
                <script type="text/javascript" src="js/date-clock.js"></script>
            </div>
        </div> 
    </div>
    
    <div style="clear:both"></div>
</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.lavalamp.js"></script>
<script type="text/javascript" src="js/jquery.corner.js"></script>
<script type="text/javascript" src="js/jdpicker_1.0.3/jquery.jdpicker.js"></script>
<script type="text/javascript" src="js/document.js"></script>
</body>
</html>