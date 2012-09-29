<?
/**
 * Constants.php
 *
 * This file is intended to group all constants to
 * make it easier for the site administrator to tweak
 * the login script.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 19, 2004
 */
 
/**
 * Database Constants - these constants are required
 * in order for there to be a successful connection
 * to the MySQL database. Make sure the information is
 * correct.
 */
define("DB_SERVER", "localhost");
define("DB_USER", "ganwebg1_user");
define("DB_PASS", "@oQxERR{=f+P");
define("DB_NAME", "ganwebg1_tasksDB");

/**
 * Database Table Constants - these constants
 * hold the names of all the database tables used
 * in the script.
 */
define("TBL_USERS", "users");
define("TBL_ACTIVE_USERS",  "active_users");
define("TBL_ACTIVE_GUESTS", "active_guests");
define("TBL_BANNED_USERS",  "banned_users");
define("TBL_PROJECTS", "projects");
define("TBL_TASKS", "tasks");
define("TBL_COMMENTS", "comments");
define("TBL_US_PROJS", "usr_prj");
define("TBL_US_TASKS", "usr_tsk");
define("TBL_PROJECT_PROPERTIES", "project_properties");
define("TBL_ACTIVITIES", "activities");

define("TBL_ACTIVE_TIMERS", "active_timers");
define("TBL_TIMERS", "timers");


define("TBL_CLIENTS", "clients");
define("TBL_CONTACTS", "contacts");
define("TBL_APPOINMENTS", "appoinments ");
define("TBL_REPORT_ACTIVITY", "report_activity ");
define("TBL_TRANSACTIONS", "transactions");

/**
 * Special Names and Level Constants - the admin
 * page will only be accessible to the user with
 * the admin name and also to those users at the
 * admin user level. Feel free to change the names
 * and level constants as you see fit, you may
 * also add additional level specifications.
 * Levels must be digits between 0-9.
 */
define("ADMIN_NAME", "admin");
define("GUEST_NAME", "Guest");

//Constants in Array //
/*
$user_levels = array();
$user_levels[0] = 'Super Admin';
$user_levels[1] = 'Admin';
$user_levels[2] = 'Project Admin';
$user_levels[3] = 'Employee';
$user_levels[7] = 'Client';
*/
define("SUPER_ADMIN_LEVEL",  0);
define("ADMIN_LEVEL",  1);
define("PROJECT_ADMIN_LEVEL",  2);
define("EMPLOYEE_LEVEL",  3);
define("CLIENT_LEVEL",  7);

//define("USER_LVL", serialize($user_levels));
$userLevels = array();
$userLevels[0] = 'Super Admin';
$userLevels[1] = 'Administrator';
$userLevels[2] = 'Project Administrator';
$userLevels[3] = 'Employee';
$userLevels[7] = 'Client';

define("USER_LVL", serialize($userLevels));
//to use it:  $userLevels = unserialize(USER_LVL);

$status = array();
$status[0] = 'Completed';
$status[1] = 'In Progress';
$status[2] = 'Not Started';
$status[3] = 'Awaiting Confirmation';

define("STATUS", serialize($status));
define("STATUS_default",3); // Awaiting Confirmation
// Now to use it
//to use it:  $stat = unserialize(STATUS);

$priority = array();
$priority[0] = 'Critical';
$priority[1] = 'Daily Checked';
$priority[2] = 'Following Week';
$priority[3] = 'NIM'; //Not Immediate Interest

define("PRIORITY", serialize($priority));
// Now to use it
//to use it:  $prio = unserialize(PRIORITY);

//end of Constants in Array

//Types of Returning Results Set No.1 --- from functions
define("GROUPED",0);
define("DETAILS",1);
define("SERIALIZED",2);
define("SERIALIZED_NONCLIENT",3);
//end of Types of Returning Results Set No.1

//Types of Returning Results Set No.2
define("ALL",1);
define("USER",0);
//end of Types of Returning Results

//Types of Properties//
define("PROPERTY",1);
define("SEPERATOR",0);
//end of types of properties//

//Types of Notifications//
define("New_Project",1001); //Who,When,Project Title
define("Edit_Project",1002); //Who,When,Project Title
define("Del_Project",1003); //Who,When,Project Title
define("Add_Project_Assignement",1004); //Who,When,Project Title, Who got assigned
define("Remove_Project_Assignement",1005); //Who,When,Project Title, Who got removed

define("New_Task",2001); //Who,When,Task Title, Project Title
define("Edit_Task",2002); //Who,When,Task Title, Project Title
define("Del_Task",2003); //Who,When,Task Title, Project Title

define("Add_Task_Assignement",2004); //Who,When,Task Title, Who got assigned
define("Add_Task_Assignement_From_Start",2104); //Who,When,Task Title, Who got assigned
define("Remove_Task_Assignement",2005); //Who,When,Task Title, Who got removed

define("Comment_Task",2010); //Who,When,Task Title, Project Title
define("Upgrade_Task",2011); //Who,When,Task Title, Project Title, Status
define("Downgrade_Task",2012); //Who,When,Task Title, Project Title, Status
//end of Types of Notifications//

//Types of Transactions//
define("Add_Billing",3001); //Project Title,Amount,desc,file,When
define("Add_Payment",3002); //Project Title,Amount,VAT,desc,file,When
define("Add_Invoice",3003); //Project Title,Amount,VAT,desc,file,When
define("Add_Expence",3004); //Operator,Amount,VAT,file,When
define("Add_Withdraw",3005); //User,Amount,When
define("Add_Deposit",3006); //User,Amount,When
//end of Types of Transactions//

define("GENERIC_AVATAR", "http://".$_SERVER["HTTP_HOST"]."/styling/avatars/man-avatar.png");

/**
 * This boolean constant controls whether or
 * not the script keeps track of active users
 * and active guests who are visiting the site.
 */
define("TRACK_VISITORS", true);

/**
 * Timeout Constants - these constants refer to
 * the maximum amount of time (in minutes) after
 * their last page fresh that a user and guest
 * are still considered active visitors.
 */
define("USER_TIMEOUT", 10);//minutes
define("GUEST_TIMEOUT", 5);//minutes

define("MSG_LOG_TIMEOUT",1);
define("TYPE_TIMEOUT",DAY); //possible are (SECOND, MINUTE, HOUR, DAY, MONTH, YEAR)


/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary to fit your website. If you need
 * help, visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default
define("COOKIE_PATH", "/");  //Avaible in whole domain

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_NAME", "Tasks Management Gan-Web");
define("EMAIL_FROM_ADDR", "noreply@gan-web.gr");
define("EMAIL_WELCOME", true);

/**
 * This constant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */
define("ALL_LOWERCASE", false);

//define ur site's name for displaying in the URL title
define("APP_NAME", "Gan Web Task Management");

?>