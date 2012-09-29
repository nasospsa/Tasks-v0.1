<?php
include("include/session.php");
include("include/notifications.php");
$selectedtask = $_GET["t"];
if (!$session->logged_in) {
    header("Location: login.php");
}
/*
  else if ($selectedtask=='' or !isset($session->avail_tasks_straight[$selectedtask])){
  header("Location: $session->referrer");
  }
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="stylesheet" type="text/css" href="styling/style.css"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Tasks Main Page |<? echo $session->web_name ?></title>
    </head>

    <body>
        <div id="wrapper">
            <div id="top">
                <div id="logo">
                    <span>Gan Web Internet Solutions</span><br/><span>Project Management Software</span>
                </div>
                <div id="info">
                    
                    <div id="userInfo">					
                        
                            <img id="avatar" src="<? echo ($session->userinfo[avatar] == "") ? GENERIC_AVATAR : $session->userinfo["avatar"] ?>" width="32"/>
                            <span id="userNav">Welcome, <strong><? echo $session->username ?>!</strong></span>
                            <a id="userMenuLink" class="icontopMenu">
                                <img src="styling/images/gear.png" width="18"/>
                            </a>
                            <a id="notificationsLink" class="icontopMenu">
                                <img src="styling/images/thunder.png" width="18"/>
                            </a>
                            <div class="clr"></div>
                        						
                    </div>
                    <div class="clr"></div>
                    
                    <div class="userMenu">
                        <ul> 
                            <? if ($session->isSuperAdmin()) { ?>
                            <li><a href="admin/admin.php">Admin Center</a></li>
                            <? } ?>
                            <li><a href="useredit.php">Edit Profile</a></li>
                            <li><a id="logout" href="process.php">Logout</a></li>
                            <li class="no_border"><h4>System Information</h4>
                                <p>Last login: <? echo $session->time_last ?><br/>
                                    Συνολικά Μέλη: <? echo $database->getNumMembers() ?><br/>
                                    Συν/μένοι Χρήστες: <span id="current_users"><? echo $database->num_active_users ?></span><br/>
                                    Επισκέπτες: <span id="current_guests"><? echo $database->num_active_guests ?></span></p>
                            </li>
                        </ul>
                    </div>
                    
                    <div id="notifications">
                        <?  if (count($notifications->getUserNotifications(SERIALIZED))>0): ?>
                        <ul class="notificationsList">
                        <?  foreach ($notifications->getUserNotifications(SERIALIZED) as $notification) {
                                echo "<li class=\"$notification[class]\">$notification[message]</li>";
                            }?>
                        <?  endif; ?>
                        </ul>
                    </div>
                </div>
                
                <div class="clr"></div>
            </div>

            <div id="left">
                <div class="content-box">
                    <div class="content-box-header side-header">
                        <h3>User Panel</h3>
                    </div>
                    <div class="content-box-content upanel">
                        <div id="user">
                            Έχετε εισέλθει ως:<br />
                            <span id="username"><? echo $session->username ?></span><a href="useredit.php">(edit)</a>
                            <img src="<? echo ($session->userinfo[avatar] == "") ? GENERIC_AVATAR : $session->userinfo["avatar"] ?>" width="160"  />
                            <a id="logout" href="process.php">Logout</a><br />
                            Last login:<br />
                            <? echo $session->time_last ?><br />
                            <a href="#">History</a>
                        </div>
                        <div id="selectTask">
                            <? $task = $session->avail_tasks_straight[$selectedtask];
                            echo $task[project_name]; ?>
                            :<br />
                            <?php
                            $tasks = $session->avail_tasks[$task[project_name]];
                            if (count($tasks) > 1) {
                                ?>
                                <select name="task" id="task_select">
                                    <?
                                    foreach ($tasks as $task_option) {
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
                                } else if (count($tasks) == 1) {
                                    //reset($projects_array_tasks);
                                    echo "<input disabled type=\"text\" value=\"" . $tasks[key($tasks)][title] . "\" />";
                                } else {
                                    echo "<strong>No Tasks</strong>";
                                }
                                ?>
                        </div>
                        <div id="homeAllTasksLink">
                            <a href="/">Tasks</a>
                            <ul id="tasksMenu">
                                <? if ($session->isWokring()) { ?>
                                <li class="all"><a href="main.php?v=All">All</a></li>
                                <li class="assigned"><a href="main.php?v=Assigned">Assigned</a></li>
                                <li class="authored"><a href="main.php?v=Authored">Authored</a></li>
                                <? } else { ?>
                                <li class="all"><a href="main.php?v=All">Current</a></li>
                                <? } ?>
                                <li class="completed"><a href="main.php?v=Completed">Completed</a></li>
                            </ul>
                        </div>
                        <? if ($session->isPA()) { ?>
                        <div id="clientsLink">
                            <a href="/clients/">Clients</a>
                        </div>
                        <div id="statsLink">
                            <a href="stats.php">Stats</a>
                        </div>
                        <? } ?>
                        <div id="timesheetsLink">
                            <a href="timesheets.php">Timesheets</a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="center">
                        <?
                        if (isset($_SESSION['success'])) {
                            /* Successful Message */
                            if ($_SESSION['success']) {
                                ?>
                        <div class="success"> <span class="ico"></span> <span class="message success_txt">
                        <? echo $_SESSION['header'] ?>
                            </span> </div>
                    <?
                    }
                    /* Failure Message */ else {
                        ?>
                        <div class="failure"> <span class="ico"></span> <span class="message fail_txt"><? echo $_SESSION['header'] ?></span> </div>
                        <?
                    }
                    unset($_SESSION['success']);
                    unset($_SESSION['header']);
                }
                ?>
                <div class="content-box">
                    <div class="content-box-header">
                        <h2><? echo $task[title] ?></h2>
                        <h3 class="sub-header"><a href="main.php?p=<? echo $task[project_name] ?>"><? echo $task[project_name] ?></a></h3>
