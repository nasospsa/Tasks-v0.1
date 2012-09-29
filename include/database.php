<?

/**
 * Database.php
 * 
 * The Database class is meant to simplify the task of accessing
 * information from the website's database.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 17, 2004
 */
include("constants.php");
date_default_timezone_set('Europe/Athens');

class MySQLDB {

    var $connection;         //The MySQL database connection
    var $num_active_users;   //Number of active users viewing site
    var $num_active_guests;  //Number of active guests viewing site
    var $num_members;        //Number of signed-up users

    /* Note: call getNumMembers() to access $num_members! */

    /* Class constructor */

    function MySQLDB() {
        /* Make connection to database */
        $this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
        mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());
        mysql_set_charset('utf8');

        /**
         * Only query database to find out number of members
         * when getNumMembers() is called for the first time,
         * until then, default value set.
         */
        $this->num_members = -1;

        if (TRACK_VISITORS) {
            /* Calculate number of users at site */
            $this->calcNumActiveUsers();

            /* Calculate number of guests at site */
            $this->calcNumActiveGuests();
        }
    }

    /**
     * confirmUserPass - Checks whether or not the given
     * username is in the database, if so it checks if the
     * given password is the same password in the database
     * for that user. If the user doesn't exist or if the
     * passwords don't match up, it returns an error code
     * (1 or 2). On success it returns 0.
     */
    function confirmUserPass($username, $password) {
        /* Add slashes if necessary (for query) */

        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }

        /* Verify that user is in database */
        $q = "SELECT password FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysql_query($q, $this->connection);
        if (!$result || (mysql_numrows($result) < 1)) {
            return 1; //Indicates username failure
        }

        /* Retrieve password from result, strip slashes */
        $dbarray = mysql_fetch_array($result);
        $dbarray['password'] = stripslashes($dbarray['password']);
        $password = stripslashes($password);

        /* Validate that password is correct */
        if ($password == $dbarray['password']) {
            return 0; //Success! Username and password confirmed
        } else {
            return 2; //Indicates password failure
        }
    }

    /**
     * confirmUserID - Checks whether or not the given
     * username is in the database, if so it checks if the
     * given userid is the same userid in the database
     * for that user. If the user doesn't exist or if the
     * userids don't match up, it returns an error code
     * (1 or 2). On success it returns 0.
     */
    function confirmUserID($username, $userid) {
        /* Add slashes if necessary (for query) */
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }

        /* Verify that user is in database */
        $q = "SELECT userid FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysql_query($q, $this->connection);
        if (!$result || (mysql_numrows($result) < 1)) {
            return 1; //Indicates username failure
        }

        /* Retrieve userid from result, strip slashes */
        $dbarray = mysql_fetch_array($result);
        $dbarray['userid'] = stripslashes($dbarray['userid']);
        $userid = stripslashes($userid);

        /* Validate that userid is correct */
        if ($userid == $dbarray['userid']) {
            return 0; //Success! Username and userid confirmed
        } else {
            return 2; //Indicates userid invalid
        }
    }

    /**
     * usernameTaken - Returns true if the username has
     * been taken by another user, false otherwise.
     */
    function usernameTaken($username) {
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }
        $q = "SELECT username FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysql_query($q, $this->connection);
        return (mysql_numrows($result) > 0);
    }

    /**
     * usernameBanned - Returns true if the username has
     * been banned by the administrator.
     */
    function usernameBanned($username) {
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }
        $q = "SELECT username FROM " . TBL_BANNED_USERS . " WHERE username = '$username'";
        $result = mysql_query($q, $this->connection);
        return (mysql_numrows($result) > 0);
    }

    /**
     * addNewUser - Inserts the given (username, password, email)
     * info into the database. Appropriate user level is set.
     * Returns true on success, false otherwise.
     */
    function addNewUser($username, $password, $email) {
        //Default Sign-Up level is Client level
        $ulevel = CLIENT_LEVEL;
        $q = "INSERT INTO " . TBL_USERS . " VALUES ('$username', '$password', '', $ulevel, '$email', '" . date("Y-m-d H:i:s") . "', '')";
        return mysql_query($q, $this->connection);
    }

    /**
     * updateUserField - Updates a field, specified by the field
     * parameter, in the user's row of the database.
     */
    function updateUserField($username, $field, $value) {
        $q = "UPDATE " . TBL_USERS . " SET " . $field . " = '$value' WHERE username = '$username'";
        return mysql_query($q, $this->connection);
    }

    /**
     * getUserInfo - Returns the result array from a mysql
     * query asking for all information stored regarding
     * the given username. If query fails, NULL is returned.
     */
    function getUserInfo($username) {
        $q = "SELECT * FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = $this->query($q);
        /* Error occurred, return given name by default */
        if (!$result || (mysql_numrows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        $dbarray = mysql_fetch_array($result);
        return $dbarray;
    }

    /**
     * getNumMembers - Returns the number of signed-up users
     * of the website, banned members not included. The first
     * time the function is called on page load, the database
     * is queried, on subsequent calls, the stored result
     * is returned. This is to improve efficiency, effectively
     * not querying the database when no call is made.
     */
    function getNumMembers() {
        if ($this->num_members < 0) {
            $q = "SELECT * FROM " . TBL_USERS;
            $result = $this->query($q);
            $this->num_members = mysql_numrows($result);
        }
        return $this->num_members;
    }

    /**
     * calcNumActiveUsers - Finds out how many active users
     * are viewing site and sets class variable accordingly.
     */
    function calcNumActiveUsers() {
        /* Calculate number of users at site */
        $q = "SELECT * FROM " . TBL_ACTIVE_USERS;
        $result = mysql_query($q, $this->connection);
        $this->num_active_users = mysql_numrows($result);
    }

    /**
     * calcNumActiveGuests - Finds out how many active guests
     * are viewing site and sets class variable accordingly.
     */
    function calcNumActiveGuests() {
        /* Calculate number of guests at site */
        $q = "SELECT * FROM " . TBL_ACTIVE_GUESTS;
        $result = mysql_query($q, $this->connection);
        $this->num_active_guests = mysql_numrows($result);
    }
    
    function getAllUsers() {
        if ($this->num_members < 0) {
            $q = "SELECT * FROM " . TBL_USERS;
            $result = $this->query($q);
            $rows = $this->mysql_fetch_full_result_array($result);
        }
        return $rows;
    }

    /**
     * addActiveUser - Updates username's last active timestamp
     * in the database, and also adds him to the table of
     * active users, or updates timestamp if already there.
     */
    function addActiveUser($username, $time) {
        $q = "UPDATE " . TBL_USERS . " SET timestamp = '" . date("Y-m-d H:i:s") . "' WHERE username = '$username'";
        mysql_query($q, $this->connection);

        if (!TRACK_VISITORS)
            return;
        $q = "REPLACE INTO " . TBL_ACTIVE_USERS . " VALUES ('$username', '" . date("Y-m-d H:i:s") . "')";
        mysql_query($q, $this->connection);
        $this->calcNumActiveUsers();
    }

    /* addActiveGuest - Adds guest to active guests table */

    function addActiveGuest($ip, $time) {
        if (!TRACK_VISITORS)
            return;
        $q = "REPLACE INTO " . TBL_ACTIVE_GUESTS . " VALUES ('$ip', '" . date("Y-m-d H:i:s") . "')";
        mysql_query($q, $this->connection);
        $this->calcNumActiveGuests();
    }

    /* These functions are self explanatory, no need for comments */

    /* removeActiveUser */

    function removeActiveUser($username) {
        if (!TRACK_VISITORS)
            return;
        $q = "DELETE FROM " . TBL_ACTIVE_USERS . " WHERE username = '$username'";
        mysql_query($q, $this->connection);
        $this->calcNumActiveUsers();
    }

    /* removeActiveGuest */

    function removeActiveGuest($ip) {
        if (!TRACK_VISITORS)
            return;
        $q = "DELETE FROM " . TBL_ACTIVE_GUESTS . " WHERE ip = '$ip'";
        mysql_query($q, $this->connection);
        $this->calcNumActiveGuests();
    }

    /* removeInactiveUsers */

    function removeInactiveUsers() {
        if (!TRACK_VISITORS)
            return;
        //$timeout = time()-USER_TIMEOUT*60;
        $q = "DELETE FROM " . TBL_ACTIVE_USERS . " WHERE timestamp < DATE_ADD(NOW(), INTERVAL -" . USER_TIMEOUT . " MINUTE)";
        mysql_query($q, $this->connection);
        $this->calcNumActiveUsers();
    }

    /* removeInactiveGuests */

    function removeInactiveGuests() {
        if (!TRACK_VISITORS)
            return;
        //$timeout = time()-GUEST_TIMEOUT*60;
        $q = "DELETE FROM " . TBL_ACTIVE_GUESTS . " WHERE timestamp < DATE_ADD(NOW(), INTERVAL -" . USER_TIMEOUT . " MINUTE)";
        mysql_query($q, $this->connection);
        $this->calcNumActiveGuests();
    }

    //Remove Old Message Logs for older than "MSG_LOG_TIMEOUT" of type "TYPE_TIMEOUT", e.g. 1 Day, 30 minutes etc..
    function removeOldMessages() {
        $q = "DELETE FROM chat WHERE sent < DATE_ADD(NOW(), INTERVAL -" . MSG_LOG_TIMEOUT . " " . TYPE_TIMEOUT . ")";
        mysql_query($q, $this->connection);
        return TRUE;
    }

    function addNewTask($title, $desc, $priority, $project, $author, $assign_array) {
        $status = STATUS_default;

        $q = "INSERT INTO " . TBL_TASKS . " (title,description,time_created,time_touched,priority,status,project_name,author) VALUES ('$title','$desc','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "','$priority','$status','$project','$author')";
        if (mysql_query($q, $this->connection)) {
            $curr_task_id = mysql_insert_id();
            foreach ($assign_array as $user_assignement) {
                $q = "INSERT INTO " . TBL_US_TASKS . " VALUES ('$curr_task_id','$user_assignement')";
                mysql_query($q, $this->connection); //*send mail to assigned*/
            }
            return true;
        }
        else
            return false;
    }

    function editTask($id, $title, $desc) {
        $q = "UPDATE " . TBL_TASKS . " SET  title='$title', time_touched='" . date("Y-m-d H:i:s") . "', description='$desc' WHERE id = '$id'";
        return mysql_query($q, $this->connection);
    }

    function delTask($id) {
        $q = "DELETE FROM " . TBL_TASKS . " WHERE id = '$id'";
        return mysql_query($q, $this->connection);
    }

    function updateTaskStatus($task_id) {
        $q = "UPDATE " . TBL_TASKS . " SET  status = status-1, time_touched='" . date("Y-m-d H:i:s") . "' WHERE id = '$task_id' AND status>0";
        return mysql_query($q, $this->connection);
    }

    function downgradeTaskStatus($task_id) {
        $q = "UPDATE " . TBL_TASKS . " SET  status = status+1, time_touched='" . date("Y-m-d H:i:s") . "' WHERE id = '$task_id' AND status<3";
        return mysql_query($q, $this->connection);
    }

    function getTaskStatus($task_id) {
        $q = "SELECT status FROM " . TBL_TASKS . " WHERE id = '$task_id'";
        $result = mysql_query($q, $this->connection);

        $stat = unserialize(STATUS);

        if ($result) {
            $row = mysql_fetch_array($result);
            return $stat[$row[0]];
        }
    }
    
    function touchTask($task_id){
        $q = "UPDATE " . TBL_TASKS . " SET  last_touched = '". date("Y-m-d H:i:s") ."' WHERE id = '$task_id'";
    }

    //Manipulated Task Assignments
    function addTaskUsers($users, $task) {
        foreach ($users as $user) {
            $q = "INSERT INTO " . TBL_US_TASKS . " VALUES ('$task','$user')";
            mysql_query($q, $this->connection);
        }
        return true;
    }

    function removeTaskUsers($users, $task) {
        foreach ($users as $user) {
            $q = "DELETE FROM " . TBL_US_TASKS . " WHERE username='$user' AND task_id='$task'";
            mysql_query($q, $this->connection);
        }
        return true;
    }

    //Project new,edit,delete functions
    function addNewProject($title, $url, $domain, $host, $desc, $author) {
        $q = "INSERT INTO " . TBL_PROJECTS . " VALUES ('$title','$url',$domain,$host,'$desc','$author',NULL)";
        return mysql_query($q, $this->connection);
    }

    function updateProject($title, $url, $domain, $host, $desc, $old) {
        $q = "UPDATE " . TBL_PROJECTS . " SET title='$title',URL='$url',domain_exp=$domain,host_exp=$host,description='$desc' WHERE title='$old'";
        return mysql_query($q, $this->connection);
    }

    function deleteProject($title) {
        $q = "DELETE FROM " . TBL_PROJECTS . " WHERE title='$title'";
        return mysql_query($q, $this->connection);
    }

    //Project Accesibility
    function addProjectUsers($users, $project) {
        foreach ($users as $user) {
            $q = "INSERT INTO " . TBL_US_PROJS . " VALUES ('$project','$user')";
            mysql_query($q, $this->connection);
        }
        return TRUE;
    }

    function removeProjectUsers($users, $project) {
        foreach ($users as $user) {
            $q = "DELETE FROM " . TBL_US_PROJS . " WHERE username='$user' AND project_name='$project'";
            mysql_query($q, $this->connection);
        }
        return TRUE;
    }

    /*
      Everytime a user is removed from a project, the user that removed him,
      must take charge of all the tasks in that project, that the removed user
      was assigned.
     */

    function updateTaskProjectUsers($users, $project, $username) {
        $tasks = array();
        $q = "SELECT id FROM " . TBL_TASKS . " WHERE project_name='$project'";
        $result = mysql_query($q, $this->connection);
        while ($id = mysql_fetch_array($result)) {
            $tasks[$id[0]] = $id[0];
        }

        if (count($tasks) > 0) {

            foreach ($users as $user) {
                foreach ($tasks as $task) {
                    $q = "UPDATE " . TBL_US_TASKS . " SET username='$username' WHERE username='$user' AND task_id='$task'";
                    $result = mysql_query($q, $this->connection);
                    if ($result == false) {
                        $q = "DELETE FROM " . TBL_US_TASKS . " WHERE username='$user' AND task_id='$task'";
                        $result = mysql_query($q, $this->connection);
                    }
                }
            }
        }
        return true;
    }

    //get Projects
    function getProjects() {
        $args = func_get_args();
        $num_args = func_num_args();
        if (($num_args == 1) && ($args[0] == ALL)) { //1 Argument => Get All Projects => getProjects(ALL)
            $q = "SELECT * FROM " . TBL_PROJECTS;
        } else if (($num_args == 2) && ($args[0] == USER)) {  //2 Arguments => Get User Projects => getProjects(USER,USERNAME)
            $username = $args[1];
            $q = "SELECT * FROM " . TBL_PROJECTS . " LEFT OUTER JOIN " . TBL_US_PROJS . " ON title = project_name WHERE username='$username'";
        }

        $result = mysql_query($q, $this->connection);
        while ($row = mysql_fetch_array($result)) {
            if (!empty($row[domain_exp]))
                $row[domain_exp] = date("d/m/Y", $this->convert_datetime($row[domain_exp]));
            if (!empty($row[host_exp]))
                $row[host_exp] = date("d/m/Y", $this->convert_datetime($row[host_exp]));
            $projects[$row[title]] = $row;
        }

        return $projects;
    }
    
    function getActiveProjects(){
    	$q = "SELECT tsk.project_name as project, max( tmr.time_started ) AS latest_act
    	FROM ".TBL_ACTIVE_TIMERS." AS tmr INNER JOIN ".TBL_TASKS." AS tsk ON tmr.task=tsk.ID
        GROUP BY tsk.project_name
        ORDER BY latest_act";
        $result = $this->query($q);
        $returned_rows = mysql_fetch_array($result);
        return $returned_rows;
    }

    function security_ProjectExists() {
        // arguments:  mode, project, username
        $args = func_get_args();
        $num_args = func_num_args();
        $project = $args[1];
        
        if (($num_args == 2) && ($args[0] == ALL)){
            $q = "SELECT * FROM " . TBL_PROJECTS . " WHERE title='$project'";
        }
        else if (($num_args == 3) && ($args[0] == USER)){
            $username = $args[2];
            $q = "SELECT * FROM " . TBL_PROJECTS . " LEFT OUTER JOIN " . TBL_US_PROJS . " ON title = project_name WHERE username='$username' AND title='$project'";
        }
        $result = mysql_query($q, $this->connection);
        $num_results = mysql_num_rows($result);
        if ($num_results > 0)
            return TRUE;
        else
            return FALSE;
    }

    function getProjectProperties($project) {
        $q = "SELECT * FROM " . TBL_PROJECT_PROPERTIES . " pp WHERE project='" . $project . "' ORDER BY pp.group,pp.order";
        $result = mysql_query($q, $this->connection);
        while ($row = mysql_fetch_array($result)) {
            $properties[$row[group]] = $row;
        }
        if (count($properties) > 0)
            return $properties;
        else
            return;
    }

    //getTasks
    function getTasksPro($project) {
        //Project Assigned SQL query
        //$q = "SELECT p.*,GROUP_CONCAT(DISTINCT username ORDER BY username DESC SEPARATOR ', ') assigned_list FROM ".TBL_TASKS." INNER JOIN ".TBL_US_PROJS." on  WHERE project_name='$project'";
        $q = "SELECT t.*,GROUP_CONCAT(DISTINCT username ORDER BY username SEPARATOR ', ') assigned_list FROM " . TBL_TASKS . " t INNER JOIN " . TBL_US_TASKS . " ON task_id=t.id WHERE project_name='$project' AND t.status>0 GROUP BY id ORDER BY id DESC";
        $result = mysql_query($q, $this->connection);
        $prio = unserialize(PRIORITY);
        $stat = unserialize(STATUS);
        
        while ($task = mysql_fetch_array($result)) {
            //$task[active_timers] = $this->getTaskActiveTimers($task[id]);
            $task[priority] = $prio[$task[priority]];
            $task[status] = $stat[$task[status]];
            $tasks[$task[id]] = $task;
        }
        if (count($tasks) > 0)
            return $tasks;
        else
            return;
    }

    function getTasksPro_Flat($project) {
        $q = "SELECT id FROM " . TBL_TASKS . " WHERE project_name='$project' ORDER BY id";
        $result = mysql_query($q, $this->connection);
        while ($task = mysql_fetch_array($result)) {
            $tasks[$task[id]] = $task[id];
        }
        if (count($tasks) > 0)
            return $tasks;
        else
            return;
    }
    
    function getAllTasksPro($project){
        $q = "SELECT * FROM " . TBL_TASKS . " WHERE project_name='$project' ORDER BY id";
        $result = mysql_query($q, $this->connection);
        if ($result){
            return $this->mysql_fetch_full_result_array($result);
        }
        else return;
    }
    
    function getLatestTasks_Pro($project){
        $q = "SELECT * FROM " . TBL_TASKS . " WHERE project_name='$project' AND status<>0
                ORDER BY time_touched DESC , time_created DESC , id DESC
                LIMIT 0,10";
        $result = mysql_query($q, $this->connection);
        if ($result){
            return $this->mysql_fetch_full_result_array($result);
        }
        else return;
    }
    
    function getLatestTasks($username){
        $q = "SELECT * FROM " . TBL_TASKS . " WHERE status<>0
                ORDER BY time_touched DESC , time_created DESC , id DESC
                LIMIT 0,10";
        $result = mysql_query($q, $this->connection);
        if ($result){
            return $this->mysql_fetch_full_result_array($result);
        }
        else return;
    }

    function getAssignedTasks_per_Pro($user) {
        $q = "SELECT t.* FROM " . TBL_US_TASKS . " ut LEFT OUTER JOIN " . TBL_TASKS . " t ON t.id = ut.task_id  WHERE username='$user' AND t.status>0";
        $result = mysql_query($q, $this->connection);
        $prio = unserialize(PRIORITY);
        $stat = unserialize(STATUS);
        while ($task = mysql_fetch_array($result)) {
            $task[priority] = $prio[$task[priority]];
            $task[status] = $stat[$task[status]];
            $assigned_tasks[$task[project_name]][] = $task;
        }
        return $assigned_tasks;
    }

    function getAuthoredTasks_per_Pro($user) {
        $q = "SELECT t.* FROM " . TBL_TASKS . " t WHERE author='$user' AND t.status>0";
        $result = mysql_query($q, $this->connection);
        $prio = unserialize(PRIORITY);
        $stat = unserialize(STATUS);
        while ($task = mysql_fetch_array($result)) {
            $task[priority] = $prio[$task[priority]];
            $task[status] = $stat[$task[status]];
            $assigned_tasks[$task[project_name]][] = $task;
        }
        return $assigned_tasks;
    }

    function getCompletedTasks_per_Pro($user) {
        $q = "SELECT t.* FROM " . TBL_TASKS . " t LEFT OUTER JOIN " . TBL_US_PROJS . " up ON t.project_name=up.project_name WHERE t.status=0 and up.username='$user'";
        $result = mysql_query($q, $this->connection);
        $prio = unserialize(PRIORITY);
        $stat = unserialize(STATUS);
        while ($task = mysql_fetch_array($result)) {
            $task[priority] = $prio[$task[priority]];
            $task[status] = $stat[$task[status]];
            $assigned_tasks[$task[project_name]][] = $task;
        }
        return $assigned_tasks;
    }

    function getAllCompletedTasks_per_Pro() {
        $q = "SELECT * FROM " . TBL_TASKS . " WHERE status=0";
        $result = mysql_query($q, $this->connection);
        $prio = unserialize(PRIORITY);
        $stat = unserialize(STATUS);
        while ($task = mysql_fetch_array($result)) {
            $task[priority] = $prio[$task[priority]];
            $task[status] = $stat[$task[status]];
            $assigned_tasks[$task[project_name]][] = $task;
        }
        return $assigned_tasks;
    }

    //Array with editable task id's
    function getAllTasks_ID() {
        $tasks = array();
        $q = "SELECT id FROM " . TBL_TASKS;
        $result = mysql_query($q, $this->connection);
        while ($id = mysql_fetch_array($result)) {
            $tasks[$id[0]] = $id[0];
        }
        if (count($tasks) > 0)
            return $tasks;
        else
            return;
    }

    function getProjectAdminTasks_ID($username) {
        $tasks = array();
        $projects = $this->getProjects(USER,$username);
        if (count($projects) > 0) {
            foreach ($projects as $project) {
                $tasks_of_proj = (array) $this->getTasksPro_Flat($project[title]);
                $tasks = array_merge($tasks, $tasks_of_proj);
            }
        }
        if (count($tasks) > 0)
            return $tasks;
        else
            return;
    }

    function getEmployeeTasks_ID($username) {
        $tasks = array();
        $q = "SELECT task_id FROM " . TBL_US_TASKS . " WHERE username='$username'";
        $result = mysql_query($q, $this->connection);
        while ($id = mysql_fetch_array($result)) {
            $tasks[$id[0]] = $id[0];
        }
        if (count($tasks) > 0)
            return $tasks;
        else
            return;
    }

    //Comment functions
    function getCommentsTask($task) {
        $q = "SELECT * FROM " . TBL_COMMENTS . " WHERE task_id='$task' ORDER BY timestamp DESC";
        $result = mysql_query($q, $this->connection);
        $comments = array();
        while ($comment = mysql_fetch_array($result)) {
            $comments[] = $comment;
        }

        //$tasks = $this->mysql_fetch_full_result_array($result);
        if (count($comments) > 0)
            return $comments;
        else
            return;
    }

    //insert Comment
    function addComment($author, $comment, $task) {
        $q = "INSERT INTO " . TBL_COMMENTS . " (commentator,text_comment,timestamp,task_id) VALUES ('$author','$comment','" . date("Y-m-d H:i:s") . "','$task')";
        return mysql_query($q, $this->connection);
    }

    //get Users for userlevels TODO: delete
    function db_getUsers() {
        $args = func_get_args();
        $num_args = func_num_args();

        $q = "SELECT * FROM " . TBL_USERS . " WHERE";

        if ($num_args >= 1) {
            /*
            foreach ($args as $userlevel) {
                $q .= " userlevel=" . $userlevel . " or";
            }
            $q = rtrim($q, " or");*/
            $q .= " userlevel=";
            $q .= implode(" or userlevel=", $args);
            $result = mysql_query($q, $this->connection);
            while ($row = mysql_fetch_array($result)) {
                $users[$row[0]] = $row;
            }
            return $users;
        }
        else
            return FALSE;
    }

    //get Project users 
    function db_getProjectUsers($project) {
        //for PAs,Employees or Clients
        $users = array();
        $args = func_get_args();
        $num_args = func_num_args();
        if ($num_args >= 2) {
            $q = "SELECT u.* FROM " . TBL_USERS . " u inner join " . TBL_US_PROJS . " p on u.username=p.username where p.project_name='$project' and (";

            for ($i = 1; $i < $num_args; $i++) {
                if (in_array($args[$i], array(PROJECT_ADMIN_LEVEL, EMPLOYEE_LEVEL, CLIENT_LEVEL))) {
                    $q .= " u.userlevel='$args[$i]' or";
                }
                else
                    return FALSE;
            }
            $q = rtrim($q, " or");
            $q .= ")";
            $result = mysql_query($q, $this->connection);
            while ($row = mysql_fetch_array($result)) {
                $users[$row[0]] = $row;
            }
            return $users;
        }
        else
            return FALSE;
    }

    //add Project Properties
    function db_addProperties($info_array, $project) {

        $q = "DELETE FROM " . TBL_PROJECT_PROPERTIES . " WHERE project='" . $project . "'";
        $this->query($q);

        foreach ($info_array as $group => $grp_properties) {
            foreach ($grp_properties as $prop_name => $prop_value) {
                $q = "INSERT INTO " . TBL_PROJECT_PROPERTIES . " (project, properties_group, property_name, property_value, type, property_order) VALUES ('$project', '$group', '$prop_value[name]', '$prop_value[value]', $prop_value[type], $prop_value[order])";
                //echo $q."<br/>";

                $this->query($q);
            }
        }
        return true;
    }

    function getProjectCredentials($project) {
        $q = "SELECT * FROM " . TBL_PROJECT_PROPERTIES . " WHERE project='$project' ORDER BY properties_group, property_order";
        if ($result = $this->query($q)) {
            while ($row = mysql_fetch_array($result)) {
                $returned_rows[] = $row;
            }
            return $returned_rows;
        }
        else
            return false;
    }

    /**
     * query - Performs the given query on the database and
     * returns the result, which may be false, true or a
     * resource identifier.
     */
    function query($query) {
        return mysql_query($query, $this->connection);
    }

    function mysql_fetch_full_result_array($result) {
        //$result = mysql_query($q, $this->connection);
        while ($row = mysql_fetch_assoc($result)) {
            $fullResult[] = $row;
        }
        return $fullResult;
    }

    function convert_datetime($str) {
        list($date, $time) = explode(' ', $str);
        list($year, $month, $day) = explode('-', $date);
        list($hour, $minute, $second) = explode(':', $time);
        $year = (int) $year;
        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        return $timestamp;
    }

    

    function updateProjectOrder($username, $order) {
        $q = "UPDATE " . TBL_USERS . " SET projectOrder='$order' where username='$username'";
        $result = mysql_query($q, $this->connection);
        return $result;
    }
    
    //Activities Functions//
    function activities_AddNewProject($acting_user,$project){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project) VALUES ('".New_Project."','" . date("Y-m-d H:i:s") . "','$acting_user','$project')";
        $result = $this->query($q);
        return $result;
    }
    function activities_EditProject($acting_user,$project){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project) VALUES ('".Edit_Project."','" . date("Y-m-d H:i:s") . "','$acting_user','$project')";
        $result = $this->query($q);
        return $result;
    }
    function activities_DeleteProject($acting_user,$project){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project) VALUES ('".Del_Project."','" . date("Y-m-d H:i:s") . "','$acting_user','$project')";
        $result = $this->query($q);
        return $result;
    }
    function activities_AddAssignProject($acting_user,$project,$users){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project,affected_users) VALUES ('".Add_Project_Assignement."','" . date("Y-m-d H:i:s") . "','$acting_user','$project','$users')";
        $result = $this->query($q);
        return $result;
    }
    function activities_RemoveAssignProject($acting_user,$project,$users){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project,affected_users) VALUES ('".Remove_Project_Assignement."','" . date("Y-m-d H:i:s") . "','$acting_user','$project','$users')";
        $result = $this->query($q);
        return $result;
    }
    
    function activities_AddNewTask($acting_user,$project,$task){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project,task) VALUES ('".New_Task."','" . date("Y-m-d H:i:s") . "','$acting_user','$project','$task')";
        $result = $this->query($q);
        return $result;
    }
    function activities_EditTask($acting_user,$project,$task){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project,task) VALUES ('".Edit_Task."','" . date("Y-m-d H:i:s") . "','$acting_user','$project','$task')";
        $result = $this->query($q);
        return $result;
    }
    function activities_DeleteTask($acting_user,$project,$task){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project,task) VALUES ('".Del_Task."','" . date("Y-m-d H:i:s") . "','$acting_user','$project','$task')";
        $result = $this->query($q);
        return $result;
    }
    
    function activities_AddAssignTask($acting_user,$project,$task,$users,$from_start){
        if ($from_start)
            $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project,task,affected_users) VALUES ('".Add_Task_Assignement_From_Start."','" . date("Y-m-d H:i:s") . "','$acting_user','$project','$task','$users')";
        else
            $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project,task,affected_users) VALUES ('".Add_Task_Assignement."','" . date("Y-m-d H:i:s") . "','$acting_user','$project','$task','$users')";
        $result = $this->query($q);
        return $result;
    }
    function activities_RemoveAssignTask($acting_user,$project,$task,$users){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project,task,affected_users) VALUES ('".Remove_Task_Assignement."','" . date("Y-m-d H:i:s") . "','$acting_user','$project','$task','$users')";
        $result = $this->query($q);
        return $result;
    }
    
    function activities_CommentOnTask($acting_user,$project,$task){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project,task) VALUES ('".Comment_Task."','" . date("Y-m-d H:i:s") . "','$acting_user','$project','$task')";
        $result = $this->query($q);
        return $result;
    }
    function activities_UpgradeTask($acting_user,$project,$task,$new_status){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project,task,result_task_status) VALUES ('".Upgrade_Task."','" . date("Y-m-d H:i:s") . "','$acting_user','$project','$task','$new_status')";
        $result = $this->query($q);
        return $result;
    }
    function activities_DowngradeTask($acting_user,$project,$task,$new_status){
        $q = "INSERT INTO " . TBL_ACTIVITIES . " (activity_type,happened,acting_user,project,task,result_task_status) VALUES ('".Downgrade_Task."','" . date("Y-m-d H:i:s") . "','$acting_user','$project','$task','$new_status')";
        $result = $this->query($q);
        return $result;
    }
    //END of..  Activities Functions//
    
    //Notifications//
    function activities_getAllNotifications(){
        $q = "SELECT * FROM ". TBL_ACTIVITIES ." ORDER by happened DESC";
        if ($result = $this->query($q)) {
            while ($row = mysql_fetch_assoc($result)) {
                $returned_rows[] = $row;
            }
            return $returned_rows;
        }
        else
            return false;
    }
    function activities_getPerProjectNotifications($projects){
        $projects = "'".implode("','", $projects)."'";
        $q = "SELECT * FROM ". TBL_ACTIVITIES . " WHERE project IN ($projects) ORDER by happened DESC";
        if ($result = $this->query($q)) {
            while ($row = mysql_fetch_assoc($result)) {
                $returned_rows[] = $row;
            }
            return $returned_rows;
        }
        else
            return false;
    }
    function activities_getUserMadeNotifications($username){
        $q = "SELECT * FROM ". TBL_ACTIVITIES . " WHERE acting_user = '" . $username . "' ORDER by happened DESC";
        if ($result = $this->query($q)) {
            while ($row = mysql_fetch_assoc($result)) {
                $returned_rows[] = $row;
            }
            return $returned_rows;
        }
        else
            return false;
    }
    
    function activities_getNumNotificationsPerUser(){
        $q = "SELECT acting_user username,count(id)*100/(SELECT COUNT(*) FROM ".TBL_ACTIVITIES.") activities FROM ". TBL_ACTIVITIES . " GROUP BY acting_user ORDER by activities DESC";
        $result = $this->query($q);
        $returned_rows = $this->mysql_fetch_full_result_array($result);
        if (!empty ($returned_rows)) {
            return $returned_rows;
        }
        else
            return false;
    }
    function activities_getDailyActsOfUser($username,$num_of_days){
        $num_of_days -= 1;
        $timestamp = time();
        $tm = 86400 * $num_of_days; // 60 * 60 * 24 = 86400 = 1 day in seconds //Get last 30 days
        $tm = $timestamp - $tm;

        $start_date = date("Y-m-d", $tm);
        $end_date = date("Y-m-d", $timestamp);

        //$q = "CALL fill_calendar('$start_date', '$end_date')";
        //$result = $this->query($q);

        $q = "SELECT calendar.datefield AS DATE, IFNULL(COUNT(activities.id),0) AS activities
            FROM (SELECT * FROM ". TBL_ACTIVITIES . " WHERE acting_user='$username') activities RIGHT JOIN calendar ON (DATE_FORMAT(happened, '%Y-%m-%d') = calendar.datefield)
            WHERE (calendar.datefield BETWEEN '$start_date' AND '$end_date')
            GROUP BY DATE";
        $result = $this->query($q);
        $returned_rows = $this->mysql_fetch_full_result_array($result);
        if (!empty ($returned_rows)) {
            return $returned_rows;
        }
        else
            return false;
    }
    function activities_groupedByUser(){
        $q = "SELECT
                acting_user,
                SUM(CASE WHEN activity_type = '".New_Project."' THEN 1 ELSE 0 END) AS new_project,
                SUM(CASE WHEN activity_type = '".Edit_Project."' THEN 1 ELSE 0 END) AS edit_project,
                SUM(CASE WHEN activity_type = '".Del_Project."' THEN 1 ELSE 0 END) AS del_project,
                SUM(CASE WHEN activity_type = '".Add_Task_Assignement."' THEN 1 ELSE 0 END) AS assign_project,
                SUM(CASE WHEN activity_type = '".Remove_Project_Assignement."' THEN 1 ELSE 0 END) AS remove_assign_project,

                SUM(CASE WHEN activity_type = '".New_Task."' THEN 1 ELSE 0 END) AS new_task,
                SUM(CASE WHEN activity_type = '".Edit_Task."' THEN 1 ELSE 0 END) AS edit_task,
                SUM(CASE WHEN activity_type = '".Del_Task."' THEN 1 ELSE 0 END) AS del_task,
                SUM(CASE WHEN activity_type = '".Add_Task_Assignement."' THEN 1 ELSE 0 END) AS assign_task,
                SUM(CASE WHEN activity_type = '".Remove_Task_Assignement."' THEN 1 ELSE 0 END) AS remove_assign_task,

                SUM(CASE WHEN activity_type = '".Comment_Task."' THEN 1 ELSE 0 END) AS comments,
                SUM(CASE WHEN activity_type = '".Upgrade_Task."' THEN 1 ELSE 0 END) AS upgrade_task,
                SUM(CASE WHEN activity_type = '".Downgrade_Task."' THEN 1 ELSE 0 END) AS downgrade_task
                FROM activities GROUP BY acting_user";
        $result = $this->query($q);
        $returned_rows = $this->mysql_fetch_full_result_array($result);
        if (!empty ($returned_rows)) {
            return $returned_rows;
        }
        else
            return false;
    }
    
    /*Timers*/
    function getTimer($username,$task_id){
        $q = "SELECT * FROM ".TBL_ACTIVE_TIMERS." WHERE user='$username' and task='$task_id'";
        $result = $this->query($q);
        if ($result){
            $row = mysql_fetch_array($result);
            return $row;
        }
        else return FALSE;
    }
    
    function addCustomTimer($username, $task, $secs){
        $q = "INSERT INTO ".TBL_TIMERS." (user, task, time_started,duration) VALUES ('$username',$task,DATE_SUB('".date("Y-m-d H:i:s")."', INTERVAL $secs SECOND), $secs)";
        return $this->query($q);
    }
    
    function getActiveTimers(){
        $q = "SELECT at . * , t.project_name as project FROM ".TBL_ACTIVE_TIMERS." AS at LEFT OUTER JOIN ".TBL_TASKS." AS t ON at.task = t.ID";
        $result = $this->query($q);
        if ($result){
            $rows = $this->mysql_fetch_full_result_array($result);
            return $rows;
        }
        else return FALSE;
    }
    
    function getTotalTimers($mode){
        $q = "SELECT task, user, sum(duration) as total_duration
                FROM ".TBL_TIMERS."
                GROUP BY task, user
                order by task";
        $result = $this->query($q);
        
        if ($result){
            if ($mode==SERIALIZED){
                $rows = $this->mysql_fetch_full_result_array($result);
            }
            else if ($mode==GROUPED){//grouped by tasks
                while ($row = mysql_fetch_array($result)){
                    $rows[$row[task]][$row[user]] = $row[total_duration];                   
                }
            }
            return $rows;
        }
        else return FALSE;
    }
    
    function getTaskTotalTimers($task){
        $q = "SELECT user, sum(duration) as total_duration
                FROM ".TBL_TIMERS."
                WHERE task=$task
                GROUP BY user
                order by task";
        $result = $this->query($q);
        if ($result){
            $rows = $this->mysql_fetch_full_result_array($result);
            return $rows;
        }
        else return FALSE;
    }
    
    function getTaskActiveTimers($task_id){
        $q = "SELECT * FROM ".TBL_ACTIVE_TIMERS." WHERE task='$task_id'";
        $result = $this->query($q);
        if ($result){
            $rows = $this->mysql_fetch_full_result_array($result);
            return $rows;
        }
        else return FALSE;
    }
    
    function getTimesheetProject($project,$group,$from,$to){
        if ($group=="task"){
            $q = "SELECT tsk.ID as ID,tsk.title as title,sum(tmr.duration) as duration, min( tmr.time_started ) AS earliest_activity, max(tmr.time_started + INTERVAL duration SECOND) as latest_activity
                    FROM ".TBL_TIMERS." AS tmr INNER JOIN ".TBL_TASKS." AS tsk ON tmr.task=tsk.ID
                    WHERE tsk.project_name='$project' AND
                    tmr.time_started between '$from' and '$to'
                    GROUP BY tsk.ID";
            
        } else if ($group=="user"){
            $q = "SELECT tmr.user as user, sum(tmr.duration) as duration, min( tmr.time_started ) AS earliest_activity, max(tmr.time_started + INTERVAL duration SECOND) as latest_activity
                    FROM ".TBL_TIMERS." AS tmr INNER JOIN ".TBL_TASKS." AS tsk ON tmr.task=tsk.ID
                    WHERE tsk.project_name='$project' AND
                    tmr.time_started between '$from' and '$to'
                    GROUP BY tmr.user";
        } else if ($group=="plain"){
            $q = "SELECT tmr.time_started AS time_started,user,task,duration
                    FROM ".TBL_TIMERS." AS tmr INNER JOIN ".TBL_TASKS." AS tsk ON tmr.task=tsk.ID
                    WHERE tsk.project_name='$project' AND
                    tmr.time_started between '$from' and '$to'";
        }
        $result = $this->query($q);
        if ($result){
            return $this->mysql_fetch_full_result_array($result);
        } else{
            return FALSE;
        }
    }
    
    function getTimesheetTask($task,$group,$from,$to){
        if ($group=="user"){
            $q = "SELECT user, sum(duration) AS duration, min( time_started ) AS earliest_activity, max(time_started + INTERVAL duration SECOND) as latest_activity
                    FROM ".TBL_TIMERS."
                    WHERE task=$task AND
                    time_started between '$from' and '$to'
                    GROUP BY user";
        } else if ($group=="plain"){
            $q = "SELECT time_started,user,duration
                    FROM ".TBL_TIMERS."
                    WHERE task=$task AND
                    time_started between '$from' and '$to'";
        }
        $result = $this->query($q);
        if ($result){
            return $this->mysql_fetch_full_result_array($result);
        } else{
            return FALSE;
        }
    }
    
    function getTimesheetUser($user,$group,$from,$to){
        if ($group=="task"){
            $q = "SELECT task, sum(duration) AS duration, min( time_started ) AS earliest_activity, max(time_started + INTERVAL duration SECOND) as latest_activity
                    FROM ".TBL_TIMERS."
                    WHERE user='$user' AND
                    time_started between '$from' and '$to'
                    GROUP BY task";
        } else if ($group=="project"){
            $q = "SELECT tsk.project_name as project,sum(tmr.duration) as duration, min( tmr.time_started ) AS earliest_activity, max(tmr.time_started + INTERVAL duration SECOND) as latest_activity
                    FROM ".TBL_TIMERS." AS tmr INNER JOIN ".TBL_TASKS." AS tsk ON tmr.task=tsk.ID
                    WHERE tmr.user='$user' AND
                    tmr.time_started between '$from' and '$to'
                    GROUP BY tsk.project_name";
        } else if ($group=="plain"){
            $q = "SELECT time_started,task,duration
                    FROM ".TBL_TIMERS."
                    WHERE user='$user' AND
                    time_started between '$from' and '$to'";
        }
        $result = $this->query($q);
        if ($result){
            return $this->mysql_fetch_full_result_array($result);
        } else{
            return FALSE;
        }
    }
    
    function getTimesheetPlain($from,$to){
        $q = "SELECT * FROM ".TBL_TIMERS." WHERE time_started between '$from' and '$to'";
        $result = $this->query($q);
        if ($result){
            return $this->mysql_fetch_full_result_array($result);
        } else{
            return FALSE;
        }
    }
    
    
    function addLiveTimer($username,$task){
        $q = "INSERT INTO ".TBL_ACTIVE_TIMERS." (user, task, time_started) VALUES ('$username',$task,'".date("Y-m-d H:i:s")."')";
        return $this->query($q);
        $this->touchTask($task);
    }
    
    function addTimer($username,$task,$time_started){
        $q = "DELETE FROM ".TBL_ACTIVE_TIMERS." WHERE user='$username' AND task='$task'";
        if ($this->query($q)){
            $ret=1;
        }
        else $ret=0;
        
        $duration = time() - strtotime($time_started);
        $q = "INSERT INTO ".TBL_TIMERS." (user, task, time_started,duration) VALUES ('$username',$task,'$time_started', $duration)";
        if ($this->query($q)){
            $ret+=2;
        }
        
        $this->touchTask($task);
        
        return $ret;
    }

    /* CLient Administration*/
    //Add New Client
    function addNewClient($clientFields, $addInfoEncode) {
        foreach ($clientFields as $key => $value) {
            $clientFields[$key] = mysql_real_escape_string($value);
        }
        $q = "INSERT INTO " . TBL_CLIENTS . " (fName,lName, Company_Name, Company_Type, TAX_Office, VAT_No, Address, Town, ZIP, Country, Additional_Info, created) VALUES ('$clientFields[firstname]', '$clientFields[lastname]', '$clientFields[companyName]', '$clientFields[companyType]', '$clientFields[taxOffice]','$clientFields[vatNumber]','$clientFields[address]','$clientFields[town]','$clientFields[zip]','$clientFields[country]','$addInfoEncode', '" . date("Y-m-d H:i:s") . "')";
        return mysql_query($q, $this->connection);
    }
    //Assign Client Id to Project
    function assignClientProject($projectName, $last_id) {
        $q = "UPDATE " . TBL_PROJECTS . " SET Client_ID = '$last_id' WHERE title = '$projectName'";
        return mysql_query($q, $this->connection); 
    }
    //Add Billing to a project
    function addNewBilling($billingFields) {
        foreach ($billingFields as $key => $value) {
            $billingFields[$key] = mysql_real_escape_string($value);
        }
        $q = "INSERT INTO " . TBL_TRANSACTIONS . " (typeOf, project_Name, amount, description, uploaded_files_URL, created) VALUES ('".Add_Billing."','$billingFields[proj]', $billingFields[amount], '$billingFields[description]', '$billingFields[fileURL]', '" . date("Y-m-d H:i:s") . "')";
        return mysql_query($q, $this->connection);
    }
    //Add Payment to a project
    function addNewPayment($paymentFields) {
        foreach ($paymentFields as $key => $value) {
            $paymentFields[$key] = mysql_real_escape_string($value);
        }
        $q = "INSERT INTO " . TBL_TRANSACTIONS . " (typeOf, project_Name, amount, description, created) VALUES ('".Add_Payment."','$paymentFields[proj]', $paymentFields[amount], '$paymentFields[description]', '" . date("Y-m-d H:i:s") . "')";
        return mysql_query($q, $this->connection);
    }
    //Add invoice to a project from payment tab
    function addNewPaymentInvoice($paymentFields) {
        foreach ($paymentFields as $key => $value) {
            $paymentFields[$key] = mysql_real_escape_string($value);
        }
        $q = "INSERT INTO " . TBL_TRANSACTIONS . " (typeOf, project_Name, amount, invoiceValues, uploaded_files_URL, created) VALUES ('".Add_Invoice."','$paymentFields[proj]', $paymentFields[invoiceVAT], '$paymentFields[invoiceValues]', '$paymentFields[fileURL]','" . date("Y-m-d H:i:s") . "')";
        return mysql_query($q, $this->connection);
    }

     //Add Invoice to a project
    function addNewInvoice($invoiceFields) {
        foreach ($invoiceFields as $key => $value) {
            $invoiceFields[$key] = mysql_real_escape_string($value);
        }
        $q = "INSERT INTO " . TBL_TRANSACTIONS . " (typeOf, project_Name, amount, invoiceValues, uploaded_files_URL, created) VALUES ('".Add_Invoice."','$invoiceFields[proj]', $invoiceFields[amount], '$invoiceFields[invoiceValues]', '$invoiceFields[fileURL]','" . date("Y-m-d H:i:s") . "')";
        return mysql_query($q, $this->connection);
    }
    //Add Expense to a project
    function addNewExpense($expenseFields) {
        foreach ($expenseFields as $key => $value) {
            $expenseFields[$key] = mysql_real_escape_string($value);
        }
        $q = "INSERT INTO " . TBL_TRANSACTIONS . " (typeOf, operator, amount, invoiceValues, uploaded_files_URL, created) VALUES ('".Add_Expence."','$expenseFields[operator]', $expenseFields[amount], '$expenseFields[invoiceValues]', '$expenseFields[fileURL]','" . date("Y-m-d H:i:s") . "')";
        return mysql_query($q, $this->connection);
    }

    //Add Withdraw to a user
    function addNewWithdraw($withdrawFields) {
        foreach ($withdrawFields as $key => $value) {
            $withdrawFields[$key] = mysql_real_escape_string($value);
        }
        $q = "INSERT INTO " . TBL_TRANSACTIONS . " (typeOf, user_withdraw, amount, description, created) VALUES ('".Add_Withdraw."','$withdrawFields[user]', $withdrawFields[amount],'$withdrawFields[description]', '" . date("Y-m-d H:i:s") . "')";
        return mysql_query($q, $this->connection);
    }
     //Add Deposit to a user
    function addNewDeposit($depositFields) {
        foreach ($depositFields as $key => $value) {
            $depositFields[$key] = mysql_real_escape_string($value);
        }
        $q = "INSERT INTO " . TBL_TRANSACTIONS . " (typeOf, user_withdraw, amount, description, created) VALUES ('".Add_Deposit."','$depositFields[user]', $depositFields[amount],'$depositFields[description]', '" . date("Y-m-d H:i:s") . "')";
        return mysql_query($q, $this->connection);
    }
    function addNewContact($contactFields, $addContactInfoEncode) {
        foreach ($contactFields as $key => $value) {
            $contactFields[$key] = mysql_real_escape_string($value);
        }
        $q = "INSERT INTO " . TBL_CONTACTS . " (Position, fName, lName, Additional_Info, Project, created) VALUES ('$contactFields[position]','$contactFields[firstname]', '$contactFields[lastname]','$addContactInfoEncode', '$contactFields[proj]', '" . date("Y-m-d H:i:s") . "')";
        return mysql_query($q, $this->connection);
    }
    
    function addNewAppoinment($appoinmentFields,$datetime) {
        foreach ($appoinmentFields as $key => $value) {
            $appoinmentFields[$key] = mysql_real_escape_string($value);
        }
        
        $datetimeFormat = $datetime[0]." ".$datetime[1];
        $q = "INSERT INTO " . TBL_APPOINMENTS . " (appointmentdate, user, person_id, typeOf, description, Project, created) VALUES ('$datetimeFormat','$appoinmentFields[user]',$appoinmentFields[contactUser] ,'$appoinmentFields[typeOf]', '$appoinmentFields[description]', '$appoinmentFields[proj]','" . date("Y-m-d H:i:s") . "')";
        return mysql_query($q, $this->connection);
    }
    function addNewReportActivity($reportActivityFields) {
        foreach ($reportActivityFields as $key => $value) {
            $reportActivityFields[$key] = mysql_real_escape_string($value);
        }
        $q = "INSERT INTO " . TBL_REPORT_ACTIVITY . " (commentator, text_activity, timestamp, project, typeOfActivity) VALUES ('$reportActivityFields[user]','$reportActivityFields[activity]','" . date("Y-m-d H:i:s") . "','$reportActivityFields[project]','$reportActivityFields[typeOfActivity]')";
        return mysql_query($q, $this->connection);
    }
    
  

    //Populate array with Clients Details
    function allClientsArray(){
        $q = "SELECT clients.ID, CONCAT(clients.lName,' ',clients.fName) as name,clients.Additional_Info,client_totals.total AS total_due FROM ".TBL_CLIENTS." LEFT OUTER JOIN(
                SELECT Client_ID,sum(project_amounts.total) AS total FROM
                ".TBL_PROJECTS." INNER JOIN (
                    SELECT project_name, sum(amount) AS total
                    FROM ".TBL_TRANSACTIONS."
                    GROUP BY project_Name)
                AS project_amounts ON project_amounts.project_Name=projects.title
                GROUP BY Client_ID)
             AS client_totals
             ON Client_ID=clients.ID";
        $result = $this->query($q);
        $returned_rows = $this->mysql_fetch_full_result_array($result);
        if (!empty ($returned_rows)) {
            return $returned_rows;
        }
        else
            return false;
    }
    
    
    function getClientContactsPerProject($project){
        $q = "SELECT ID, Position, fName, lName, Additional_Info FROM ".TBL_CONTACTS." WHERE Project = '$project'";
        $result = $this->query($q);
        $returned_rows = $this->mysql_fetch_full_result_array($result);
        if (!empty ($returned_rows)) {
            return $returned_rows;
        }
        else {
            return false;
        }
            
    }
    
  
     function   getAppointmentsPerProject($project){
        $q = "SELECT appoinments.ID, appointmentDate, user, position, contacts.fName as contactFName, contacts.lName as contactLName, clients.fName as clientFName, clients.lName as clientLName, typeOf, description, notes
              FROM ".TBL_APPOINMENTS."
              LEFT OUTER JOIN ".TBL_CONTACTS." ON person_id = contacts.ID
              LEFT OUTER JOIN ".TBL_CLIENTS." ON person_id = clients.ID
              WHERE appoinments.Project='$project'";
        
        $result = $this->query($q);
        $returned_rows = $this->mysql_fetch_full_result_array($result);
        if (!empty ($returned_rows)) {
            return $returned_rows;
        }
        else {
            return false;
        }
            
    }
    
    function getReportActivityProject($project) {
        $q = "SELECT * FROM " . TBL_REPORT_ACTIVITY . " WHERE project='$project' ORDER BY timestamp DESC";
        $result = mysql_query($q, $this->connection);
        $activities = array();
        while ($activity = mysql_fetch_array($result)) {
            $activities[] = $activity;
        }
        if (count($activities) > 0)
            return $activities;
        else
            return;
    }
    //Populate array with a specific Client Details
    function getClientArray($selectedClientId){
        $q = "SELECT * FROM ".TBL_CLIENTS." WHERE ID = ".$selectedClientId;
        $result = $this->query($q);
        $returned_rows = mysql_fetch_array($result);
        if (!empty ($returned_rows)) {
            return $returned_rows;
        }
        else {
            return false;
        }
            
    }
    function getClientProjects($selectedClientId){
        $q = "SELECT title FROM ".TBL_PROJECTS." WHERE Client_ID =".$selectedClientId;
        $result = $this->query($q);
        $returned_rows = $this->mysql_fetch_full_result_array($result);
        if (!empty ($returned_rows)) {
            return $returned_rows;
        }
        else {
            return false;
        }
            
    }
    
    
    
    

    function activityLast30Array(){
        $q = "SELECT typeOf, project_Name, user_withdraw, operator, amount, created
                FROM ".TBL_TRANSACTIONS." ORDER BY created DESC LIMIT 30";
        $result = $this->query($q);
        $returned_rows = $this->mysql_fetch_full_result_array($result);
        if (!empty ($returned_rows)) {
            return $returned_rows;
        }
        else {
            return false;
        }
    }
    
      function clientActivityArray($selectedClientId){
        $q = "SELECT typeOf, project_Name, amount, invoiceValues, transactions.description, uploaded_files_URL, created
                FROM " . TBL_TRANSACTIONS . " INNER JOIN " . TBL_PROJECTS . " on title = project_Name
                WHERE Client_ID=" . $selectedClientId;
        $result = $this->query($q);
        $returned_rows = $this->mysql_fetch_full_result_array($result);
        if (!empty ($returned_rows)) {
            return $returned_rows;
        }
        else {
            return false;
        }
            
    }
    
    function editClient($value, $field, $id){
        
            $q = "UPDATE " . TBL_CLIENTS . " SET " . $field  . " = '$value' WHERE ID = '$id'";
        
        
        return $this->query($q);
    }
    
    function getCashierAmount(){
        $q = "SELECT sum( amount )
                FROM ".TBL_TRANSACTIONS."
                WHERE typeOf = ".Add_Payment."
                OR typeOf = ".Add_Expence."
                OR typeOf = ".Add_Withdraw." OR typeOf = ".Add_Deposit."";
        $result = $this->query($q);
        $row = mysql_fetch_array($result);
        if (!empty ($row)){
            return $row[0];
        }
        else return false;
    }


}

;

/* Create database connection */
$database = new MySQLDB;
?>