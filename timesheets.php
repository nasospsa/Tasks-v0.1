<?php
include("include/session.php");
include("include/notifications.php");
$results = $_SESSION["content"][table];
$results_type = $_SESSION["content"][type];
$results_header = $_SESSION["content"][header];
$results_dates = $_SESSION["content"][dates];
unset($_SESSION["content"]);
/* @var $session Session */

if (!$session->logged_in) {
    header("Location: login.php");
}
$body_class = "";

$all_users = $database->getAllUsers();
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="stylesheet" type="text/css" href="styling/style.css"/>
        <link rel="stylesheet" type="text/css" href="styling/jquery.autocomplete.css" />
        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/ui-darkness/jquery-ui.css"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo $pageTitle . " | " . $session->web_name ?></title>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/jquery.tools.tooltip.min.js"></script>
    </head>

    <body class="<? echo $body_class ?>">
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
                        <? if (count($notifications->getUserNotifications(SERIALIZED)) > 0): ?>
                            <ul class="notificationsList">
                            <?
                            foreach ($notifications->getUserNotifications(SERIALIZED) as $notification) {
                                echo "<li class=\"$notification[class]\">$notification[message]</li>";
                            }
                            endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="clr"></div>
            </div>
            
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
                            <div id="homeAllTasksLink">
                                <a href="/">Tasks</a>
                            </div>
                            <?
                            if ($session->isPA()) {
                                ?>
                                <div id="clientsLink">
                                    <a href="/clients/">Clients</a>
                                </div>
                                <div id="statsLink">
                                    <a href="stats.php">Stats</a>
                                </div>
                            <? } ?>
                            <div id="timesheetsLink">
                                <a href="#!">Timesheets</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-box">
                    <div class="content-box-header side-header"><h3>User Notes</h3></div>
                    <div class="content-box-content"><? include("include/notes.php") ?></div>
                </div>
            </div>


            <div id="center" class="norightcol">
            <? if (isset($_SESSION['success'])) {
                /* Successful Message */
                if ($_SESSION['success']) { ?>
                <div class="success">
                    <span class="ico"></span>
                    <span class="message success_txt"><? echo $_SESSION['header'] ?></span>
                </div>
                <?
                } /* Failure Message */ else { ?>
                <div class="failure">
                    <span class="ico"></span>
                    <span class="message fail_txt"><? echo $_SESSION['header'] ?></span>
                </div>
                <?
                }
                unset($_SESSION['success']);
                unset($_SESSION['header']);
            }?>
                <div class="content-box">
                    <div class="content-box-header">
                        <h2>Timesheets</h2><pre class="beta">Feature in Alpha Stage</pre>
                    </div>
                    <div class="content-box-content">
                        <? if ($form->error("big message")!=""){ ?>
                        <h1><? echo $form->error("big message"); ?></h1>
                        <? } ?>
                        <div id="timesheet_filter_wrapper">
                            <form name="timesheetForm" action="process.php" method="POST">
                                <div class="lvl1filter">
                                    <h3>1. Select:</h3>
                                    <? echo $form->error("general"); ?>
                                    <div id="lvl1optionProject">
                                        <span class="radiolvl1Label"><input type="radio" name="lvl1filter" value="project" /> Project</span>
                                        <div class="lvl1SelectContainer">
                                            <span class="projectSelectFilter">
                                                <select id="lvl1Project" name="lvl1Project">
                                                    <? foreach ($session->avail_projects as $project) { ?>
                                                    <option value="<? echo $project[title] ?>"><? echo $project[title] ?></option>
                                                    <? } ?>
                                                </select>
                                            </span>
                                        </div>
                                    </div>
                                    <div id="lvl1optionTask">
                                        <span class="radiolvl1Label"><input type="radio" name="lvl1filter" value="task" /> Task</span>
                                        <div class="lvl1SelectContainer">
                                            <div>
                                            <span class="projectLabelFilter">Project:</span>
                                            <span class="projectSelectFilter">
                                                <select id="lvl1TaskProject" name="lvl1TaskProject">
                                                    <option disabled="disabled" selected="selected">--</option>
                                                    <? foreach ($session->avail_projects as $project) {//All Projects - Paizei swsta  ?>
                                                    <option value="<? echo $project[title] ?>"><? echo $project[title] ?></option>
                                                    <? } ?>
                                                </select>
                                            </span>
                                            </div>
                                            <div>
                                            <span class="taskLabelFilter">Task:</span>
                                            <span class="taskSelectFilter">
                                                <select id="lvl1Task" disabled="disabled" name="lvl1Task">
                                                    <option disabled="disabled" selected="selected">--</option>
                                                </select>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="lvl1optionUser">
                                        <span class="radiolvl1Label"><input type="radio" name="lvl1filter" value="user" /> User</span>
                                        <div class="lvl1SelectContainer">
                                            <span class="userSelectFilter">
                                                <select multiple="multiple" name="lvl1User[]">
                                                    <option value="all">All</option>
                                                    <? foreach ($all_users as $user) { ?>
                                                    <option value="<? echo $user[username] ?>"><? echo $user[username] ?></option>
                                                    <? } ?>
                                                </select>
                                            </span>
                                        </div>
                                    </div>
                                    <div id="lvl1optionNone">
                                        <span class="radiolvl1Label"><input type="radio" name="lvl1filter" value="none" /> None</span>
                                    </div>
                                </div>
                                <div class="lvl2filter">
                                    <h3>2. Analyzed by:</h3>
                                    <div class="selectFilter2">
                                        <select name="lvl2Group" id="lvl2Group">
                                            <option disabled="disabled" selected="selected">--</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="lvl3filter">
                                    <h3>3. Dates:</h3>
                                    <div class="dateFromWrapper">
                                        <span class="lblDate">From: </span><input id="from" type="text" name="from_date" class="datePick" value="<? echo $form->value("from_date") ?>" />
                                        <? echo $form->error("from_date"); ?>
                                    </div>
                                    <div class="dateToWrapper">
                                        <span class="lblDate">Until: </span><input id="to" type="text" name="to_date" class="datePick" value="<? echo $form->value("to_date") ?>" />
                                        <? echo $form->error("to_date"); ?>
                                    </div>
                                </div>
                                <div class="submitFilter">
                                    <input name="subTimesheet" type="hidden" value="1" />
                                    <a href="#!" id="submitReport" class="black" onclick="timesheetForm.submit();">
                                        <img src="http://cdn1.iconfinder.com/data/icons/Free-Medical-Icons-Set/128x128/ChronologicalReview.png" width="72" />
                                        Generate Report
                                    </a>
                                </div>
                                <div class="clr">
                                </div>
                            </form>
                        </div>
                        <? if (!empty ($results)){ ?>
                        <div id="timesheetResultsWrapper">
                            <?
                            foreach ($results as $result) {
                            $index=0;
                            $table = $result[timesheet];
                            $total_duration=0;
                            if (!is_null($table)){ ?>
                            <div class="timesheet">
                            <h2><? echo $result[header] ?> <span class="subtitle">(<? echo "From $results_dates[0] to $results_dates[1]" ?>)</span></h2>
                            <table class="timesheetResultsTBL" cellpadding="0" cellspacing="0">
                            <? foreach ($table as $row){
                                $index++;
                                if ($index==1){ ?>
                                <tr>
                                <? foreach (array_keys($row) as $key){ ?>
                                    <td class="<? echo $key ?>"><? echo str_replace("_", " ", $key) ?></td>
                                <? } ?>
                                </tr>
                                <? } ?>
                                <tr>
                                <? foreach ($row as $key=>$value){
                                    if ($index==1)?>
                                    <td>
                                    <? if($key=="duration"){
                                        $total_duration += $value;
                                        $hours = floor($value/3600);
                                        $hours = $hours<10?"0".$hours:$hours;
                                        $mins  = round($value/60) - $hours*60;
                                        $mins = $mins<10?"0".$mins:$mins;
                                        echo $hours.":".$mins;
                                    } else{
                                        echo $value;                                        
                                    } ?>
                                    </td>
                                <? } ?>
                                </tr>
                            <? } ?>
                            </table>
                            <span>Total Duration: 
                            <?
                            $hours = floor($total_duration/3600);
                            $hours = ($hours<10 && $hours>0)?"0".$hours:$hours;
                            $mins  = round($total_duration/60) - $hours*60;
                            $mins = $mins<10?"0".$mins:$mins;
                            echo $hours.":".$mins; ?></span>
                            </div>
                            <? }} ?>
                            <div class="clr"></div>
                        </div>
                        <? } ?>
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

        <script type="text/javascript" src="js/jquery.autocomplete.js"></script>
        <script type="text/javascript" src="js/document.js"></script>
    </body>
</html>