<?
if (in_array($selectedtask, $session->getEditTasks_by_ID())) {
    ?>
                            <span class="edit_task_btn"><a href="editTask.php?t=<? echo $task[id] ?>"><img src="styling/images/spanner_48.png" style="width:25px;height:25px;" /></a></span>
                        <? } ?>
                    </div>
                    <div class="content-box-content">
                        <div id="taskInfoWrapper">
                            <table id="tblTask_Details">
                                <tr>
                                    <td>Περιγραφή:</td><td><? echo $task[description] ?><br /></td>
                                </tr>
                                <tr>
                                    <td>Δημιουργήθηκε:</td><td><? echo date("d/m/'y, H:i:s", strtotime($task[time_created])) ?></td>
                                </tr>
                                <tr>
                                    <td>Κατάσταση:</td><td><? echo $task[status] ?></td>
                                </tr>
                                <tr>
                                    <td>Προτεραιότητα:</td><td><? echo $task[priority] ?></td>
                                </tr>
                                <tr>
                                    <td>Υπεύθυνοι:</td><td><? echo $task[assigned_list] ?></td>
                                </tr>
                                <? if (isset ($session->timers_per_task[$task[id]])){ ?>
                                <tr>
                                    <td>Χρόνος Εργασίας:</td>
                                    <td><?
                                    foreach($session->timers_per_task[$task[id]] as $user=>$duration){
                                        $total += $duration;
                                        $users[] = $user;
                                    }
                                    $users = implode(", ", $users);
                                    echo ($total<3600)?round($total/60)." mins":round($total/3600)." hours";?>
                                        <span class="timed_users"> (<? echo $users ?>)</span>
                                    </td>
                                </tr>
                                <? } ?>
                            </table>
                            <div id="addCustomTimerWrapper">
                                <a href="#!" id="addCustomTimerLink">Add Custom Timer</a>
                                <div id="addCustomTimerDetailsWrapper" style="display:<? echo $form->error("vis_add_timer")?"block":"none" ?>">
                                    <form name="customTimer" action="process.php" method="POST">
                                        <label for="timerMins">Mins:</label>
                                        <input type="text" id="addTimerMinutes" name="timerMins" value="<? echo $form->value("timerMins") ?>" />
                                        <? echo $form->error("mins") ?>
                                        <input type="hidden" name="tsk" value="<? echo $task[id] ?>" />
                                        <input type="hidden" name="subAddCustomTimer" value="1" />
                                        <input id="btnAddTimer" class="green_btn confirm" name="subAddTimer" type="submit" value="Submit" />
                                    </form>
                                </div>
                            </div>
                            <div id="divUpdateStatusButtons">
                                <form name="task_sts" action="process.php" method="post">
                                    <input type="hidden" name="tsk" value="<? echo $task[id] ?>" />
