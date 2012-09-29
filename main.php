<?php
include("include/session.php");
include("include/notifications.php");
/* @var $session Session */

if (!$session->logged_in) {
    header("Location: login.php");
}
$body_class = "";
if ($_GET["v"] == "All" or $_GET["v"] == "") {
    $projects = $session->avail_tasks;
    $pageTitle = "Αρχική";
    $menuCurrent["All"] = "current";
} else if ($_GET["v"] == "Assigned") {
    $projects = $session->getAssigned_Tasks_by_Project();
    $pageTitle = "Τα Task μου";
    $menuCurrent["Assigned"] = "current";
    $body_class = "mytasks";
    $styling = ".content-box-content {
		background-color:#FFC7B2;
	}
	#blob {
		background: url('styling/images/bg-lavamenu-red.png') bottom left repeat-x transparent;
		border: 1px solid #A00;
	}";
} else if ($_GET["v"] == "Authored") {
    $projects = $session->getAuthored_Tasks_by_Project();
    $pageTitle = "Αυτά που έχω δημιουργήσει";
    $menuCurrent["Authored"] = "current";
    $body_class = "authored";
    $styling = ".content-box-content {
		background-color:#b2c6ff;
	}
	#blob {
		background: url('styling/images/bg-lavamenu-blue.png') bottom left repeat-x transparent;
		border: 1px solid #17439b;
	}";
} else if ($_GET["v"] == "Completed") {
    $projects = $session->getCompleted_Tasks_by_Project();
    $pageTitle = "Ολοκληρωμένα";
    $menuCurrent["Completed"] = "current";
    $body_class = "completed";
    $styling = ".content-box-content {
		background-color:#ccffb2;
	}
	#blob {
		background: url('styling/images/bg-lavamenu-green.png') bottom left repeat-x transparent;
		border: 1px solid #269b4a;
	}";
} else {
    header("Location: main.php");
}
?>
<!-- saved from url=(0022)http://internet.e-mail -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <link rel="stylesheet" type="text/css" href="styling/style.css"/>
        <link rel="stylesheet" type="text/css" href="styling/jquery.autocomplete.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo $pageTitle . " | " . $session->web_name ?></title>
<? if (isset($styling))
    echo "<style>" . $styling . "</style>"; ?>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/jquery.tools.tooltip.min.js"></script>
    </head>

    <body class="<? echo $body_class ?>">
        <pre><?
        //var_dump($database->getActiveProjects());
        ?></pre>
        <div id="wrapper">
            <div id="top">
                <div id="logo">
                    <span>Gan Web Internet Solutions</span><br/><span>Project Management Software</span>
                </div>
                <div id="info">
                    
                    <div id="userInfo">					
                        <a ref="#!" class="userInfo_button">
                            <img id="avatar" src="<? echo ($session->userinfo[avatar] == "") ? GENERIC_AVATAR : $session->userinfo["avatar"] ?>" width="32"/>
                            <span id="userNav">Welcome, <strong><? echo $session->username ?>!</strong>
                                <img id="userSet" src="styling/images/settings.png" width="17"/>
                            </span>
                            <div class="clr"></div>
                        </a>							
                    </div>
                    <div id="notificationsWrapper">
                        <a id="notificationsBtn">
                            <img id="userNotificationsIMG" src="styling/images/activities.png" height="32" />
                        </a>
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
            <!--
            <div id="mainmenu">
                <ul>
                    <? if ($session->isWokring()) { ?>
                    <li id="<? echo $menuCurrent["Assigned"]; ?>"><a href="main.php?v=Assigned">My Tasks</a></li>
                    <li id="<? echo $menuCurrent["All"]; ?>"><a href="main.php?v=All">All</a></li>
                    <li id="<? echo $menuCurrent["Authored"]; ?>"><a href="main.php?v=Authored">Authored</a></li>
                    <? } else { ?>
                    <li id="<? echo $menuCurrent["All"]; ?>"><a href="main.php?v=All">Current</a></li>
                    <? } ?>
                    <li id="<? echo $menuCurrent["Completed"]; ?>"><a href="main.php?v=Completed">Completed</a></li>

                </ul>
            </div>
            -->

            <div id="left">
                <div class="content-box">
                    <div class="content-box-header side-header"><h3>User Panel</h3></div>
                    <div class="content-box-content upanel">
                        <div id="user">
                            <span id="username"><? echo $session->username ?></span><br/>
                            <span id="userlevel">(<? echo $session->userlevel_in_words ?>)</span>
                            <img src="<? echo ($session->userinfo[avatar] == "") ? GENERIC_AVATAR : $session->userinfo["avatar"] ?>" width="160"  />
                            <div class="userSocial">
