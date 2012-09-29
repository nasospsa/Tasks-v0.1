<?php
include("include/session.php");
include("include/notifications.php");
include("include/statistics.php");
/* @var $session Session */

if (!$session->logged_in) {
    header("Location: login.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <link rel="stylesheet" type="text/css" href="styling/style.css"/>
        <link rel="stylesheet" type="text/css" href="styling/jquery.autocomplete.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo $pageTitle . " | " . $session->web_name ?></title>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js"></script>
        <script type="text/javascript" src="/js/highcharts/highcharts.js"></script>
        <script type="text/javascript" src="/js/highcharts/modules/exporting.js"></script>
        
        <? include("js/js-stats-loading.php"); ?>
    </head>

    <body>
        <? //echo $_SERVER['SERVER_ADDR']; ?>
        <div id="wrapper">
            <div id="top">
                <div id="logo"><span>Gan Web Internet Solutions</span><br/><span>Project Management Software</span></div>
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
                                ?>
<? endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="clr"></div>
            </div>
            <div id="mainmenu">
                <ul>
                <? if ($session->isWokring()) { ?>
                    <li><a href="main.php?v=myTasks">My Tasks</a></li>
                    <li id="current"><a href="main.php?v=All">All</a></li>
                    <li><a href="main.php?v=Authored">Authored</a></li>
                    <? } else { ?>
                    <li id="current"><a href="main.php?v=All">Current</a></li>
                    <? } ?>
                    <li><a href="main.php?v=Completed">Completed</a></li>
                </ul>
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
                                    <a href="#!">Stats</a>
                                </div>
                            <? } ?>
                            <div id="timesheetsLink">
                                <a href="/timesheets.php">Timesheets</a>
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
                <?
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
                ?>

                <div class="content-box">
                    <div class="content-box-header">
                        <h2>Στατιστικά</h2>
                    </div>
                    <div class="content-box-content">
                        <pre>Feature in Alpha Stage.. soon to be implemented
                        </pre>
                        
                        <div class="float50">
                            <h3 class="chartTitle">Total Activities made by User</h3>
                            <div id="usersPie" class="chartDiv1"></div>
                        </div>
                        <div class="float50">
                            <h3 class="chartTitle">Last 15 Days Activities</h3>
                            <div id="usersCalendar" class="chartDiv1"></div>
                        </div>
                        <div class="clr"></div>
                        <h3 class="chartTitle">User Activities Lifetime</h3>
                        <div id="datatable_Activities">
                        <?
                        $userActivitiesArray = $database->activities_groupedByUser();
                        //$rowTotal = 0;
                        //$columnTotal = 0;
                        if (count($userActivitiesArray)):?>
                        <table id="userActivitiesTBL">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>New Project</th>
                                <th>Edit Project</th>
                                <th>Del Project</th>
                                <th>Assign Pr</th>
                                <th>Rem Assign Pr</th>
                                <th>New Task</th>
                                <th>Edit Task</th>
                                <th>Del Task</th>
                                <th>Assign Task</th>
                                <th>Remove Task</th>
                                <th>Comments</th>
                                <th>Upgrade Task</th>
                                <th>DGrade Task</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                            foreach ($userActivitiesArray as $user) {?>
                            <tr>
                                <?
                                $rowTotal = 0;
                                foreach ($user as $value) {?>
                                <td><? echo $value; $rowTotal+=$value; ?></td>
                                <?
                                }?>
                                <td><? echo $rowTotal; ?></td>
                            </tr>
                            <?
                            }?>
                            </tbody>
                        </table>
                        <? //$statistics->last30DaysTotalActivitiesPerUser();
                        endif; ?>
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