<? if ($task[status] != 'Completed') { ?>
                                        <input class="blue_btn stsTaskPage" name="sub_upd_sts"  type="submit" value="Update Status" />
                                    <? }if ($task[status] != 'Awaiting Confirmation') { ?>
                                        <input class="red_btn stsTaskPage" name="sub_down_sts" type="submit" value="Downgrade" />
                                    <? } ?>
                                </form>
                            </div>
                            <div style="clear:both"></div>
                        </div>

                        <strong>Comments:</strong> 

                        <!-- Ftiaksimo stylinf mono loipon edw kai energopoiisi apo process to database>-->
                        <div id="form_div">
                            <div class="profile">
                                <img src="<? echo $session->userinfo["avatar"] ?>" width="75" />
                            </div>
                            <div class="comment_details">
                                <form action="#!" method="post">
                                    <textarea id="comment" cols="65"></textarea>
                                    <br />
                                    <input type="submit" class="submit_comment" value=" Submit Comment" />
                                    <div id="flash"></div>
                                    <input type="hidden" id="task_id" value="<? echo $task[id] ?>" />
                                    <input type="hidden" id="subnewcomment" value="1" />
                                </form>
                            </div>
                        </div>
                        <ol id="comments_list">
<?php
//$post_id value comes from the POSTS table
$comments = $session->commentsoftask($selectedtask);

//echo var_dump($comments);
if (count($comments) > 0) {
    foreach ($comments as $comment) {
        $avatar = $session->getUserAvatar($comment[commentator]);
        ?>
                                    <li>
                                        <div class="profile">
                                            <img src="<? echo ($avatar == "") ? GENERIC_AVATAR : $avatar ?>" width="75" />
                                        </div>
                                        <div class="comment_details">
                                            <div><? echo $comment[text_comment] ?></div>
                                            <div><? echo $comment[commentator] ?> | <? echo $session->nicetime($comment[timestamp]) ?></div>
                                        </div>
                                    </li>
        <?
    }
}
?>
                        </ol>
                    </div>
                </div>
            </div>
            <div id="right">
                <div class="content-box">
                    <div class="content-box-header side-header">
                        <h3><? echo "Latest Tasks"; ?></h3>
                    </div>
                    <div class="content-box-content">
                        <ol class="latestTasksList">
                            <?
                            $latest_tasks = $database->getLatestTasks_Pro($task[project_name]);
                            foreach ($latest_tasks as $task_l) { ?>
                            <li><a href="task.php?t=<? echo $task_l[id] ?>"><? echo $task_l[title] ?></a></li>        
                            <? } ?>
                        </ol>
                    </div>
                </div>
                
                <?php
				$projects = $session->avail_projects;
				if (count($projects) > 1) {
				?>
				<div class="content-box">
                    <div class="content-box-header side-header">
                        <h3>Projects</h3>
                    </div>
                    <div class="content-box-content">
                        <select name="proj" id="proj_select" >
                            <option value="">All</option>
                        <?
                        foreach ($projects as $project => $tasks) {
                            echo "<option ";
                            if ($project == $task[project_name]) {
                                echo "selected ";
                            }
                            echo " value=\"$project\">$project (".count($tasks).")</option>";
                        }
                        unset($project);
                        unset($tasks);
                        unset($projects);
                        ?>
                        </select>
                    </div>
                </div>
                <?
                } ?>
                                
                <div class="content-box">
                    <div class="content-box-header side-header">
                        <h3>Project Info</h3>
                    </div>
                    <div class="content-box-content clock-tab">
                        <? $foundPro = $session->avail_projects[$task[project_name]]; ?>
                        <div id="project_info">
                            <div id="Title"> Τίτλος:<br />
                                <? echo $foundPro[title] ?> </div>
                            <? if (isset ($foundPro[URL])): ?>
                            <div id="URL"> URL:<br />
                                <? echo $foundPro[URL] ?> </div>
                            <? endif;
                            if (isset ($foundPro[domain_exp])): ?>
                            <div id="domainEXP"> Domain Expire:<br />
                                <? echo $foundPro[domain_exp] ?> </div>
                            <? endif;
                            if (isset ($foundPro[host_exp])): ?>
                            <div id="hostEXP"> Host Expire:<br />
                                <? echo $foundPro[host_exp] ?> </div>
                            <? endif;
                            if (isset ($foundPro[description])): ?>
                            <div id="pro_desc"> Descriptipn:<br />
                                <? echo $foundPro[description] ?> </div>
                            <? endif;
                            if (isset ($foundPro[author])): ?>
                            <div id="pro_author"> Author:<br />
                                <? echo $foundPro[author] ?> </div>
                            <? endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div style="clear:both"></div>
        </div>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/jquery.corner.js"></script>
        <script type="text/javascript" src="js/jdpicker_1.0.3/jquery.jdpicker.js"></script>
        <script type="text/javascript" src="js/document.js"></script>
    </body>
</html>