<? if ($session->userinfo[facebook]) { ?>
                                    <div class="userFacebook">
                                        <a href="http://www.facebook.com/<? echo $session->userinfo[facebook] ?>" target="_blank">
                                            <img src="styling/images/faceBook_32.png" alt="<? echo $session->username ?> Facebook Profile"/>
                                        </a>
                                    </div>
                                <? } ?>
                                <? if ($session->userinfo[twitter]) { ?>
                                    <div class="userTwitter">
                                        <a href="http://www.twitter.com/#!/<? echo $session->userinfo[facebook] ?>" target="_blank">
                                            <img src="styling/images/twitter_32.png" alt="<? echo $session->username ?> Twitter Profile"/>
                                        </a>
                                    </div>
                                <? } ?>
                                <div class="clr"></div>
<? if ($session->userinfo[skype]) { ?>
                                    <div class="userSkype">
                                        <script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
                                        <a  href="skype:<? echo $session->userinfo[skype] ?>?call"><img src="http://mystatus.skype.com/balloon/<? echo $session->userinfo[skype] ?>" style="border: none;"  width="150" height="60" alt="My status" /></a>			
                                    </div>
                                <? } ?>
                            </div>
                            <div id="selectPr">
                                Project:<br />
<?php
//echo key($projects_array_tasks);
$selectedPro = $_GET["p"];
if (count($projects) > 1) {
    ?>
                                    <select name="proj" id="proj_select" >
                                        <option value="">All</option>
                                    <?
                                    foreach ($projects as $project => $tasks) {
                                        echo "<option ";
                                        if ($project == $selectedPro) {
                                            echo "selected ";
                                        }
                                        echo " value=\"$project\">$project (".count($tasks).")</option>";
                                    }
                                    unset($project);
                                    unset($tasks);
                                    ?>
                                    </select>
                                        <?
                                    } else if (count($projects) == 1) {
                                        //reset($projects_array_tasks);
                                        echo "<input disabled type=\"text\" value=\"" . key($projects) . "\" />";
                                    } else {
                                        echo "<strong>No Project</strong>";
                                    }
                                    ?>



                                <?php
                                //$projects = $session->avail_projects;
                                ?>              

                            </div>
                            <?
                            if ($session->isAdmin()) { ?>
                            <div id="newProject">
                                <a href="addProject.php">New Project...</a>
                            </div>
                            <? } ?>
                            <div id="homeAllTasksLink">
                                <a href="/">Tasks</a>
                                <ul id="tasksMenu">
                                    <? if ($session->isWokring()) { ?>
                                    <li class="all" id="<? echo $menuCurrent["All"]; ?>"><a href="main.php?v=All">All</a></li>
                                    <li class="assigned" id="<? echo $menuCurrent["Assigned"]; ?>"><a href="main.php?v=Assigned">Assigned</a></li>
                                    <li class="authored" id="<? echo $menuCurrent["Authored"]; ?>"><a href="main.php?v=Authored">Authored</a></li>
                                    <? } else { ?>
                                    <li class="all" id="<? echo $menuCurrent["All"]; ?>"><a href="main.php?v=All">Current</a></li>
                                    <? } ?>
                                    <li class="completed" id="<? echo $menuCurrent["Completed"]; ?>"><a href="main.php?v=Completed">Completed</a></li>
                                </ul>
                            </div>
                            <?
                            if ($session->isPA()) { ?>
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
                <div class="content-box">
                    <div class="content-box-header side-header"><h3>User Notes</h3></div>
                    <div class="content-box-content"><? include("include/notes.php") ?></div>
                </div>
            </div>


            <div id="center">
                <div id="debug">
<?php
if (is_array($session->projectOrder) && is_array($projects)) {
    $projects = $session->sortArrayByArray($projects, $session->projectOrder);
}
?>
                </div>
<?
//$projects = $session->avail_tasks;
if ((count($projects) > 0)) {
    if (isset($_SESSION['success'])) {
        /* Successful Message */
        if ($_SESSION['success']) {
            ?>
                            <div class="success">
                                <span class="ico"></span>
                                <span class="message success_txt"><? echo $_SESSION['header'] ?></span>
                            </div>

                        <?
                        }
                        /* Failure Message */ else {
                            ?>
                            <div class="failure">
                                <span class="ico"></span>
                                <span class="message fail_txt"><? echo $_SESSION['header'] ?></span>
                            </div> 
        <?
        }
        unset($_SESSION['success']);
        unset($_SESSION['header']);
    }

    foreach ($projects as $project => $tasks) {
        if ($selectedPro != '') {
            if ($selectedPro != $project) {
                continue;
            } else {
                $foundPro = $session->avail_projects[$project];
            }
        }
        if (in_array($project, $session->active_projects)) $project_class="pr_active";
        else $project_class="";
        if ((count($tasks) > 0) or ($selectedPro != '')) {
            ?>
                            <div class="content-box project <? echo $project_class; ?>" id="proj_<? echo $project; ?>">
                                <div class="content-box-header">
                                    <h2>
                                        <a href="main.php?v=<? echo $_GET["v"] . "&p=" . $project ?>"><? echo $project ?></a>
                            <? if ($session->isPA()) { ?>
                                            <a class="edit_project_button" href="editProject.php?p=<? echo $project ?>"></a>
                                            <a class="credentials_project_button" href="projectInfo.php?p=<? echo $project ?>"></a>
                                <? } ?>

                                    </h2>


                                    <a href="#!" class="showProject_btn" style="display:<? echo $selectedPro != '' ? "none" : "block"; ?>"></a>
                                    <span class="span_TaskCounter">(Σύνολο Εργασιών <? echo count($tasks); ?> )</span>

                                </div>
                                <div class="content-box-content" style="display:<? echo ($selectedPro != '' || $_SESSION['task_success' . $project] || $form->error("vis_add_task" . $project) || count($projects) < 4) ? "block" : "none"; ?>">
                                        <? if (count($tasks) > 0) { ?>
                                    <table class="tasks" cellpadding="0" cellspacing="0">
                                        <col />
                                        <col style="width:120px" />
                                        <col style="width:320px" />
                                        <col style="width:100px" />
                                        <col style="width:60px" />
                                        <col style="width:100px" />
                                        <tr>
                                            <th></th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Priority</th>
                                            <th class="task_sts_label">Status</th>
                                            <th></th>
                                        </tr>
                <?
                $counter_tasks = 0;
                foreach ($tasks as $task) {
                    $counter_tasks++;
                    $row_class = $counter_tasks%2==0 ?"even":"odd";
                    //if (is_array($task[active_timers])) $row_class .= " active";
                    
                    if (isset ($session->active_tasks[$task[id]])){
                        $row_class .= " active";
                        if ($session->active_tasks[$task[id]][self_active]) $row_class .= " self_active";
                        $td_class = "active ";
                        //$row_class .= " ".$session->active_tasks[$task[id]][self_active];
                    }
                    else $td_class = "";
                    
                    /* Could be on session as info */
                    $assigned_task = $started = FALSE;
                    
                    $assigned_list = explode(", ", $task["assigned_list"]);
                    if (in_array($session->username, $assigned_list)) {$assigned_task=TRUE;} 
                    $td_class .= $session->active_tasks[$task[id]][self_active]?"started":"notstarted";
                    ?>
                                                <tr class="<? echo $row_class ?>">
                                                    <form name="task_sts" action="process.php" method="post">
                                                        <input type="hidden" name="tsk" value="<? echo $task[id] ?>" />
                                                        <td class="<? echo $td_class;  ?>">
                                                            
                                                            <?
                                                            $activeUsersArr = array();
                                                            $activeUsers = "";
                                                            
                                                            if(array_key_exists($task[id], $session->active_tasks)){
                                                                $task[active_timers] = $session->active_tasks[$task[id]][timers];
                                                            //if (is_array($task[active_timers])){ ?>
                                                            <div class="tooltip triangle-right" id="tip<? echo $task[id]; ?>">
                                                                <ul>
                                                            <?
                                                            foreach ($task[active_timers] as $timer){
                                                                if ($session->active_tasks[$task[id]][self_active]) $started=TRUE;
                                                                $duration = time() - strtotime($timer[time_started]);
                                                                $duration = round($duration / (60*60),1);
                                                                
                                                                $timer[time_started] = date("H:i d/m/y" , strtotime($timer[time_started]));
                                                                
                                                                echo "<li><span class=\"timer_user\">".$timer[user]."</span>: <span class=\"timer_duration\">$duration h</span> <span class=\"timer_date\">".$timer[time_started]."</span></li>";
                                                                
                                                                $activeUsersArr[] = $timer[user].": ".$timer[time_started];
                                                            }
                                                            $activeUsers = implode("\r", $activeUsersArr);
                                                            }
                                                            //$activeUsers = substr($activeUsers, 0, -3);
                                                            
                                                            ?>
                                                                </ul>
                                                            </div>
                                                            <? if ($assigned_task && !$started){ ?>
                                                            <a class="taskStartTiming <?php if (!empty ($activeUsers)){ echo "trigger-tip";} ?>" href="#!"><img src="../styling/images/play_task_timer.png" alt="" width="24" /></a>
                                                            <? } else if ($started){ ?>
                                                            <a class="taskStopTiming <?php if (!empty ($activeUsers)){ echo "trigger-tip";} ?>" href="#!"><img src="../styling/images/pause_task_timer.png" alt="" width="24" /></a>
                                                            <? } ?>
                                                        </td>
                                                        <td><a href="task.php?t=<? echo $task[id] ?>" alt="Details"><? echo $task[title] ?></a></td>
                                                        <td><? echo $task[description] ?></td>
                                                        <td><? echo $task[priority] ?></td>
                                                        <td class="task_sts_label">
                                                            <? if (isset ($session->timers_per_task[$task[id]])){ ?>
                                                            <span class="timesheet_icon"></span>
                                                            <? } ?>
                                                            <span class="status <? echo strtolower(str_replace(" ", "_", $task[status])) ?>" title="<? echo $task[status] ?>"></span>
                                                        </td>
                                                        <td class="sts_buttons_cell" style="text-align:center;">

                                                            <!--
                                                <? if ($task[status] != 'Completed') { ?>
                                                                                <input class="blue_btn sts" name="sub_upd_sts"  type="submit" value="Update Status" />
                    <? }if ($task[status] != 'Awaiting Confirmation') { ?>
                                                                <input class="red_btn sts" name="sub_down_sts" type="submit" value="Downgrade" />
                    <? } ?>
                                                            -->

                    <? if ($task[status] != 'Awaiting Confirmation') { ?>
                                                                <input class="down stsajax" name="down_sts" type="button" />
                    <? }if ($task[status] != 'Completed') { ?>
                                                                <input class="upd stsajax" name="upd_sts" type="button" />
                                                            <? } ?>

                                                        </td>
                                                    </form>
                                                </tr>
                    <? if ($counter_tasks == 4 and count($tasks) > 4 and !isset($foundPro)) { ?>
                                                    <tr><td class="moretasks" colspan="6"><a href="main.php?v=<? echo $_GET["v"] . "&p=" . $project ?>">...Περισσότερα tasks...</a></td></tr>
                                                                <?
                                                                break;
                                                            }
                                                        }
                                                        if (isset($_SESSION['task_success' . $project])) {

                                                            if ($_SESSION['task_success' . $project]) {
                                                                ?>
                                                    <tr><td colspan="6"><span class="success add_task_msg">Το task προστέθηκε και ανατέθηκε κανονικά!</span></td></tr>
                                                    <?
                                                }
                                                /* Registration failed */ else {
                                                    ?>
                                                    <tr><td class="error" colspan="5">* Υπήρξε πρόβλημα με το νέο task, προσπαθήστε ξανά!</td></tr>
                                                    <?
                                                }
                                                unset($_SESSION['task_success' . $project]);
                                            }
                                            ?>
                                        </table>
                                        <?
                                        }
                                        else
                                            echo "<h3>No tasks, sorry...</h3>";
                                        if ($session->isWokring()) {
                                            ?>
                                        <form name="newTaskform" action="process.php" method="post">
                                            <input type="hidden" name="project" value="<? echo $project ?>" />
                                            <input type="hidden" name="subnewtask" value="1" />
                                            <a class="addtask_link" href="#!">Add Task</a>
                                            <div class="addtask_div" style="display:<?
                        if ($form->error("vis_add_task" . $project) != '') {
                            echo "block";
                            $err = true;
                        } else {
                            echo "none";
                            $err = false;
                        }
                        ?>">	
                                                <table cellspacing="0" cellpading="0">
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Description</th>
                                                        <th>Priority</th>
                                                    </tr>
                                                    <tr>
                                                        <td class="titleCell"><input class="task_title" type="text" name="task_title" value="<? if ($err) {
                            echo $form->value("task_title");
                        } ?>" /><br />
                                                                <? echo $form->error("task_title_" . $project); ?></td>
                                                        <td class="descCell"><textarea class="task_desc" rows="2" cols="50" name="task_desc"><? if ($err) {
                                                    echo $form->value("task_desc");
                                                } ?></textarea></td>
                                                        <td class="prioCell">
                                                            <select class="task_priority" name="task_priority">
                <?
                foreach ($priority as $key => $prio) {
                    echo "<option value=\"$key\">$prio</option>";
                }
                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Assigned to:<br />
                                                            <select class="assignNewTask_select" >

                                                                <?
                                                                $working_users = $session->getUsers_on_Project($project, SERIALIZED_NONCLIENT);

                                                                foreach ($working_users as $user) {
                                                                    if (preg_match("/^Self/", $user))
                                                                        $valuser = $session->username;
                                                                    else
                                                                        $valuser = $user;
                                                                    echo "<option value=\"$valuser\">$user</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                            <input type="button" value="" class="adduser_btn" /><br />
                <? if ($session->isPA() and count($session->availableUsers_for_project_assignement($project, SERIALIZED)) > 0) { ?>
                                                                <br />
                                                                <a class="assign_more" href="editProject.php?p=<? echo $project ?>">Assign Project to more..</a>
                <? } ?>
                                                        </td>
                                                        <td>
                                                            <div class="users_assigned_links"></div>
                <? echo $form->error("task_assignement" . $project); ?>
                                                            <input class="assignedUsers" type="hidden" style="width: 250px;" name="task_assign" />
                                                        </td>
                                                        <td>
                                                            <input class="green_btn addtask_form_btn" type="submit" value="Submit Task" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </form>



                <? } ?>
                                </div>
                            </div>
                            <?
                        }
                    }
                    if ($selectedPro != '' and !isset($foundPro)) {
                        ?>
                        <script type="text/javascript">
                            window.location = "main.php";
                        </script>
        <?
    }
} else {
    ?>
                    <div class="content-box">
                        <div class="content-box-header">
                            <h2>No Task Available..</h2>
                        </div>
                        <div class="content-box-content">
                            <p>Θα πρέπει να περιμένετε μέχρι κάποιος υπεύθυνος να σας αναθέσει κάποιο task!</p>
                        </div>
                    </div>
    <? } ?>
            </div>

            <div id="right">
                <? if (isset($foundPro)) { ?>
                <div class="content-box">
                    <div class="content-box-header side-header">
                        <h3>Select Project</h3>
                    </div>
                    <div class="content-box-content">
                        <select name="proj" id="proj_select" >
                            <option value="">All</option>
                        <?
                        if (count($projects)>1){
                        foreach ($projects as $project => $tasks) {
                            echo "<option ";
                            if ($project == $selectedPro) {
                                echo "selected ";
                            }
                            echo " value=\"$project\">$project (".count($tasks).")</option>";
                        }
                        unset($project);
                        unset($tasks);
                        ?>
                        </select>
                        <?
                        } else if (count($projects) == 1) {
                            //reset($projects_array_tasks);
                            echo "<input disabled type=\"text\" value=\"" . key($projects) . "\" />";
                        } else {
                            echo "<strong>No Project</strong>";
                        }
                        ?>
                    </div>
                </div>
                <div class="content-box">
                    <div class="content-box-header side-header">
                        <h3><? echo "Project Info"; ?></h3>
                    </div>
                    <div class="content-box-content clock-tab">
                        <div id="project_info">
                            <div id="Title">
                                Project Name:<br />
                                <strong><? echo $foundPro[title] ?></strong>
                            </div>
                            <? if (isset($foundPro[URL])): ?>
                            <div id="URL">
                                URL:<br />
                                <strong><a target="_new" href="http://<? echo $foundPro[URL] ?>"><? echo $foundPro[URL] ?></a></strong>
                            </div>
                            <? endif;
                            if (isset($foundPro[domain_exp])): ?>
                            <div id="domainEXP">
                                Domain Expire:<br />
                                <strong><? echo $foundPro[domain_exp] ?></strong>
                            </div>
                            <? endif;
                            if (isset($foundPro[host_exp])): ?>
                            <div id="hostEXP">
                                Host Expire:<br />
                                <strong><? echo $foundPro[host_exp] ?></strong>
                            </div>
                            <? endif;
                            if (isset($foundPro[description])): ?>
                            <div id="pro_desc">
                                Descriptipn:<br />
                                <strong><? echo $foundPro[description] ?></strong>
                            </div>
                            <? endif;
                            if (isset($foundPro[author])): ?>
                            <div id="pro_author">
                                Author:<br />
                                <strong><? echo $foundPro[author] ?></strong>
                            </div>
                            <? endif; ?>
                        </div>
                    </div>
                </div>
                <? } else if (count($session->compl_projects) > 0) { ?>
                <div class="content-box">
                    <div class="content-box-header side-header">
                        <h3><? echo "Inactive Projects"; ?></h3>
                    </div>
                    <div class="content-box-content clock-tab">
                        <div class="inactiveProj">
                            <ul>
                            <?php foreach ($session->compl_projects as $uncomplete) { ?>
                                <li><a href="main.php?p=<? echo $uncomplete ?>"><? echo $uncomplete ?></a></li>
                            <? } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <? } ?>
                <div class="content-box">
                    <div class="content-box-header side-header">
                        <h3>Search</h3>
                    </div>
                    <div class="content-box-content search-tab">
                        <div class="proj_search">
                            <h4>Search for Project:</h4>
                            <input type="text" name="project" id="project" />			
                        </div>
                    </div>
                </div>
            </div>




            <div style="clear:both"></div>

            <div id="footer">
                <p>Copyright @<? echo date("Y"); ?>, Gan Tasks - Project Management Software, Version 1.11.4
                    <br/> This is a <a href="http://www.gan-web.gr/" >Gan Web Production</a></p>
            </div>
        </div>

<? include("chat_container.php"); ?>

        <script type="text/javascript" src="js/jquery.lavalamp.js"></script>
        <script type="text/javascript" src="js/jquery.corner.js"></script>
        <script type="text/javascript" src="js/jdpicker_1.0.3/jquery.jdpicker.js"></script>
        <script type="text/javascript" src="js/document.js"></script>
        <script type="text/javascript" src="js/jquery.autocomplete.js"></script>
    </body>
</html>
