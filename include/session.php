<?

/**
 * Session.php
 * 
 * The Session class is meant to simplify the task of keeping
 * track of logged in users and also guests.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 19, 2004
 */
include("database.php"); //It includes constants.php
include("security.php");
include("mailer.php");
include("form.php");

class Session {

    public $username;     //Username given on sign-up
    var $userid;       //Random value generated on current login
    var $userlevel;    //The level to which the user pertains
    var $userlevel_in_words;
    var $time_last;         //Time user was last active (page loaded)
    var $logged_in;    //True if user is logged in, false otherwise
    var $userinfo = array();  //The array holding all user info
    var $url;          //The page url current being viewed
    var $referrer;     //Last recorded site page viewed
    var $web_name = "Gan Web Task Management"; //Sites name
    var $client_web_name = "Gan Web Client Administration"; //Sites name
    //Addon variables..
    var $avail_projects;   //Projects available to the user
    var $compl_projects = array();   //Projects with no task
    var $avail_tasks_straight = array(); //Array holding the available tasks, not grouped
    var $avail_tasks = array(); //  Array holding arrays of tasks for each available project to the user, grouped by project
    
    public $active_tasks;
    public $active_projects;
    public $timers_per_task;
    //var $assigned_tasks_by_project = array();
    //var $authored_tasks_by_project = array();
    //var $completed_tasks_by_project = array();
    //var $avail_tasks_for_edit = array();
    //var $avail_projects_for_edit = array();
    //Clients Administration
    var $all_clients;

    /**
     * Note: referrer should really only be considered the actual
     * page referrer in process.php, any other time it may be
     * inaccurate.
     */
    private $text_pattern = "/^([a-zA-Z0-9Ά-ώ\s\.,_!@#%&()\-]{1,255})$/u";

    /* Class constructor */

    function Session() {
        //$this->time = time();
        $this->startSession();
    }

    /**
     * startSession - Performs all the actions necessary to
     * initialize this session object. Tries to determine if the
     * the user has logged in already, and sets the variables
     * accordingly. Also takes advantage of this page load to
     * update the active visitors tables.
     */
    function startSession() {
        global $database;  //The database connection
        session_start();   //Tell PHP to start the session

        /* Determine if user is logged in */
        $this->logged_in = $this->checkLogin();

        /**
         * Set guest value to users not logged in, and update
         * active guests table accordingly.
         */
        if (!$this->logged_in) {
            $this->username = $_SESSION['username'] = GUEST_NAME;
            $this->userlevel = GUEST_LEVEL;
            $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
        }
        /* Update users last active timestamp */ else {
            $database->addActiveUser($this->username, $this->time);
        }

        /* Remove inactive visitors from database */
        $database->removeInactiveUsers();
        $database->removeInactiveGuests();
        $database->removeOldMessages();

        if (($_GET["action"]=="") && ($_POST["action"]=="")) {
            /* Set referrer page */
            if (isset($_SESSION['url'])) {
                $this->referrer = $_SESSION['url'];
            } else {
                $this->referrer = "/";
            }

            /* Set current url */
            if ($_SERVER['QUERY_STRING'] != '')
                $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];
            else
                $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
        }
    }

    /**
     * checkLogin - Checks if the user has already previously
     * logged in, and a session with the user has already been
     * established. Also checks to see if user has been remembered.
     * If so, the database is queried to make sure of the user's
     * authenticity. Returns true if the user has logged in.
     */
    function checkLogin() {
        global $database, $constants;  //The database connection
        /* Check if user has been remembered */
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            $this->username = $_SESSION['username'] = $_COOKIE['cookname'];
            $this->userid = $_SESSION['userid'] = $_COOKIE['cookid'];
        }

        /* Username and userid have been set and not guest */
        if (isset($_SESSION['username']) && isset($_SESSION['userid']) &&
                $_SESSION['username'] != GUEST_NAME) {
            /* Confirm that username and userid are valid */
            if ($database->confirmUserID($_SESSION['username'], $_SESSION['userid']) != 0) {
                /* Variables are incorrect, user not logged in */
                unset($_SESSION['username']);
                unset($_SESSION['userid']);
                return false;
            }

            /* User is logged in, set class variables */
            $this->userinfo = $database->getUserInfo($_SESSION['username']);
            $this->username = $this->userinfo['username'];
            $this->userid = $this->userinfo['userid'];

            $userLevels = unserialize(USER_LVL);
            $this->userlevel_in_words = $userLevels[$this->userinfo['userlevel']];
            $this->userlevel = $this->userinfo['userlevel'];

            $this->time_last = $this->userinfo['timestamp'];
            $this->projectOrder = explode(",", $this->userinfo['projectOrder']);

            //case 1 User is Admin and can see every Project and every Task
            if ($this->isAdmin()) {
                $this->avail_projects = $database->getProjects(ALL);
            }
            //case 2 User is PA, Employee or Client
            else {
                $this->avail_projects = $database->getProjects(USER, $this->username);
            }

            //Set active_task AND active_projects
            $this->setAllTimers();
            
            if (count($this->avail_projects) > 0) {
                foreach ($this->avail_projects as $project) {
                    $this->avail_tasks[$project[title]] = $database->getTasksPro($project[title]);

                    if ($this->avail_tasks[$project[title]] == 0) {
                        $this->compl_projects[] = $project[title];
                    }
                    //TODO check: $this->avail_tasks_straight += $this->avail_tasks[$project[title]];
                    $this->avail_tasks_straight = array_merge((array) $this->avail_tasks_straight, (array) $this->avail_tasks[$project[title]]);
                }
            }

            foreach ($this->avail_tasks_straight as $task) {
                $tasks[$task[id]] = $task;
            }
            $this->avail_tasks_straight = $tasks;

            return true;
        }
        /* User not logged in */ else {
            return false;
        }
    }

    /**
     * login - The user has submitted his username and password
     * through the login form, this function checks the authenticity
     * of that information in the database and creates the session.
     * Effectively logging in the user if all goes well.
     */
    function getAssigned_Tasks_by_Project() {
        global $database;
        return $database->getAssignedTasks_per_Pro($this->username);
    }

    function getAuthored_Tasks_by_Project() {
        global $database;
        return $database->getAuthoredTasks_per_Pro($this->username);
    }

    function getCompleted_Tasks_by_Project() {
        global $database;
        if ($this->isAdmin()) {
            return $database->getAllCompletedTasks_per_Pro();
        } else {
            return $database->getCompletedTasks_per_Pro($this->username);
        }
    }

    function getEditTasks_by_ID() {
        global $database;
        if ($this->isAdmin()) {
            return $database->getAllTasks_ID();
        } else if ($this->userlevel == PROJECT_ADMIN_LEVEL) {
            return $database->getProjectAdminTasks_ID($this->username);
        } else if ($this->userlevel == EMPLOYEE_LEVEL) {
            return $database->getEmployeeTasks_ID($this->username);
        } else {
            return false;
        }
    }

    function login($subuser, $subpass, $subremember) {
        global $database, $form;  //The database and form object

        /* Username error checking */
        $field = "user";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "Username not entered");
        } else {
            /* Check if username is not alphanumeric */
            if (!eregi("^([0-9a-z])*$", $subuser)) {
                $form->setError($field, "Username not alphanumeric");
            }
        }

        /* Password error checking */
        $field = "pass";  //Use field name for password
        if (!$subpass) {
            $form->setError($field, "Password not entered");
        }

        /* Return if form errors exist */
        if ($form->num_errors > 0) {
            return false;
        }

        /* Checks that username is in database and password is correct */
        $subuser = stripslashes($subuser);
        $result = $database->confirmUserPass($subuser, md5($subpass));

        /* Check error codes */
        if ($result == 1) {
            $field = "user";
            $form->setError($field, "Username not found");
        } else if ($result == 2) {
            $field = "pass";
            $form->setError($field, "Invalid password");
        }

        /* Return if form errors exist */
        if ($form->num_errors > 0) {
            return false;
        }

        /* Username and password correct, register session variables */
        $this->userinfo = $database->getUserInfo($subuser);
        $this->username = $_SESSION['username'] = $this->userinfo['username'];
        $this->userid = $_SESSION['userid'] = $this->generateRandID();

        $userLevels = unserialize(USER_LVL);
        $this->userlevel_in_words = $userLevels[$this->userinfo['userlevel']];

        $this->userlevel = $this->userinfo['userlevel'];


        /* Insert userid into database and update active users table */
        $database->updateUserField($this->username, "userid", $this->userid);
        $database->addActiveUser($this->username, $this->time);
        $database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

        /**
         * This is the cool part: the user has requested that we remember that
         * he's logged in, so we set two cookies. One to hold his username,
         * and one to hold his random value userid. It expires by the time
         * specified in constants.php. Now, next time he comes to our site, we will
         * log him in automatically, but only if he didn't log out before he left.
         */
        if ($subremember) {
            setcookie("cookname", $this->username, time() + COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid", $this->userid, time() + COOKIE_EXPIRE, COOKIE_PATH);
        }

        /* Login completed successfully */
        return true;
    }

    function client_login($subuser, $subpass) {
        global $database, $form;  //The database and form object

        /* Username error checking */
        $field = "user";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "Username not entered");
        } else {
            /* Check if username is not alphanumeric */
            if (!eregi("^([0-9a-z])*$", $subuser)) {
                $form->setError($field, "Username not alphanumeric");
            }
        }

        /* Password error checking */
        $field = "pass";  //Use field name for password
        if (!$subpass) {
            $form->setError($field, "Password not entered");
        }

        /* Return if form errors exist */
        if ($form->num_errors > 0) {
            return false;
        }

        /* Checks that username is in database and password is correct */
        $subuser = stripslashes($subuser);
        $result = $database->confirmUserPass($subuser, md5($subpass));

        /* Check error codes */
        if ($result == 1) {
            $field = "user";
            $form->setError($field, "Username not found");
        } else if ($result == 2) {
            $field = "pass";
            $form->setError($field, "Invalid password");
        }

        /* Return if form errors exist */
        if ($form->num_errors > 0) {
            return false;
        }

        /* Username and password correct, register session variables */
        $this->userinfo = $database->getUserInfo($subuser);
        $this->username = $_SESSION['username'] = $this->userinfo['username'];
        $this->userid = $_SESSION['userid'] = $this->generateRandID();

        $userLevels = unserialize(USER_LVL);
        $this->userlevel_in_words = $userLevels[$this->userinfo['userlevel']];

        $this->userlevel = $this->userinfo['userlevel'];


        /* Insert userid into database and update active users table */
        $database->updateUserField($this->username, "userid", $this->userid);
        $database->addActiveUser($this->username, $this->time);
        $database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

        /**
         * This is the cool part: the user has requested that we remember that
         * he's logged in, so we set two cookies. One to hold his username,
         * and one to hold his random value userid. It expires by the time
         * specified in constants.php. Now, next time he comes to our site, we will
         * log him in automatically, but only if he didn't log out before he left.
         */
        if ($subremember) {
            setcookie("cookname", $this->username, time() + COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid", $this->userid, time() + COOKIE_EXPIRE, COOKIE_PATH);
        }

        /* Login completed successfully */
        return true;
    }

    /**
     * logout - Gets called when the user wants to be logged out of the
     * website. It deletes any cookies that were stored on the users
     * computer as a result of him wanting to be remembered, and also
     * unsets session variables and demotes his user level to guest.
     */
    function logout() {
        global $database;  //The database connection
        /**
         * Delete cookies - the time must be in the past,
         * so just negate what you added when creating the
         * cookie.
         */
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            setcookie("cookname", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
        }

        /* Unset PHP session variables */
        unset($_SESSION['username']);
        unset($_SESSION['userid']);

        /* Reflect fact that user has logged out */
        $this->logged_in = false;

        /**
         * Remove from active users table and add to
         * active guests tables.
         */
        $database->removeActiveUser($this->username);
        $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);

        /* Set user level to guest */
        $this->username = GUEST_NAME;
        $this->userlevel = GUEST_LEVEL;
    }

    /**
     * register - Gets called when the user has just submitted the
     * registration form. Determines if there were any errors with
     * the entry fields, if so, it records the errors and returns
     * 1. If no errors were found, it registers the new user and
     * returns 0. Returns 2 if registration failed.
     */
    function register($subuser, $subpass, $subemail) {
        global $database, $form, $mailer;  //The database, form and mailer object

        /* Username error checking */
        $field = "user";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "* Username not entered");
        } else {
            /* Spruce up username, check length */
            $subuser = stripslashes($subuser);
            if (strlen($subuser) < 5) {
                $form->setError($field, "Username below 5 characters");
            } else if (strlen($subuser) > 30) {
                $form->setError($field, "Username above 30 characters");
            }
            /* Check if username is not alphanumeric */ else if (!eregi("^([0-9a-z])+$", $subuser)) {
                $form->setError($field, "Username not alphanumeric");
            }
            /* Check if username is reserved */ else if (strcasecmp($subuser, GUEST_NAME) == 0) {
                $form->setError($field, "Username reserved word");
            }
            /* Check if username is already in use */ else if ($database->usernameTaken($subuser)) {
                $form->setError($field, "Username already in use");
            }
            /* Check if username is banned */
            /*
              else if($database->usernameBanned($subuser)){
              $form->setError($field, "* Username banned");
              } */
        }

        /* Password error checking */
        $field = "pass";  //Use field name for password
        if (!$subpass) {
            $form->setError($field, "Password not entered");
        } else {
            /* Spruce up password and check length */
            $subpass = stripslashes($subpass);
            if (strlen($subpass) < 4) {
                $form->setError($field, "Password too short");
            }
            /* Check if password is not alphanumeric */ else if (!eregi("^([0-9a-z])+$", ($subpass = trim($subpass)))) {
                $form->setError($field, "Password not alphanumeric");
            }
            /**
             * Note: I trimmed the password only after I checked the length
             * because if you fill the password field up with spaces
             * it looks like a lot more characters than 4, so it looks
             * kind of stupid to report "password too short".
             */
        }

        /* Email error checking */
        $field = "email";  //Use field name for email
        if (!$subemail || strlen($subemail = trim($subemail)) == 0) {
            $form->setError($field, "Email not entered");
        } else {
            /* Check if valid email address */
            $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                    . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                    . "\.([a-z]{2,}){1}$";
            if (!eregi($regex, $subemail)) {
                $form->setError($field, "Email invalid");
            }
            $subemail = stripslashes($subemail);
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->addNewUser($subuser, md5($subpass), $subemail)) {
                if (EMAIL_WELCOME) {
                    $mailer->sendWelcome($subuser, $subemail, $subpass);
                }
                return 0;  //New user added succesfully
            } else {
                return 2;  //Registration attempt failed
            }
        }
    }

    /**
     * editAccount - Attempts to edit the user's account information
     * including the password, which it first makes sure is correct
     * if entered, if so and the new password is in the right
     * format, the change is made. All other fields are changed
     * automatically.
     */
    function editAccount($subcurpass, $subnewpass, $subemail, $avatar) {
        global $database, $form;  //The database and form object
        /* New password entered */
        if ($subnewpass) {
            /* Current Password error checking */
            $field = "curpass";  //Use field name for current password
            if (!$subcurpass) {
                $form->setError($field, "Current Password not entered");
            } else {
                /* Check if password too short or is not alphanumeric */
                $subcurpass = stripslashes($subcurpass);
                if (strlen($subcurpass) < 4 ||
                        !eregi("^([0-9a-z])+$", ($subcurpass = trim($subcurpass)))) {
                    $form->setError($field, "Current Password incorrect");
                }
                /* Password entered is incorrect */
                if ($database->confirmUserPass($this->username, md5($subcurpass)) != 0) {
                    $form->setError($field, "Current Password incorrect");
                }
            }

            /* New Password error checking */
            $field = "newpass";  //Use field name for new password
            /* Spruce up password and check length */
            $subpass = stripslashes($subnewpass);
            if (strlen($subnewpass) < 4) {
                $form->setError($field, "New Password too short");
            }
            /* Check if password is not alphanumeric */ else if (!eregi("^([0-9a-z])+$", ($subnewpass = trim($subnewpass)))) {
                $form->setError($field, "New Password not alphanumeric");
            }
        }
        /* Change password attempted */ else if ($subcurpass) {
            /* New Password error reporting */
            $field = "newpass";  //Use field name for new password
            $form->setError($field, "New Password not entered");
        }

        /* Email error checking */
        $field = "email";  //Use field name for email
        if ($subemail && strlen($subemail = trim($subemail)) > 0) {
            /* Check if valid email address */
            $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                    . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                    . "\.([a-z]{2,}){1}$";
            if (!eregi($regex, $subemail)) {
                $form->setError($field, "Email invalid");
            }
            $subemail = stripslashes($subemail);
        }

        $field = "avatar";
        if ($avatar != "") {
            //$avatar = stripslashes($avatar);
            if (!preg_match("/^http/", $avatar)) {
                $avatar = "http://" . $_SERVER["HTTP_HOST"] . "/" . $avatar;
            }

            $avatar_info = getimagesize($avatar);


            if (empty($avatar_info["mime"])) {
                $form->setError($field, "Συνέβη λάθος με το URL");
            } else {
                try {

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_HEADER, true);
                    curl_setopt($ch, CURLOPT_NOBODY, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_URL, $avatar); //specify the url
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    $head = curl_exec($ch);

                    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
                    $size = round($size / (1024), 2);
                } catch (Exception $exc) {
                    $size = 1000;
                }


                //$size = filesize($avatar);

                if ($size > 500) {
                    $form->setError($field, "Η εικόνα πρέπει να είναι κάτω από 500kb");
                } else if (floor($avatar_info[1] / $avatar_info[0]) > 2) {
                    $form->setError($field, "Η εικόνα πρέπει να έχει ratio έως 1:2");
                }
            }
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return false;  //Errors with form
        } else {
            /* Update password since there were no errors */
            if ($subcurpass && $subnewpass) {
                $database->updateUserField($this->username, "password", md5($subnewpass));
            }

            /* Change Email */
            if ($subemail) {
                $database->updateUserField($this->username, "email", $subemail);
            }

            if ($avatar) {
                $database->updateUserField($this->username, "avatar", $avatar);
            }

            /* Success! */
            return true;
        }
    }

    //Project Manipulation Functions
    function newproject($title, $desc, $url, $domain, $host) {
        global $database, $form, $mailer;
        $title_exists = false;
        if ($url == "http://") {
            unset($url);
        }
        $date_pattern = "#(0[1-9]|[12][0-9]|3[01])[/](0[1-9]|1[12])[/](19[0-9]{2}|[2][0-9][0-9]{2})#";

        $title_exists = $database->security_ProjectExists(ALL, $title);

        $field = "title";
        if (!$title || strlen($title = trim($title)) == 0) {
            $form->setError($field, "* Δεν έχει οριστεί τίτλος");
        } else if (strlen($title) < 5) {
            $form->setError($field, "* Δώστε όνομα Project πάνω απο 5 χαρακτήρες");
        }
        //greek check for proper format (μαζί με τόνους) [1-zA-Z0-1\p{Greek}@.\s]
        else if (!preg_match($this->text_pattern, $title)) {
            $form->setError($field, "* O τίτλος δεν είναι αλφαριθμητικός");
        } else if ($title_exists) {
            $form->setError($field, "* O τίτλος αυτός υπάρχει ήδη!");
        }
        //else $asd="123";

        if (!empty($desc)) {
            $field = "desc";
            if (!preg_match($this->text_pattern, $desc)) {
                $form->setError($field, "* H περιγραφή δεν είναι αλφαριθμητική");
            }
        }
        if (!empty($url)) {
            $field = "url";
            $url_pattern = '^((http|https|ftp)\://)?([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|localhost|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$^';
            if (!preg_match($url_pattern, $url)) {
                $form->setError($field, "* Δεν είναι σωστό το URL");
            }
        }

        if (!empty($domain)) {
            $field = "domain";
            if (!preg_match($date_pattern, $domain)) {
                $form->setError($field, "* Δεν είναι σωστή η ημ/νία");
            } else {
                $temp = explode("/", $domain);
                $domain = "'" . $temp[2] . "-" . $temp[1] . "-" . $temp[0] . "'";
            }
        }
        else
            $domain = 'NULL';

        if (!empty($host)) {
            $field = "host";
            if (!preg_match($date_pattern, $host)) {
                $form->setError($field, "* Δεν είναι σωστή η ημ/νία");
            } else {
                $temp = explode("/", $host);
                $host = "'" . $temp[2] . "-" . $temp[1] . "-" . $temp[0] . "'";
            }
        }
        else
            $host = 'NULL';

        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        } else {
            if ($database->addNewProject($title, $url, $domain, $host, $desc, $this->username)) {
                $database->activities_AddNewProject($this->username, $title);
                $mailer->newProjectNotification($title, $this->username);
                return 0; //Everything went fine!
            }
            else
                return 2; // New Project failed
        }
    }

    function delproject($title) {
        global $database, $form, $mailer;
        if ($database->deleteProject($title)) {
            $database->activities_DeleteProject($this->username, $title);
            $mailer->delProjectNotification($title, $this->username);
            return 0; //Everything went fine!
        }
        else
            return 2; // Del Project failed
    }

    function editProject($title, $desc, $url, $domain, $host, $old) {
        global $database, $form, $mailer;

        $title_exists = false;
        $old_exists = false;
        $date_pattern = "#(0[1-9]|[12][0-9]|3[01])[/](0[1-9]|1[12])[/](19[0-9]{2}|[2][0-9][0-9]{2})#";

        if ($url == "http://") {
            unset($url);
        }

        if ($title != $old) {
            $title_exists = $database->security_ProjectExists(ALL, $title);
        }

        //case 1 User is Admin and can see every Project and every Task
        if ($this->isAdmin()) {
            $old_exists = $database->security_ProjectExists(ALL, $old);
        }
        //case 2 User is PA, Employee or Client
        else {
            //We check if The user Can change the Selected Project
            //for example if he tries to change a project that he is not eligible to
            // $old_exists => FALSE
            $old_exists = $database->security_ProjectExists(USER, $old, $this->username);
        }

        $field = "title";
        if (!$title || strlen($title = trim($title)) == 0) {
            $form->setError($field, "Δεν έχει οριστεί τίτλος");
        } else if (strlen($title) < 5) {
            $form->setError($field, "Δώστε όνομα Project πάνω απο 5 χαρακτήρες");
        }
        //greek check for proper format (μαζί με τόνους) [1-zA-Z0-1\p{Greek}@.\s]
        else if (!preg_match($this->text_pattern, $title)) {
            $form->setError($field, "O τίτλος δεν είναι αλφαριθμητικός");
        } else if ($title_exists) {
            $form->setError($field, "O τίτλος αυτός υπάρχει ήδη!");
        } else if (!$old_exists) {
            $form->setError($field, "Συνέβη κάποιο λάθος με τον τίτλο!");
        }
        //else $asd="123";

        if (!empty($desc)) {
            $field = "desc";
            if (!preg_match($this->text_pattern, $desc)) {
                $form->setError($field, "H περιγραφή δεν είναι αλφαριθμητική");
            }
        }
        if (!empty($url)) {
            $field = "url";
            $url_pattern = '^((http|https|ftp)\://)?([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|localhost|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$^';
            if (!preg_match($url_pattern, $url)) {
                $form->setError($field, "Δεν είναι σωστό το URL");
            }
        }

        if (!empty($domain)) {
            $field = "domain";
            if (!preg_match($date_pattern, $domain)) {
                $form->setError($field, "Δεν είναι σωστή η ημ/νία");
            } else {
                $temp = explode("/", $domain);
                $domain = "'" . $temp[2] . "-" . $temp[1] . "-" . $temp[0] . "'";
            }
        }
        else
            $domain = 'NULL';

        if (!empty($host)) {
            $field = "host";
            if (!preg_match($date_pattern, $host)) {
                $form->setError($field, "Δεν είναι σωστή η ημ/νία");
            } else {
                $temp = explode("/", $host);
                $host = "'" . $temp[2] . "-" . $temp[1] . "-" . $temp[0] . "'";
            }
        }
        else
            $host = 'NULL';

        $field = "user_error";
        /*
          $eligible_users = array_merge($database->db_getProjectUsers($old, PROJECT_ADMIN_LEVEL, EMPLOYEE_LEVEL), array_keys($database->db_getUsers(SUPER_ADMIN_LEVEL, ADMIN_LEVEL)));
          if (!in_array($this->username, array_values($eligible_users))) {
          $form->setError($field, "Λάθος χρήστης!");
          } */

        //thema thema else $form->setError($field, "* ola kala!".$this->username." ");


        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        } else {

            //thema thema
            //if($database->addNewTask($title,$desc,$priority,$project,$this->username,$assigned_array)){
            //edw bainei to database->addnewproject
            //
            if ($database->updateProject($title, $url, $domain, $host, $desc, $old)) {
                $database->activities_EditProject($this->username, $title);
                $mailer->editProjectNotification($title, $this->username);
                return 0; //Everything went fine!
            }
            else
                return 2; // New Project failed
        }
    }

    //Session functions for project assignement
    function addUserstoProject($assign_array, $project) {
        global $database, $form, $mailer;

        $field = "add_users";
        $mistakes = $this->array_in_array_mistakes($assign_array, $this->availableUsers_for_project_assignement($project, SERIALIZED));
        if ($mistakes > 0) {
            $form->setError($field, "Συνέβη λάθος με τους χρήστες");
        }

        $field = "project";
        if (!isset($this->avail_projects[$project])) {
            $form->setError($field, "Λάθος με το project!");
        }


        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        } else {
            if ($database->addProjectUsers($assign_array, $project)) {
                $database->activities_AddAssignProject($this->username, $project, implode(",", $assign_array));
                $mailer->editProjectNotification($project, $this->username);
                return 0; //Everything went fine!
            }
            else
                return 2; // New Project failed
        }
    }

    function removeUsersfromProject($assign_array, $project) {
        global $mailer, $database, $form; //mailer probably

        $field = "remove_users";
        $mistakes = $this->array_in_array_mistakes($assign_array, $this->availableUsers_for_project_removal($project, SERIALIZED));
        if ($mistakes > 0) {
            $form->setError($field, "Συνέβη λάθος με τους χρήστες");
        }

        $field = "project";
        if (!isset($this->avail_projects[$project])) {
            $form->setError($field, "Λάθος με το project!");
        }


        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        } else {
            if (($database->removeProjectUsers($assign_array, $project)) && ($database->updateTaskProjectUsers($assign_array, $project, $this->username))) {
                $database->activities_RemoveAssignProject($this->username, $project, implode(",", $assign_array));
                $mailer->editProjectNotification($project, $this->username);
                return 0; //Everything went fine!
            }
            else
                return 2; // Removing User from Project FAILED
        }
    }

    function newtask($title, $desc, $priority, $assignedto, $project) {
        global $database, $form, $mailer;  //The database, form and mailer object

        $assigned_array = explode(",", $assignedto);

        //Check assigned users array!
        //$self_array[] = $this->username;

        $self = "Self (" . $this->username . ")";

        if (in_array($this->username, $assigned_array)) {
            $index = array_search($this->username, $assigned_array);
            array_splice($assigned_array, $index, 1);
            array_splice($assigned_array, 0, 0, $self);
        }

        $not_eligible_users = $this->array_in_array_mistakes($assigned_array, $this->getUsers_on_Project($project, SERIALIZED_NONCLIENT));



        foreach ($this->avail_projects as $avail_project) {
            $projects[] = $avail_project[title];
        }

        //Error checking of task_title
        $field = "task_title_" . $project;
        if (!$title || strlen($title = trim($title)) == 0) {
            $form->setError($field, "Δεν έχει οριστεί τίτλος");
        } else if (strlen($title) < 5) {
            $form->setError($field, "Δώστε όνομα Task πάνω απο 5 χαρακτήρες");
        }
        //greek check for proper format (μαζί με τόνους)
        else if (!preg_match($this->text_pattern, $title)) {
            $form->setError($field, "O τίτλος δεν είναι αλφαριθμητικός");
        }

        //Error checking for assigned users
        $field = "task_assignement" . $project;
        if (!$assignedto || strlen($assignedto = trim($assignedto)) == 0) {
            $form->setError($field, "Ορίστε άτομο/άτομα για ανάθεση του Task");
        } else if ($not_eligible_users > 0) {
            $form->setError($field, "Λάθος στα άτομα προς ανάθεση του Task");
        } else if (!in_array($project, $projects, true)) {
            $form->setError($field, "Σφάλμα στην επιλογή του project!");
        }


        if (in_array($self, $assigned_array)) {
            $index = array_search($self, $assigned_array);
            array_splice($assigned_array, $index, 1);
            array_splice($assigned_array, 0, 0, $this->username);
        }

        if ($form->num_errors > 0) {
            $form->setError("vis_add_task" . $project, "none");
            return 1;  //Errors with form
        } else {
            if ($database->addNewTask($title, $desc, $priority, $project, $this->username, $assigned_array)) {
                $database->activities_AddNewTask($this->username, $project, $title);

                $from_start = TRUE;
                $database->activities_AddAssignTask($this->username, $project, $title, implode(",", $assigned_array), $from_start);

                $mailer->newTaskNotification($title, $project, $this->username, $assigned_array);
                return 0; //Everything went fine!
            }
            else
                return 2; // New Task failed
        }
    }

    function edittask($id, $title, $desc, $project) {
        global $database, $form, $mailer;

        $field = "id";
        $edit_taks = $this->getEditTasks_by_ID();
        if (is_array($edit_taks)) {
            if (!in_array($id, $edit_taks)) {
                $form->setError($field, "Δεν είναι διαθέσιμο αυτό το Task");
            }
        }

        $field = "title";
        if (!$title || strlen($title = trim($title)) == 0) {
            $form->setError($field, "Δεν έχει οριστεί τίτλος");
        } else if (strlen($title) < 5) {
            $form->setError($field, "Δώστε όνομα Task πάνω απο 5 χαρακτήρες");
        }
        //greek check for proper format (μαζί με τόνους)
        else if (!preg_match($this->text_pattern, $title)) {
            $form->setError($field, "O τίτλος δεν είναι αλφαριθμητικός");
        }

        $field = "desc";
        if (!preg_match($this->text_pattern, $desc)) {
            $form->setError($field, "Η περιγραφή δεν είναι αλφαριθμητική");
        }

        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        } else {
            if ($database->editTask($id, $title, $desc)) {
                $database->activities_EditTask($this->username, $project, $title);
                $mailer->editTaskNotification($title, $project, $this->username);
                return 0; //Everything went fine!
            }
            else
                return 2;
        }
    }

    function deletetask($id, $task, $project) {
        global $database, $form, $mailer;

        $field = "id";
        $edit_taks = $this->getEditTasks_by_ID();
        if (is_array($edit_taks)) {
            if (!in_array($id, $edit_taks)) {
                $form->setError($field, "Δεν είναι διαθέσιμο αυτό το Task");
            }
        }

        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        } else {
            if ($database->delTask($id)) {
                $database->activities_DeleteTask($this->username, $project, $task);
                $mailer->deleteTaskNotification($task, $project, $this->username);
                return 0; //Everything went fine!
            }
            else
                return 2;
        }
    }

    function updatetask($task_id) {
        global $database, $form;
        //getEditTasks_by_ID()
        $temp = $this->getEditTasks_by_ID();
        if (is_array($temp)) {
            if (in_array($task_id, $temp)) {
                if ($database->updateTaskStatus($task_id)) {
                    $task_details = $this->avail_tasks_straight[$task_id];
                    $status_array = unserialize(STATUS);
                    $status = array_search($task_details[status], $status_array);
                    $status-=1;
                    $database->activities_UpgradeTask($this->username, $task_details[project_name], $task_details[title], $status);
                    return 0;
                }
                else
                    return 1;
            }
            else
                return 2;
        }
        else
            return 2;
    }

    function downgradetask($task_id) {
        global $database, $form;
        $temp = $this->getEditTasks_by_ID();
        if ($temp != false) {
            if (in_array($task_id, $temp)) {
                if ($database->downgradeTaskStatus($task_id)
                ) {
                    $task_details = $this->avail_tasks_straight[$task_id];
                    $status_array = unserialize(STATUS);
                    $status = array_search($task_details[status], $status_array);
                    $status+=1;
                    $database->activities_DowngradeTask($this->username, $task_details[project_name], $task_details[title], $status);
                    return 0;
                }
                else
                    return 1;
            }
            else
                return 2;
        }
        else
            return 2;
    }

    function editTaskStatus_AJAX($task_id, $action_sts) {
        global $database;

        $live_sts = $database->getTaskStatus($task_id);

        if ($action_sts == "upd_sts" && $live_sts != "Completed") {
            $retval = $this->updatetask($task_id);
        } else if ($action_sts == "down_sts" && $live_sts != "Awaiting Confirmation") {
            $retval = $this->downgradetask($task_id);
        }

        else
            $retval = "fail";

        $live_sts = $database->getTaskStatus($task_id);

        /*
          if ($live_sts_int == "Awaiting Confirmation"){}

          else if (($live_sts_int == "In Progress") or ($live_sts_int == "Not Started")){}

          else if ($live_sts_int == "Completed"){}
         */

        return $live_sts;
    }

    function addUserstoTask($assign_array, $task) {
        global $database, $form; //mailer probably

        $avail_tasks = $this->getEditTasks_by_ID();

        if (!in_array($task, $avail_tasks)) {
            $form->setError($field, "Λάθος με το task!" . $avail_tasks[7]);
        }


        $field = "users";

        $task_details = $this->avail_tasks_straight[$task];
        $avail_users = $this->getUsers_on_Project($task_details[project_name], SERIALIZED_NONCLIENT);
        $avail_users[0] = ltrim($avail_users[0], "Self (");
        $avail_users[0] = rtrim($avail_users[0], ")");

        $assigned = explode(", ", $task_details[assigned_list]);
        $avail_users = $this->array_not_in_array($avail_users, $assigned);
        $mistakes = $this->array_in_array_mistakes($assign_array, $avail_users);

        if ($mistakes > 0 or empty($assign_array)) {
            $form->setError($field, "Συνέβη λάθος με τους χρήστες");
        }



        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        } else {
            if ($database->addTaskUsers($assign_array, $task)) {
                $from_start = FALSE;
                $database->activities_AddAssignTask($this->username, $task_details[project_name], $task_details[title], implode(",", $assign_array), $from_start);
                return 0; //Everything went fine!
            }
            else
                return 2; // New Project failed
        }
    }

    function removeUsersfromTask($assign_array, $task) {
        global $database, $form; //mailer probably

        $avail_tasks = $this->getEditTasks_by_ID();
        //(!in_array($task,$avail_tasks)){
        if (!in_array($task, $avail_tasks)) {
            $form->setError($field, "Λάθος με το task!");
        }


        $field = "users";

        $task_details = $this->avail_tasks_straight[$task];
        $assigned = explode(", ", $task_details[assigned_list]);
        $mistakes = $this->array_in_array_mistakes($assign_array, $assigned);
        if (count($assigned) == 1)
            $mistakes++;
        if ($mistakes > 0) {
            $form->setError($field, "Συνέβη λάθος με τους χρήστες");
        }



        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        } else {
            if ($database->removeTaskUsers($assign_array, $task)) {
                $from_start = FALSE;
                $database->activities_RemoveAssignTask($this->username, $task_details[project_name], $task_details[title], implode(",", $assign_array), $from_start);
                return 0; //Everything went fine!
            }
            else
                return 2; // New Project failed
        }
    }

    //Project Based Working Users
    /*     * getUsers_on_Project - get Users which are on Project
     * in 3 modes.
     * 1. Grouped in Admins, PAs,Employees or Clients
     * 2. Serialized with full details
     * 3. Serialized only with username
     * */
    function getUsers_on_Project($project, $mode) {

        global $database;

        //$database->db_getProjectUsers($selected_project,PROJECT_ADMIN_LEVEL,EMPLOYEE_LEVEL,CLIENT_LEVEL);

        switch ($mode) {

            case GROUPED:
                if ($this->isAdmin()) {
                    $ad_users = array_keys($database->db_getUsers(SUPER_ADMIN_LEVEL, ADMIN_LEVEL));
                    foreach ($ad_users as $value) {
                        $users["Admins"][$value] = $value;
                    }

                    $users["Project Admins"] = $database->db_getProjectUsers($project, PROJECT_ADMIN_LEVEL);
                    $users["Employees"] = $database->db_getProjectUsers($project, EMPLOYEE_LEVEL);
                    $users["Clients"] = $database->db_getProjectUsers($project, CLIENT_LEVEL);
                } else if ($this->isPA()) {
                    $users["Employees"] = $database->db_getProjectUsers($project, EMPLOYEE_LEVEL);
                    $users["Clients"] = $database->db_getProjectUsers($project, CLIENT_LEVEL);
                }
                break;

            case DETAILS:
                if ($this->isAdmin()) {
                    $users = $database->db_getUsers(SUPER_ADMIN_LEVEL, ADMIN_LEVEL);
                    $users = array_merge($users, $database->db_getProjectUsers($project, PROJECT_ADMIN_LEVEL, EMPLOYEE_LEVEL, CLIENT_LEVEL));
                } else if ($this->isPA()) {
                    $users = $database->db_getProjectUsers($project, EMPLOYEE_LEVEL);
                    $users[$this->username] = $this->userinfo;
                } else if ($this->isWokring()) {
                    $users[$this->username] = $this->userinfo;
                }
                break;

            case SERIALIZED:
                if ($this->isAdmin()) {
                    $users = array_keys($database->db_getUsers(SUPER_ADMIN_LEVEL, ADMIN_LEVEL));
                    $users = array_merge($users, array_keys($database->db_getProjectUsers($project, PROJECT_ADMIN_LEVEL, EMPLOYEE_LEVEL, CLIENT_LEVEL)));
                } else if ($this->isPA()) {
                    $users = array_keys($database->db_getProjectUsers($project, EMPLOYEE_LEVEL, CLIENT_LEVEL));
                    $users[] = $this->username;
                } else if ($this->isWokring()) {
                    $users[] = $this->username;
                }
                break;

            case SERIALIZED_NONCLIENT:
                $self = "Self (" . $this->username . ")";

                if ($this->isAdmin()) {
                    $users = array_keys($database->db_getUsers(SUPER_ADMIN_LEVEL, ADMIN_LEVEL));
                    $users = array_merge($users, array_keys($database->db_getProjectUsers($project, PROJECT_ADMIN_LEVEL, EMPLOYEE_LEVEL)));
                    $index = array_search($this->username, $users);
                    array_splice($users, $index, 1);
                    array_splice($users, 0, 0, $self);
                } else if ($this->isPA()) {
                    //$users[] = $self;
                    $users = array_keys($database->db_getProjectUsers($project, EMPLOYEE_LEVEL));
                    array_splice($users, 0, 0, $self);
                } else if ($this->isWokring()) {
                    $users[] = $self;
                }
                break;

            default:
                //return FALSE;
                break;
        }
        return $users;
    }

    function getUserAvatar($username) {
        global $database;
        $user_info = $database->getUserInfo($username);
        $src = $user_info["avatar"];
        return $src;
    }

    /*
      function availableUsers_for_project_assignement($project,$mode) {
      global $database;

      if ($this->isAdmin())
      $avail_users = $this->array_not_in_array(array_keys($database->db_getUsers(PROJECT_ADMIN_LEVEL, EMPLOYEE_LEVEL, CLIENT_LEVEL)), $this->getUsers_on_Project($project, SERIALIZED));
      else if ($this->isPA())
      $avail_users = $this->array_not_in_array(array_keys($database->db_getUsers(EMPLOYEE_LEVEL, CLIENT_LEVEL)), $this->getUsers_on_Project($project, SERIALIZED));
      unset($avail_users[$this->username]);


      return $avail_users;
      } */

    function availableUsers_for_project_assignement($project, $mode) {
        global $database;

        $project_users = $this->getUsers_on_Project($project, SERIALIZED);

        if ($mode == SERIALIZED) {
            if ($this->isAdmin())
                $possible_available = array_keys($database->db_getUsers(PROJECT_ADMIN_LEVEL, EMPLOYEE_LEVEL, CLIENT_LEVEL));
            else if ($this->isPA())
                $possible_available = array_keys($database->db_getUsers(EMPLOYEE_LEVEL, CLIENT_LEVEL));

            $avail_users = $this->array_not_in_array($possible_available, $project_users);
            unset($avail_users[$this->username]);
        }
        else if ($mode == GROUPED) {
            if ($this->isAdmin()) {
                $avail_users["Project Admins"] = $this->array_not_in_array_by_keys($database->db_getUsers(PROJECT_ADMIN_LEVEL), $project_users);
                $avail_users["Employees"] = $this->array_not_in_array_by_keys($database->db_getUsers(EMPLOYEE_LEVEL), $project_users);
                $avail_users["Clients"] = $this->array_not_in_array_by_keys($database->db_getUsers(CLIENT_LEVEL), $project_users);
            } else if ($this->isPA()) {
                $avail_users["Employees"] = $this->array_not_in_array_by_keys($database->db_getUsers(EMPLOYEE_LEVEL), $project_users);
                $avail_users["Clients"] = $this->array_not_in_array_by_keys($database->db_getUsers(CLIENT_LEVEL), $project_users);
            }

            foreach ($avail_users as $key => $value) {
                if (empty($value))
                    unset($avail_users[$key]);
            }
        }
        return $avail_users;
    }

    /*
      function availableUsers_for_project_assignement_grouped($project) {
      global $database;
      if ($this->isAdmin()) {
      $avail_users["Project Admins"] = $this->array_not_in_array_by_keys($database->db_getUsers(PROJECT_ADMIN_LEVEL), $this->getUsers_on_Project($project, SERIALIZED));
      $avail_users["Employees"] = $this->array_not_in_array_by_keys($database->db_getUsers(EMPLOYEE_LEVEL), $this->getUsers_on_Project($project, SERIALIZED));
      $avail_users["Clients"] = $this->array_not_in_array_by_keys($database->db_getUsers(CLIENT_LEVEL), $this->getUsers_on_Project($project, SERIALIZED));
      } else if ($this->isPA()) {
      $avail_users["Employees"] = $this->array_not_in_array_by_keys($database->db_getUsers(EMPLOYEE_LEVEL), $this->getUsers_on_Project($project, SERIALIZED));
      $avail_users["Clients"] = $this->array_not_in_array_by_keys($database->db_getUsers(CLIENT_LEVEL), $this->getUsers_on_Project($project, SERIALIZED));
      }

      foreach ($avail_users as $key => $value) {
      if (empty($value))
      unset($avail_users[$key]);
      }

      return $avail_users;
      } */

    function availableUsers_for_project_removal($project, $mode) {
        global $database;

        if ($mode == SERIALIZED) {
            $avail_users = $this->getUsers_on_Project($project, SERIALIZED);
            /*
              if ($this->isAdmin()) {
              $avail_users = $database->db_getProjectUsers($project, PROJECT_ADMIN_LEVEL, EMPLOYEE_LEVEL, CLIENT_LEVEL);
              } else if ($this->isPA())
              $avail_users = $database->db_getProjectUsers($project, EMPLOYEE_LEVEL, CLIENT_LEVEL);

              unset($avail_users[$this->username]);
             */
        } else if ($mode == GROUPED) {
            $avail_users = $this->getUsers_on_Project($project, GROUPED);
            unset($avail_users["Admins"]);
            unset($avail_users["Project Admins"][$this->username]);
            foreach ($avail_users as $key => $value) {
                if (empty($value))
                    unset($avail_users[$key]);
            }
        }

        return $avail_users;
    }

    function availableUsers_for_project_removal_grouped($project) {
        global $database;

        $avail_users = $this->getUsers_on_Project($project, GROUPED);
        unset($avail_users["Admins"]);
        unset($avail_users["Project Admins"][$this->username]);
        foreach ($avail_users as $key => $value) {
            if (empty($value))
                unset($avail_users[$key]);
        }
        return $avail_users;
    }

    //Comments Function
    function commentsoftask($task_id) {
        global $database;
        //$mailer->notifyAdmins($this->username);
        if (isset($this->avail_tasks_straight[$task_id])) {
            return $database->getCommentsTask($task_id);
        }
        else
            return false;
    }

    function addComment($comment, $task) {
        global $database, $form;

        $comment=mysql_real_escape_string($comment);
        $task=mysql_real_escape_string($task);

        $field = "comment";
        if (empty($comment)) {
            $form->setError($field, "Δώστε κείμενο σχολίου!");
        }
        $field = "task";
        if (empty($task)) {
            $form->setError($field, "Συνέβη λάθος με το task!");
        } else if (!isset($this->avail_tasks_straight[$task])) {
            $form->setError($field, "Συνέβη λάθος με το task!");
        }

        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        } else {
            if ($database->addComment($this->username, $comment, $task)) {
                $task_details = $this->avail_tasks_straight[$task];
                $database->activities_CommentOnTask($this->username, $task_details[project_name], $task_details[title]);
                return 0; //Everything went fine!
            }
            else
                return 2; // New Comment failed
        }
    }
    function addCustomTimer($task,$mins){
        global $database, $form;

        $mins=(int)mysql_real_escape_string($mins);
        $task=mysql_real_escape_string($task);

        $field = "task";
        if (empty($task)) {
            $form->setError($field, "Συνέβη λάθος με το task!");
        } else if (!isset($this->avail_tasks_straight[$task])) {
            $form->setError($field, "Συνέβη λάθος με το task!");
        }
        
        $field="mins";
        if (empty($mins)){
            $form->setError($field, "Fill in Minutes");
        } else if(!is_numeric ($mins)){
            $form->setError($field, "Wrong Input");
        } else if($mins>=100){
            $form->setError($field, "Fill less than 100");
        } else if($mins<0){
            $form->setError($field, "fill positive number");
        } else{
            $secs = $mins*60;
        }

        $field = "vis_add_timer";
        if ($form->num_errors > 0) {
            $form->setError($field, "Errors");
            return 1;  //Errors with form
        } else {
            if ($database->addCustomTimer($this->username, $task, $secs)) {
                //$database->activities_CommentOnTask($this->username, $task_details[project_name], $task_details[title]);
                return 0; //Everything went fine!
            }
            else
                return 2; // New Timer failed
        }
    }

    function addProjectProperties($postarray) {
        global $database;

        if (preg_match("/^group_name/", key($postarray))) {

            //$info_array = array();
            reset($postarray);

            $counter = 0;
            $group = "new grp";

            foreach ($postarray as $name => $value) {
                if (preg_match("/^group_name/", $name)) {
                    $group = $value;
                    $order = 0;
                    $seperators = 0;
                } else if (preg_match("/^property_name/", $name)) {
                    $info_array[$group][$counter][name] = $value;
                    $info_array[$group][$counter][type] = 1; //Use constants
                    $info_array[$group][$counter][order] = $order;
                    $order++;
                } else if (preg_match("/^property_value/", $name)) {
                    $info_array[$group][$counter][value] = $value;
                    $counter++;
                } else if (preg_match("/^seperator/", $name)) {
                    $info_array[$group][$counter][name] = "seperator" . $seperators;
                    $info_array[$group][$counter][type] = 0; //Use constants
                    $info_array[$group][$counter][order] = $order;
                    $order++;
                    $counter++;
                    $seperators++;
                }
            }

            $project = $postarray[project];
            
            if ($database->db_addProperties($info_array, $project)) {
                return 0; //Everything Fine
            }
            else
                return 1;
        }
        else
            return 2; //Errors
    }

    function getCredentialsOfProject($project) {
        global $database;

        $credentials = (array)$database->getProjectCredentials($project);
        foreach ($credentials as $row) {
            $res[$row["properties_group"]][$row["property_order"]] = $row;
        }
        /*

        if (count($res) == 0) {
            $res[] = array("id" => "0", "project" => $project, "properties_group" => "Group Name", "property_name" => "Property Name", "property_value" => "Property Value", "type" => 0, "order" => 0);
        }*/

        return $res;
    }

    function updateProjectSortedList($order) {
        global $database;
        return $database->updateProjectOrder($this->username, $order);
    }
    
    /*Timer Functions*/
    function startTaskTiming($task){
        global $database;
        return $database->addLiveTimer($this->username,$task);
    }
    function stopTaskTiming($task){
        global $database;
        $active_timer = $database->getTimer($this->username,$task);
        //Check to see for error and permissions
        return $database->addTimer($this->username,$task,$active_timer[time_started]);
    }
    function setAllTimers(){
        global $database;
        $active_projects = array();
        $active_timers = $database->getActiveTimers();
        foreach ($active_timers as $timer) {
            if ($this->username==$timer[user]) $self_active=TRUE;
            else $self_active=FALSE;
            if (!in_array($timer[project], $active_projects)) $active_projects[]=$timer[project];
            
            $ret_timers[$timer[task]][timers][] = array("user"=>$timer[user],"time_started"=>$timer[time_started]);
            
            $ret_timers[$timer[task]][self_active] = ($ret_timers[$timer[task]][self_active] || $self_active);
            //array("user"=>$timer[user],"time_started"=>$timer[time_started],"self_active"=>$self_active);
        }
        $this->active_tasks = $ret_timers;
        $this->active_projects = $active_projects;
        $this->timers_per_task = $database->getTotalTimers(GROUPED);
    }
    
    /*TimeSheet Functions*/
    function AJAX_PopulateTasks($project){
        global $database;
        $tasks = $database->getAllTasksPro($project);
        foreach ($tasks as $task) {
            echo "<option value=\"$task[id]\">$task[title]</option>";
        }
    }
    
    function getTimesheet($post_array){
        global $database,$form,$security;
        $from_date = mysql_escape_string($post_array[from_date]);
        $to_date = mysql_escape_string($post_array[to_date]);
        $field="from_date";
        if (!$security->valid_EUDate($from_date)){
            $form->setError($field, "Not a valid date");
        } else{
            $from_date_sql = $this->dateChangeFormat($from_date)." 00:00:00";
        }
        $field="to_date";
        if (!$security->valid_EUDate($to_date)){
            $form->setError($field, "Not a valid date");
        } else if (!$security->greaterDate($from_date,$to_date)){
            $form->setError($field, "This is an earlier date");
        } else{
            $to_date_sql = $this->dateChangeFormat($to_date)." 23:59:59";
        }
        
        
        if ($form->num_errors > 0) {
            $ret = 1;//There were for erros
            return FALSE;  //Errors with form
        } else {
            $filter = mysql_escape_string($post_array[lvl1filter]);
            $group = mysql_escape_string($post_array[lvl2Group]);
            switch ($filter) {
                case "project":
                    $project = mysql_escape_string($post_array[lvl1Project]);
                    $field="project";
                    if ($this->isAdmin()) {
                        $exists = $database->security_ProjectExists(ALL, $project);
                    }
                    else {
                        $exists = $database->security_ProjectExists(USER, $project, $this->username);
                    }
                    if ($exists){
                        $field="lvl2Group";
                        if ($group=="plain" || $group=="user" || $group=="task"){
                            $timesheet = $database->getTimesheetProject($project,$group,$from_date_sql,$to_date_sql);
                            if (!$timesheet){
                                $ret = 3; //There were No Results
                            }
                            else{
                                $ret[table][$project][timesheet] = $timesheet;
                                $ret[table][$project][header] = "Project: $project";
                                $ret[dates][0] = $from_date;
                                $ret[dates][1] = $to_date;
                            }
                        } else{
                            $form->setError($field, "Error with the \"Group by\" filter");
                            $ret = 1;
                        }

                    } else{
                        $form->setError($field, "This project does not exist");
                        $ret = 1;
                    }
                    break;
                case "task":
                    $task = $post_array[lvl1Task];
                    
                    $field = "task";
                    $available = TRUE;// TODO: afto tha allazei
                    /* TODO: Na ginei swsto to avail_tasks_straight
                    if (!in_array($task, $this->avail_tasks_straight)){
                        $form->setError($field, "The task is not available");
                    }*/
                    
                    if ($available){
                        $field="lvl2Group";
                        if ($group=="plain" || $group=="user"){
                            $timesheet = $database->getTimesheetTask($task,$group,$from_date_sql,$to_date_sql);
                            
                            if (!$timesheet){
                                $ret = 3; //There were No Results
                            }
                            else{
                                $ret[table][$task][timesheet] = $timesheet;
                                $ret[table][$task][header] = "Task: $task";//need project and also Task name
                                $ret[dates][0] = $from_date;
                                $ret[dates][1] = $to_date;
                            }
                        } else{
                            $form->setError($field, "Error with the \"Group by\" filter");
                            $ret = $group;
                        }
                    }
                    //$form->setError("big message", "Not yet Implemented");
                    break;
                case "user":
                    $users = $post_array[lvl1User];
                    //echo $users;
                    
                    //$ret = $users;
                    
                    $field = "user";
                    $available = TRUE;// TODO: afto tha allazei
                    /* TODO: Na ginei swstos elegxos sto requested user
                    */
                    
                    if ($available){
                        $field="lvl2Group";
                        if ($group=="project" || $group=="task" || $group="plain"){
                            foreach ($users as $user) {
                                $result = $database->getTimesheetUser($user,$group,$from_date_sql,$to_date_sql);
                                if ($result){
                                    $timesheets[$user][timesheet] = $result;
                                    $timesheets[$user][header] = "User: $user";
                                }
                            }
                            
                            $ret[table] = $timesheets;
                            $ret[type] = "multiple";
                            
                            if (!$ret[table]){
                                $ret = 3; //There were No Results
                            }
                            else{
                                //$ret[header] = "User: $user";
                                $ret[dates][0] = $from_date;
                                $ret[dates][1] = $to_date;
                            }
                        } else{
                            $form->setError($field, "Error with the \"Group by\" filter");
                            $ret = 1;
                        }
                    }
                    break;
                case "none":
                    $result = $database->getTimesheetPlain($from_date_sql,$to_date_sql);
                    if ($result){
                        $timesheets[0][timesheet] = $result;
                        $timesheets[0][header] = "Full Records";
                    }
                    $ret[table] = $timesheets;

                    if (!$ret[table]){
                        $ret = 3; //There were No Results
                    }
                    else{
                        //$ret[header] = "User: $user";
                        $ret[dates][0] = $from_date;
                        $ret[dates][1] = $to_date;
                    }
                    //$form->setError("big message", "Not yet Implemented");
                    break;
                default:
                    $form->setError("general", "Select one of the filters");
                    break;
            }
            return $ret;
        }
    }

    /**
     * isAdmin - Returns true if currently logged in user is
     * an administrator, false otherwise.
     */
    /*
      function isAdmin(){
      return ($this->userlevel == SUPER_ADMIN_LEVEL ||
      $this->username  == ADMIN_NAME);
      } */

    function isSuperAdmin() {
        return ($this->userlevel == SUPER_ADMIN_LEVEL);
    }

    function isAdmin() {
        return ($this->userlevel <= ADMIN_LEVEL);
    }

    function isPA() {
        return ($this->userlevel <= PROJECT_ADMIN_LEVEL);
    }

    function isWokring() {
        return ($this->userlevel <= EMPLOYEE_LEVEL);
    }

    //Supplementary functions

    /**
     * generateRandID - Generates a string made up of randomized
     * letters (lower and upper case) and digits and returns
     * the md5 hash of it to be used as a userid.
     */
    function generateRandID() {
        return md5($this->generateRandStr(16));
    }

    /**
     * generateRandStr - Generates a string made up of randomized
     * letters (lower and upper case) and digits, the length
     * is a specified parameter.
     */
    function generateRandStr($length) {
        $randstr = "";
        for ($i = 0; $i < $length; $i++) {
            $randnum = mt_rand(0, 61);
            if ($randnum < 10) {
                $randstr .= chr($randnum + 48);
            } else if ($randnum < 36) {
                $randstr .= chr($randnum + 55);
            } else {
                $randstr .= chr($randnum + 61);
            }
        }
        return $randstr;
    }

    function array_in_array_mistakes($arr1, $arr2) {
        $mistakes = 0;
        foreach ($arr1 as $element_arr1) {
            if (!in_array($element_arr1, $arr2))
                $mistakes++;
        }
        return $mistakes;
    }

    function array_not_in_array($arr1, $arr2) {
        $mistakes = array();
        foreach ($arr1 as $element_arr1) {
            if (!in_array($element_arr1, $arr2))
                $mistakes[$element_arr1] = $element_arr1;
        }
        return $mistakes;
    }

    function array_not_in_array_by_keys($arr1, $arr2) {
        $mistakes = array();
        foreach ($arr1 as $key1 => $value1) {
            if (!in_array($key1, $arr2))
                $mistakes[$key1] = $value1;
        }
        return $mistakes;
    }

    function nicetime($date) {

        if (empty($date)) {
            return "No date provided";
        }

        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");
        $now = time();
        $unix_date = strtotime($date);
        if (empty($unix_date)) {
            return "Bad date";
        }
        // is it future date or past date
        if ($now > $unix_date) {
            $difference = $now - $unix_date;
            $tense = "ago";
        }

        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);
        if ($difference != 1) {
            $periods[$j].= "s";
        }
        return "$difference $periods[$j] {$tense}";
    }

    function sortArrayByArray($array, $orderArray) {
        $ordered = array();
        foreach ((array) $orderArray as $key) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        return $ordered + $array;
    }

    /* Client Administration
     *
     *
     *
     * Gan Web RULZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ
     *
     *
     *
     */

    /* New Client Create */

    function client_register($clientFields) {
        global $database, $form, $mailer;  //The database, form and mailer object

        /* First name error checking */
        $field = "fname";  //Use field name for first name
        if (!$clientFields[firstname] || strlen($clientFields[firstname] = trim($clientFields[firstname])) == 0) {
            $form->setError($field, "* First Name not entered");
        } else {
            /* Spruce up first name, check length */
            $clientFields[firstname] = stripslashes($clientFields[firstname]);
            if (strlen($clientFields[firstname]) < 3) {
                $form->setError($field, "First Name below 3 characters");
            } else if (strlen($clientFields[firstname]) > 30) {
                $form->setError($field, "First Name above 30 characters");
            }/* Check if first name is not alphanumeric */ else if (!preg_match($this->text_pattern, $clientFields[firstname])) {
                $form->setError($field, "First Name not alphanumeric");
            }
        }

        $field = "lname";  //Use field name for last name
        if (!$clientFields[lastname] || strlen($clientFields[lastname] = trim($clientFields[lastname])) == 0) {
            $form->setError($field, "*Last Name not entered");
        } else {
            /* Spruce up Last Name, check length */
            $clientFields[lastname] = stripslashes($clientFields[lastname]);
            if (strlen($clientFields[lastname]) < 3) {
                $form->setError($field, "Last Name below 3 characters");
            } else if (strlen($clientFields[lastname]) > 30) {
                $form->setError($field, "Last Name above 30 characters");
            }
            /* Check if Last Name is not alphanumeric */ else if (!preg_match($this->text_pattern, $clientFields[lastname])) {
                $form->setError($field, "Last Name not alphanumeric");
            }
        }

        $field = "telephone";  //Use field name for telephone
        if (!empty($clientFields[telephone])) {
            /* Spruce up telephone, check length */
            $clientFields[telephone] = stripslashes($clientFields[telephone]);

            if (!preg_match("/^[0-9]{1,}$/", $clientFields[telephone])) {
                $form->setError($field, "Telephone not numeric");
            } else if (strlen($clientFields[telephone]) > 10) {
                $form->setError($field, "Telephone above 10 numbers");
            } else if (strlen($clientFields[telephone]) < 10) {
                $form->setError($field, "Telephone below 10 numbers");
            }
        }

        $field = "mobile";  //Use field name for mobile
        /* Spruce up mobile, check length */
        $clientFields[mobile] = stripslashes($clientFields[mobile]);
        if (!empty($clientFields[mobile])) {
            if (!preg_match("/^[0-9]{1,}$/", $clientFields[mobile])) {
                $form->setError($field, "Mobile not numeric");
            } else if (strlen($clientFields[mobile]) > 10) {
                $form->setError($field, "Mobile above 10 numbers");
            } else if (strlen($clientFields[mobile]) < 10) {
                $form->setError($field, "Mobile below 10 numbers");
            }
        }

        $field = "fax";  //Use field name for fax
        /* Spruce up fax, check length */
        $clientFields[fax] = stripslashes($clientFields[fax]);
        if (!empty($clientFields[fax])) {
            if (!preg_match("/^[0-9]{1,}$/", $clientFields[fax])) {
                $form->setError($field, "Fax not numeric");
            } else if ((strlen($clientFields[fax]) > 10)) {
                $form->setError($field, "Fax above 10 numbers");
            } else if ((strlen($clientFields[fax]) < 10)) {
                $form->setError($field, "Fax below 10 numbers");
            }
        }

        /* Email error checking */
        $field = "email";  //Use field name for email
        if (!$clientFields[email] || strlen($clientFields[email] = trim($clientFields[email])) == 0) {
            $form->setError($field, "Email not entered");
        } else {
            /* Check if valid email address */
            $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                    . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                    . "\.([a-z]{2,}){1}$";
            if (!eregi($regex, $clientFields[email])) {
                $form->setError($field, "Email invalid");
            }
            $clientFields[email] = stripslashes($clientFields[email]);
        }

        $field = "companyName";  //Use field name for companyName
        /* Spruce up companyName, check length */
        $clientFields[companyName] = stripslashes($clientFields[companyName]);
        if (!empty($clientFields[companyName])) {
            if (!preg_match($this->text_pattern, $clientFields[companyName])) {
                $form->setError($field, "Company Name not alphanumeric");
            }
        }

        $field = "companyType";  //Use field name for companyType
        /* Spruce up companyType, check length */
        $clientFields[companyType] = stripslashes($clientFields[companyType]);
        if (!empty($clientFields[companyType])) {
            if (!preg_match($this->text_pattern, $clientFields[companyType])) {
                $form->setError($field, "Company Type not alphanumeric");
            }
        }

        $field = "vatNumber";  //Use field name for vatNumber
        /* Spruce up vatNumber, check length */
        $clientFields[vatNumber] = stripslashes($clientFields[vatNumber]);
        if (!empty($clientFields[vatNumber])) {
            if (!preg_match("/^[0-9]{1,}$/", $clientFields[vatNumber])) {
                $form->setError($field, "Vat Number not numeric");
            } else if ((strlen($clientFields[vatNumber]) > 9)) {
                $form->setError($field, "Vat Number above 9 numbers");
            } else if ((strlen($clientFields[vatNumber]) < 9)) {
                $form->setError($field, "Vat Number below 9 numbers");
            }
        }

        $field = "address";  //Use field name for address
        /* Spruce up address, check length */
        $clientFields[address] = stripslashes($clientFields[address]);
        if (!empty($clientFields[address])) {
            if (!preg_match($this->text_pattern, $clientFields[address])) {
                $form->setError($field, "Address not alphanumeric");
            }
        }

        $field = "town";  //Use field name for town
        /* Spruce up town, check length */
        $clientFields[town] = stripslashes($clientFields[town]);
        if (!empty($clientFields[town])) {
            if (!preg_match($this->text_pattern, $clientFields[town])) {
                $form->setError($field, "Town not alphanumeric");
            }
        }

        $field = "zip";  //Use field name for zip
        /* Spruce up zip, check length */
        $clientFields[zip] = stripslashes($clientFields[zip]);
        if (!empty($clientFields[zip])) {
            if (!preg_match("/^[0-9]{1,}$/", $clientFields[zip])) {
                $form->setError($field, "ZIP not numeric");
            } else if ((strlen($clientFields[zip]) > 5)) {
                $form->setError($field, "ZIP above 5 numbers");
            } else if ((strlen($clientFields[zip]) < 5)) {
                $form->setError($field, "ZIP below 5 numbers");
            }
        }

        $field = "country";  //Use field name for country
        /* Spruce up country, check length */
        $clientFields[country] = stripslashes($clientFields[country]);
        if (!empty($clientFields[country])) {
            if (!preg_match($this->text_pattern, $clientFields[country])) {
                $form->setError($field, "Country not alphanumeric");
            }
        }

        $field = "facebook";  //Use field name for facebook
        /* Spruce up facebook, check length */
        $clientFields[facebook] = stripslashes($clientFields[facebook]);
        if (!empty($clientFields[facebook])) {
            if (!preg_match($this->text_pattern, $clientFields[facebook])) {
                $form->setError($field, "Facebook not alphanumeric");
            }
        }
        $field = "twitter";  //Use field name for twitter
        /* Spruce up twitter, check length */
        $clientFields[twitter] = stripslashes($clientFields[twitter]);
        if (!empty($clientFields[twitter])) {
            if (!preg_match($this->text_pattern, $clientFields[twitter])) {
                $form->setError($field, "Twitter not alphanumeric");
            }
        }
        $field = "skype";  //Use field name for skype
        /* Spruce up skype, check length */
        $clientFields[skype] = stripslashes($clientFields[skype]);
        if (!empty($clientFields[skype])) {
            if (!preg_match($this->text_pattern, $clientFields[skype])) {
                $form->setError($field, "Skype not alphanumeric");
            }
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $form->setError("vis_add_client", "none");
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            $addInfo = array('Telephone' => $clientFields[telephone], 'Mobile' => $clientFields[mobile], 'Fax' => $clientFields[fax], 'Email' => $clientFields[email], 'Facebook' => $clientFields[facebook], 'Twitter' => $clientFields[twitter], 'Skype' => $clientFields[skype]);
            $addInfoEncode = json_encode($addInfo);
            if ($database->addNewClient($clientFields, $addInfoEncode)) {
                /* if (EMAIL_WELCOME) {
                  $mailer->sendWelcome($subuser, $subemail, $subpass);
                  } */
                $last_id = mysql_insert_id();
                $database->assignClientProject($clientFields[proj], $last_id);
                return 0;  //New user added succesfully
            } else {
                $form->setError("vis_add_client", "none");
                return 2;  //Registration attempt failed
            }
        }
    }

    /* New Billing Create */

    function billing_create($billingFields) {
        global $database, $form, $mailer;  //The database, form and mailer object

        /* Client error checking */
        $field1 = "client";  //Client field name for client
        if (!$billingFields[client] || strlen($billingFields[client] = trim($billingFields[client])) == 0) {
            $form->setError($field1, "* Client not entered");
        }

        $field = "proj";  //Project field name for client
        if (!$billingFields[proj] || strlen($billingFields[proj] = trim($billingFields[proj])) == 0) {
            $form->setError($field, "* Project not entered");
        } else if ($billingFields[client] == 0) {
            $form->setError($field1, "* Client not entered");
            $form->setError($field, "* Project not entered");
        }

        $field = "amount";  //Amount field name
        if (!$billingFields[amount] || strlen($billingFields[amount] = trim($billingFields[amount])) == 0) {
            $form->setError($field, "* Amount not entered");
        } else {
            /* Spruce up amount, check length */
            $billingFields[amount] = stripslashes($billingFields[amount]);

            if (!preg_match("/^\d+(\.(\d{1,2}))?$/", $billingFields[amount])) {
                $form->setError($field, "Amount not numeric");
            } else {
                $billingFields[amount] *= -1;
            }
        }


        $field = "description";  //Description field name
        /* Spruce up description, check length */
        $billingFields[description] = stripslashes($billingFields[description]);
        if (!empty($billingFields[description])) {
            if (!preg_match($this->text_pattern, $billingFields[description])) {
                $form->setError($field, "Description not alphanumeric");
            }
        }

        if (strpos($billingFields[fileURL], ",")) {
            $billingFields[fileURL] = explode(",", $billingFields[fileURL]);
        } else {
            //$billingFields[fileURL] = "http://tasks.gan-web/clients/financialOffers/".$billingFields[fileURL]."";
        }
        $billingFields[fileURL] = json_encode($billingFields[fileURL]);

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $form->setError("vis_add_billing", "none");
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->addNewBilling($billingFields)) {
                /* if (EMAIL_WELCOME) {
                  $mailer->sendWelcome($subuser, $subemail, $subpass);
                  }
                  $last_id = mysql_insert_id();
                  $database->assignClientProject($clientFields[proj],$last_id); */
                return 0;  //New user added succesfully
            } else {
                $form->setError("vis_add_billing", "none");
                return 2;  //Registration attempt failed
            }
        }
    }

    /* New Payment Create */

    function payment_create($paymentFields) {
        global $database, $form, $mailer;  //The database, form and mailer object

        /* Client error checking */
        $field1 = "clientPay";  //Client field name
        if (!$paymentFields[client] || strlen($paymentFields[client] = trim($paymentFields[client])) == 0) {
            $form->setError($field1, "* Client not entered");
        }

        $field = "proj";  //Project field name for client
        if (!$paymentFields[proj] || strlen($paymentFields[proj] = trim($paymentFields[proj])) == 0) {
            $form->setError($field, "* Project not entered");
        } else if ($paymentFields[client] == 0) {
            $form->setError($field1, "* Client not entered");
            $form->setError($field, "* Project not entered");
        }

        $field = "amount";  //Amount field name
        if (!$paymentFields[amount] || strlen($paymentFields[amount] = trim($paymentFields[amount])) == 0) {
            $form->setError($field, "* Amount not entered");
        } else {
            /* Spruce up amount, check length */
            $paymentFields[amount] = stripslashes($paymentFields[amount]);

            if (!preg_match("/^\d+(\.(\d{1,2}))?$/", $paymentFields[amount])) {
                $form->setError($field, "Amount not numeric");
            }
        }

        $field = "description";  //Description field name
        /* Spruce up description, check length */
        $paymentFields[description] = stripslashes($paymentFields[description]);
        if (!empty($paymentFields[description])) {
            if (!preg_match($this->text_pattern, $paymentFields[description])) {
                $form->setError($field, "Description not alphanumeric");
            }
        }
        $addInvoice = false;
        /* If Invoice Checked */
        if (isset($paymentFields[invoiceAmount])) {
            $field = "invoiceAmount";  //Amount field name
            if (!$paymentFields[invoiceAmount] || strlen($paymentFields[invoiceAmount] = trim($paymentFields[invoiceAmount])) == 0) {
                $form->setError($field, "* Amount not entered");
            } else {
                /* Spruce up amount, check length */
                $paymentFields[invoiceAmount] = stripslashes($paymentFields[invoiceAmount]);

                if (!preg_match("/^\d+(\.(\d{1,2}))?$/", $paymentFields[invoiceAmount])) {
                    $form->setError($field, "Amount not numeric");
                } else {
                    $invoiceAmounts[subTotal] = $paymentFields[invoiceAmount] / ($paymentFields[vat] + 1);
                    $invoiceAmounts[subTotal] = number_format($invoiceAmounts[subTotal], 2);
                    $invoiceAmounts[vat] = $invoiceAmounts[subTotal] * $paymentFields[vat];
                    $invoiceAmounts[vat] = number_format($invoiceAmounts[vat], 2);
                    $invoiceAmounts[total] = $invoiceAmounts[subTotal] + $invoiceAmounts[vat];
                    $invoiceAmounts[vatRate] = $paymentFields[vat];

                    $paymentFields[invoiceValues] = json_encode($invoiceAmounts);
                    $paymentFields[invoiceVAT] = -$invoiceAmounts[vat];
                    $addInvoice = true;
                }
            }
        }

        if (strpos($paymentFields[fileURL], ",")) {
            $paymentFields[fileURL] = explode(",", $paymentFields[fileURL]);
        } else {
            //$billingFields[fileURL] = "http://tasks.gan-web/clients/financialOffers/".$billingFields[fileURL]."";
        }
        $paymentFields[fileURL] = json_encode($paymentFields[fileURL]);


        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $form->setError("vis_add_payment", "none");
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->addNewPayment($paymentFields)) {
                if ($addInvoice) {
                    $database->addNewPaymentInvoice($paymentFields);
                }

                /* if (EMAIL_WELCOME) {
                  $mailer->sendWelcome($subuser, $subemail, $subpass);
                  }
                  $last_id = mysql_insert_id();
                  $database->assignClientProject($clientFields[proj],$last_id); */
                return 0;  //New user added succesfully
            } else {
                $form->setError("vis_add_payment", "none");
                return 2;  //Registration attempt failed
            }
        }
    }

    /* New Invoice Create */

    function invoice_create($invoiceFields) {
        global $database, $form, $mailer;  //The database, form and mailer object

        /* Client error checking */
        $field1 = "clientInvoice";  //Client field name
        if (!$invoiceFields[client] || strlen($invoiceFields[client] = trim($invoiceFields[client])) == 0) {
            $form->setError($field1, "* Client not entered");
        }

        $field = "proj";  //Project field name for client
        if (!$invoiceFields[proj] || strlen($invoiceFields[proj] = trim($invoiceFields[proj])) == 0) {
            $form->setError($field, "* Project not entered");
        } else if ($invoiceFields[client] == 0) {
            $form->setError($field1, "* Client not entered");
            $form->setError($field, "* Project not entered");
        }

        $field = "amount";  //Amount field name
        if (!$invoiceFields[amount] || strlen($invoiceFields[amount] = trim($invoiceFields[amount])) == 0) {
            $form->setError($field, "* Amount not entered");
        } else {
            /* Spruce up amount, check length */
            $invoiceFields[amount] = stripslashes($invoiceFields[amount]);

            if (!preg_match("/^\d+(\.(\d{1,2}))?$/", $invoiceFields[amount])) {
                $form->setError($field, "Amount not numeric");
            } else {
                $invoiceAmounts[subTotal] = $invoiceFields[amount] / ($invoiceFields[vat] + 1);
                $invoiceAmounts[subTotal] = number_format($invoiceAmounts[subTotal], 2);
                $invoiceAmounts[vat] = $invoiceAmounts[subTotal] * $invoiceFields[vat];
                $invoiceAmounts[vat] = number_format($invoiceAmounts[vat], 2);
                $invoiceAmounts[total] = $invoiceAmounts[subTotal] + $invoiceAmounts[vat];
                $invoiceAmounts[vatRate] = $invoiceFields[vat];

                $invoiceFields[invoiceValues] = json_encode($invoiceAmounts);
                $invoiceFields[amount] = -$invoiceAmounts[vat];
            }
        }

        if (strpos($invoiceFields[fileURL], ",")) {
            $invoiceFields[fileURL] = explode(",", $invoiceFields[fileURL]);
        } else {
            //$billingFields[fileURL] = "http://tasks.gan-web/clients/financialOffers/".$billingFields[fileURL]."";
        }
        $invoiceFields[fileURL] = json_encode($invoiceFields[fileURL]);

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $form->setError("vis_add_invoice", "none");
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->addNewInvoice($invoiceFields)) {
                /* if (EMAIL_WELCOME) {
                  $mailer->sendWelcome($subuser, $subemail, $subpass);
                  }
                  $last_id = mysql_insert_id();
                  $database->assignClientProject($clientFields[proj],$last_id); */
                return 0;  //New user added succesfully
            } else {
                $form->setError("vis_add_invoice", "none");
                return 2;  //Registration attempt failed
            }
        }
    }

    /* New Expense Create */

    function expense_create($expenseFields) {
        global $database, $form, $mailer;  //The database, form and mailer object

        /* Operator error checking */
        $field = "operator";  //Operator field name
        if (!$expenseFields[operator] || strlen($expenseFields[operator] = trim($expenseFields[operator])) == 0) {
            $form->setError($field, "* Operator not entered");
        }

        $field = "amount";  //Amount field name
        if (!$expenseFields[amount] || strlen($expenseFields[amount] = trim($expenseFields[amount])) == 0) {
            $form->setError($field, "* Amount not entered");
        } else {
            /* Spruce up amount, check length */
            $expenseFields[amount] = stripslashes($expenseFields[amount]);

            if (!preg_match("/^\d+(\.(\d{1,2}))?$/", $expenseFields[amount])) {
                $form->setError($field, "Amount not numeric");
            } else {
                $expenseAmounts[subTotal] = $expenseFields[amount] / ($expenseFields[vat] + 1);
                $expenseAmounts[subTotal] = number_format($expenseAmounts[subTotal], 2);
                $expenseAmounts[vat] = $expenseAmounts[subTotal] * $expenseFields[vat];
                $expenseAmounts[vat] = number_format($expenseAmounts[vat], 2);
                $expenseAmounts[total] = $expenseAmounts[subTotal] + $expenseAmounts[vat];
                $expenseAmounts[vatRate] = $expenseFields[vat];

                $expenseFields[invoiceValues] = json_encode($expenseAmounts);
                $expenseFields[amount] *= -1;
            }
        }

        if (strpos($expenseFields[fileURL], ",")) {
            $expenseFields[fileURL] = explode(",", $expenseFields[fileURL]);
        } else {
            //$billingFields[fileURL] = "http://tasks.gan-web/clients/financialOffers/".$billingFields[fileURL]."";
        }
        $expenseFields[fileURL] = json_encode($expenseFields[fileURL]);

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $form->setError("vis_add_expense", "none");
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->addNewExpense($expenseFields)) {
                /* if (EMAIL_WELCOME) {
                  $mailer->sendWelcome($subuser, $subemail, $subpass);
                  }
                  $last_id = mysql_insert_id();
                  $database->assignClientProject($clientFields[proj],$last_id); */
                return 0;  //New user added succesfully
            } else {
                $form->setError("vis_add_expense", "none");
                return 2;  //Registration attempt failed
            }
        }
    }

    /* New Withdraw Create */

    function withdraw_create($withdrawFields) {
        global $database, $form, $mailer;  //The database, form and mailer object

        /* User error checking */
        $field = "user";  //User field name
        if (!$withdrawFields[user] || strlen($withdrawFields[user] = trim($withdrawFields[user])) == 0) {
            $form->setError($field, "* User not entered");
        }

        $field = "amount";  //Amount field name 
        if (!$withdrawFields[amount] || strlen($withdrawFields[amount] = trim($withdrawFields[amount])) == 0) {
            $form->setError($field, "* Amount not entered");
        } else {
            $withdrawFields[amount] = stripslashes($withdrawFields[amount]);

            if (!preg_match("/^\d+(\.(\d{1,2}))?$/", $withdrawFields[amount])) {
                $form->setError($field, "Amount not numeric");
            } else {
                $withdrawFields[amount] *= -1;
            }
        }
        
       
        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $form->setError("vis_add_withdraw", "none");
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->addNewWithdraw($withdrawFields)) {
                /* if (EMAIL_WELCOME) {
                  $mailer->sendWelcome($subuser, $subemail, $subpass);
                  }
                  $last_id = mysql_insert_id();
                  $database->assignClientProject($clientFields[proj],$last_id); */
                return 0;  //New user added succesfully
            } else {
                $form->setError("vis_add_withdraw", "none");
                return 2;  //Registration attempt failed
            }
        }
    }
    
    function deposit_create($depositFields) {
        global $database, $form, $mailer;  //The database, form and mailer object

        /* User error checking */
        $field = "user";  //User field name
        if (!$depositFields[user] || strlen($depositFields[user] = trim($depositFields[user])) == 0) {
            $form->setError($field, "* User not entered");
        }

        $field = "amount";  //Amount field name 
        if (!$depositFields[amount] || strlen($depositFields[amount] = trim($depositFields[amount])) == 0) {
            $form->setError($field, "* Amount not entered");
        } else {
            $depositFields[amount] = stripslashes($depositFields[amount]);

            if (!preg_match("/^\d+(\.(\d{1,2}))?$/", $depositFields[amount])) {
                $form->setError($field, "Amount not numeric");
            }            
        }
        
       
        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $form->setError("vis_add_deposit", "none");
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->addNewDeposit($depositFields)) {
                /* if (EMAIL_WELCOME) {
                  $mailer->sendWelcome($subuser, $subemail, $subpass);
                  }
                  $last_id = mysql_insert_id();
                  $database->assignClientProject($clientFields[proj],$last_id); */
                return 0;  //New user added succesfully
            } else {
                $form->setError("vis_add_deposit", "none");
                return 2;  //Registration attempt failed
            }
        }
    }
    /* All Clients */

    function allClientsOverviewArray() {
        global $database;
        $allClients = $database->allClientsArray();
        $idx = -1;
        foreach ($allClients as $client) {
            $idx++;
            $result_clients[$idx][id] = $client[ID];
            $result_clients[$idx][name] = $client[name];
            $clientInfo = json_decode($client[Additional_Info]);
            $result_clients[$idx][telephone] = $clientInfo->{"Telephone"};
            $result_clients[$idx][mobile] = $clientInfo->{"Mobile"};
            $result_clients[$idx][email] = $clientInfo->{"Email"};
            $result_clients[$idx][total_due] = $client[total_due];
            $result_clients[$idx][Social][Facebook] = $clientInfo->{"Facebook"};
            $result_clients[$idx][Social][Twitter] = $clientInfo->{"Twitter"};
            $result_clients[$idx][Social][Skype] = $clientInfo->{"Skype"};
        }
        return $result_clients;
    }

    /* Specific Client */
    
    function getClientArray($selectedClientId) {
        global $database;
        $getClient = $database->getClientArray($selectedClientId);
        $getClientProjects = $database->getClientProjects($selectedClientId);

        $result_client[id] = $getClient[ID];
        $result_client[fName] = $getClient[fName];
        $result_client[lName] = $getClient[lName];
        $result_client[Company_Name] = $getClient[Company_Name];
        $result_client[Company_Type] = $getClient[Company_Type];
        $result_client[TAX_Office] = $getClient[TAX_Office];
        $result_client[VAT_No] = $getClient[VAT_No];
        $result_client[Address] = $getClient[Address];
        $result_client[Town] = $getClient[Town];
        $result_client[ZIP] = $getClient[ZIP];
        $result_client[Country] = $getClient[Country];
        $clientInfo = json_decode($getClient[Additional_Info]);
        $result_client[telephone] = $clientInfo->{"Telephone"};
        $result_client[mobile] = $clientInfo->{"Mobile"};
        $result_client[email] = $clientInfo->{"Email"};
        $result_client[Social][Facebook] = $clientInfo->{"Facebook"};
        $result_client[Social][Twitter] = $clientInfo->{"Twitter"};
        $result_client[Social][Skype] = $clientInfo->{"Skype"};
        $result_client[created] = strtotime($getClient[created]);
        if (date('Ymd') == date('Ymd', $result_client[created])) {
            $result_client[created] = date('\T\o\d\a\y  H:i, j/n/\'y', $result_client[created]);
        } else if (date('Ymd', strtotime('yesterday')) == date('Ymd', $result_client[created])) {
            $result_client[created] = date('\Y\e\s\t\e\r\d\a\y H:i, j/n/\'y', $result_client[created]);
        } else {
            $result_client[created] = date('l H:i, j/n/\'y', $result_client[created]);
        }
        $result_client[prjs] = json_encode($getClientProjects);


        return $result_client;
    }

    
    function getClientContactsPerProject($project) {
        global $database;
        $getContacts = $database->getClientContactsPerProject($project);
        $idx = -1;
        
        foreach ($getContacts as $contact) {
            $idx++;
            $result_contact[$idx][id] = $contact[ID];
            $result_contact[$idx][position] = $contact[Position];
            $result_contact[$idx][fName] = $contact[fName];
            $result_contact[$idx][lName] = $contact[lName];
            $contactInfo = json_decode($contact[Additional_Info]);
            $result_contact[$idx][telephone] = $contactInfo->{"Telephone"};
            $result_contact[$idx][mobile] = $contactInfo->{"Mobile"};
            $result_contact[$idx][email] = $contactInfo->{"Email"};
            $result_contact[$idx][fax] = $contactInfo->{"Fax"};
            $result_contact[$idx][Social][Facebook] = $contactInfo->{"Facebook"};
            $result_contact[$idx][Social][Twitter] = $contactInfo->{"Twitter"};
            $result_contact[$idx][Social][Skype] = $contactInfo->{"Skype"};
        }
        return $result_contact;
    }
    
    function getAppointmentsPerProject($project) {
        global $database;
        $getAppointments = $database->getAppointmentsPerProject($project);
        $idx = -1;
        
        foreach ($getAppointments as $appointment) {
            $idx++;
            $result_appointment[$idx][id] = $appointment[ID];
            
            $appointmentDateTimeArray = explode(" ", $appointment[appointmentDate]);
            $appointmentDate = explode("-", $appointmentDateTimeArray[0]);
            $result_appointment[$idx][date]= $appointmentDate[2]."/".$appointmentDate[1]."/".$appointmentDate[0];
            $appointmentTime = substr($appointmentDateTimeArray[1], 0, -3);
            $result_appointment[$idx][time] = $appointmentTime;
            $result_appointment[$idx][user] = $appointment[user];
            $result_appointment[$idx][person_id] = $appointment[person_id];
            $result_appointment[$idx][typeOf] = $appointment[typeOf];
            if ($appointment[typeOf]=="4001"){
                $result_appointment[$idx][$appointment[typeOf]][fName] = $appointment[clientFName];
                $result_appointment[$idx][$appointment[typeOf]][lName] = $appointment[clientLName];
            }else {
                $result_appointment[$idx][position] = $appointment[position];
                $result_appointment[$idx][$appointment[typeOf]][fName] = $appointment[contactFName];
                $result_appointment[$idx][$appointment[typeOf]][lName] = $appointment[contactLName];
            }
            $result_appointment[$idx][description] = $appointment[description];
            $result_appointment[$idx][notes] = $appointment[notes];
            
        }
        return $result_appointment;
    }
    
    function activityLast30() {
        global $database;
        $activityLast = $database->activityLast30Array();
        $idx = -1;
        foreach ($activityLast as $activity) {
            $idx++;
            $result_activity[$idx][type] = $activity[typeOf];
            $result_activity[$idx][project] = $activity[project_Name];
            $result_activity[$idx][user] = $activity[user_withdraw];
            $result_activity[$idx][operator] = $activity[operator];
            $result_activity[$idx][amount] = $activity[amount];
            $result_activity[$idx][happened] = strtotime($activity[created]);
            //$result_activity[$idx][happened] = date('j/n/\'y H:i', $result_activity[$idx][happened]);
            $result_activity[$idx][happened] = date("Y/m/d H:i",$result_activity[$idx][happened]);
            
        }
        return $result_activity;
    }

    function getClientActivity($selectedClientId) {
        global $database;
        $clientActivity = $database->clientActivityArray($selectedClientId);
        $idx = -1;

        foreach ($clientActivity as $activity) {
            $idx++;
            $result_activity[$activity[project_Name]][$idx][type] = $activity[typeOf];
            $result_activity[$activity[project_Name]][$idx][project] = $activity[project_Name];
            $result_activity[$activity[project_Name]][$idx][amount] = $activity[amount];
            $sum_amount[$activity[project_Name]] += $result_activity[$activity[project_Name]][$idx][amount];
            $result_activity[$activity[project_Name]][$idx][invoice] = $activity[invoiceValues];
            $result_activity[$activity[project_Name]][$idx][desc] = $activity[description];
            $result_activity[$activity[project_Name]][$idx][filesURL] = json_decode($activity[uploaded_files_URL]);
            $result_activity[$activity[project_Name]][$idx][happened] = strtotime($activity[created]);
            $happened = date('j/n/\'y H:i', $result_activity[$activity[project_Name]][$idx][happened]);
            $result_activity[$activity[project_Name]][$idx][happened] = $happened;
            $result_activity[$activity[project_Name]][sum_amount] = $sum_amount[$activity[project_Name]];
            $result_activity[sum_amount] += $sum_amount[$activity[project_Name]];
        }

        return $result_activity;
    }
    
    
    function contact_register($contactFields) {
        global $database, $form, $mailer;  //The database, form and mailer object

        /* Position error checking */
        $field = "position";  //Use field name for first name
        if (!$contactFields[position] || strlen($contactFields[position] = trim($contactFields[position])) == 0) {
            $form->setError($field, "* Position not entered");
        }else if (!preg_match($this->text_pattern, $contactFields[position])) {
                $form->setError($field, "Position not alphanumeric");
        }
        
        
        /* First name error checking */
        $field = "contact_fname";  //Use field name for first name
        if (!$contactFields[firstname] || strlen($contactFields[firstname] = trim($contactFields[firstname])) == 0) {
            $form->setError($field, "* First Name not entered");
        } else {
            /* Spruce up first name, check length */
            $contactFields[firstname] = stripslashes($contactFields[firstname]);
            if (strlen($contactFields[firstname]) < 3) {
                $form->setError($field, "First Name below 3 characters");
            } else if (strlen($contactFields[firstname]) > 30) {
                $form->setError($field, "First Name above 30 characters");
            }/* Check if first name is not alphanumeric */ else if (!preg_match($this->text_pattern, $contactFields[firstname])) {
                $form->setError($field, "First Name not alphanumeric");
            }
        }

        $field = "contact_lname";  //Use field name for last name
        if (!$contactFields[lastname] || strlen($clientFields[lastname] = trim($contactFields[lastname])) == 0) {
            $form->setError($field, "*Last Name not entered");
        } else {
            /* Spruce up Last Name, check length */
            $contactFields[lastname] = stripslashes($contactFields[lastname]);
            if (strlen($contactFields[lastname]) < 3) {
                $form->setError($field, "Last Name below 3 characters");
            } else if (strlen($contactFields[lastname]) > 30) {
                $form->setError($field, "Last Name above 30 characters");
            }
            /* Check if Last Name is not alphanumeric */ else if (!preg_match($this->text_pattern, $contactFields[lastname])) {
                $form->setError($field, "Last Name not alphanumeric");
            }
        }

        $field = "contact_telephone";  //Use field name for telephone
        if (!empty($contactFields[telephone])) {
            /* Spruce up telephone, check length */
            $contactFields[telephone] = stripslashes($contactFields[telephone]);

            if (!preg_match("/^[0-9]{1,}$/", $contactFields[telephone])) {
                $form->setError($field, "Telephone not numeric");
            } else if (strlen($contactFields[telephone]) > 10) {
                $form->setError($field, "Telephone above 10 numbers");
            } else if (strlen($contactFields[telephone]) < 10) {
                $form->setError($field, "Telephone below 10 numbers");
            }
        }

        $field = "contact_mobile";  //Use field name for mobile
        /* Spruce up mobile, check length */
        $contactFields[mobile] = stripslashes($contactFields[mobile]);
        if (!empty($contactFields[mobile])) {
            if (!preg_match("/^[0-9]{1,}$/", $contactFields[mobile])) {
                $form->setError($field, "Mobile not numeric");
            } else if (strlen($contactFields[mobile]) > 10) {
                $form->setError($field, "Mobile above 10 numbers");
            } else if (strlen($contactFields[mobile]) < 10) {
                $form->setError($field, "Mobile below 10 numbers");
            }
        }

        $field = "contact_fax";  //Use field name for fax
        /* Spruce up fax, check length */
        $contactFields[fax] = stripslashes($contactFields[fax]);
        if (!empty($contactFields[fax])) {
            if (!preg_match("/^[0-9]{1,}$/", $contactFields[fax])) {
                $form->setError($field, "Fax not numeric");
            } else if ((strlen($contactFields[fax]) > 10)) {
                $form->setError($field, "Fax above 10 numbers");
            } else if ((strlen($contactFields[fax]) < 10)) {
                $form->setError($field, "Fax below 10 numbers");
            }
        }

        /* Email error checking */
        $field = "contact_email";  //Use field name for email
        if (!$contactFields[email] || strlen($contactFields[email] = trim($contactFields[email])) == 0) {
            $form->setError($field, "Email not entered");
        } else {
            /* Check if valid email address */
            $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                    . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                    . "\.([a-z]{2,}){1}$";
            if (!eregi($regex, $contactFields[email])) {
                $form->setError($field, "Email invalid");
            }
            $contactFields[email] = stripslashes($contactFields[email]);
        }
        
        $field = "contact_facebook";  //Use field name for facebook
        /* Spruce up facebook, check length */
        $contactFields[facebook] = stripslashes($contactFields[facebook]);
        if (!empty($contactFields[facebook])) {
            if (!preg_match($this->text_pattern, $contactFields[facebook])) {
                $form->setError($field, "Facebook not alphanumeric");
            }
        }
        $field = "contact_twitter";  //Use field name for twitter
        /* Spruce up twitter, check length */
        $contactFields[twitter] = stripslashes($contactFields[twitter]);
        if (!empty($contactFields[twitter])) {
            if (!preg_match($this->text_pattern, $contactFields[twitter])) {
                $form->setError($field, "Twitter not alphanumeric");
            }
        }
        $field = "contact_skype";  //Use field name for skype
        /* Spruce up skype, check length */
        $contactFields[skype] = stripslashes($contactFields[skype]);
        if (!empty($contactFields[skype])) {
            if (!preg_match($this->text_pattern, $contactFields[skype])) {
                $form->setError($field, "Skype not alphanumeric");
            }
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $form->setError("vis_add_contact", "none");
            $_SESSION['visible_project' . $contactFields[proj]] = true;
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ 
        else {
            $addInfo = array('Telephone' => $contactFields[telephone], 'Mobile' => $contactFields[mobile], 'Fax' => $contactFields[fax], 'Email' => $contactFields[email], 'Facebook' => $contactFields[facebook], 'Twitter' => $contactFields[twitter], 'Skype' => $contactFields[skype]);
            $addContactInfoEncode = json_encode($addInfo);
            if ($database->addNewContact($contactFields, $addContactInfoEncode)) {
                $_SESSION['visible_project' . $contactFields[proj]] = true;
                return 0;  //New user added succesfully
            } else {
                $form->setError("vis_add_contact", "none");
                $_SESSION['visible_project' . $contactFields[proj]] = true;
                return 2;  //Registration attempt failed
            }
        }
    }
    
     function appoinment_register($appoinmentFields) {
        global $database, $form, $mailer, $security;  //The database, form and mailer object

        /* Position error checking */
        $field = "appoinmentDate";  //Use field name for appoinment
        if (!$security->valid_EUDate($appoinmentFields[date])){
            $form->setError($field, "Not a valid date");
        }else if ($security->greaterDate($appoinmentFields[date],date('d/m/y'))){
            $form->setError($field, "This is an earlier date");
        }else {
            $date = explode("/", $appoinmentFields[date]);
            $datetime[0] = $date[2]."-".$date[1]."-".$date[0];
            $datetime[1] = $appoinmentFields[time];
        }
        /* User error checking */
        $field = "user";  //User field name
        if (!$appoinmentFields[user] || strlen($appoinmentFields[user] = trim($appoinmentFields[user])) == 0) {
            $form->setError($field, "* User not entered");
        }
        
        $field = "description";  //Description field name
        /* Spruce up description, check length */
        $appoinmentFields[description] = stripslashes($appoinmentFields[description]);
        if (!empty($appoinmentFields[description])) {
            if (!preg_match($this->text_pattern, $appoinmentFields[description])) {
                $form->setError($field, "Description not alphanumeric");
            }
        }
        
        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $form->setError("vis_add_appoinment", "none");
            $_SESSION['visible_project' . $appoinmentFields[proj]] = true;
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ 
        else {
            if ($database->addNewAppoinment($appoinmentFields, $datetime)) {
                $_SESSION['visible_project' . $appoinmentFields[proj]] = true;
                return 0;  //New user added succesfully
            } else {
                $_SESSION['visible_project' . $appoinmentFields[proj]] = true;
                $form->setError("vis_add_appoinment", "none");
                return 2;  //Registration attempt failed
            }
        }
    }
    
    
    function reportActivity_register($reportActivityFields) {
        global $database, $form, $mailer;  //The database, form and mailer object

        /* Position error checking */
        
        $field = "activity";  //Description field name
        /* Spruce up description, check length */
        $reportActivityFields[activity] = stripslashes($reportActivityFields[activity]);
        if (!empty($reportActivityFields[activity])) {
            if (!preg_match($this->text_pattern, $reportActivityFields[activity])) {
                $form->setError($field, "Activity not alphanumeric");
            }
        }
        
        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $_SESSION['visible_project' . $reportActivityFields[project]] = true;
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ 
        else {
            if ($database->addNewReportActivity($reportActivityFields)) {
                $_SESSION['visible_project' . $reportActivityFields[project]] = true;
                return 0;  //New user added succesfully
            } else {
                $_SESSION['visible_project' . $reportActivityFields[project]] = true;
                return 2;  //Registration attempt failed
            }
        }
    }
    
    function activitiesOfProject($project) {
        global $database;
        return $database->getReportActivityProject($project);
    }
    
    
    function client_edit($value, $field, $id) {
        global $database, $form, $mailer;
        $value = stripslashes($value);
        if (($field == "fName") or ($field == "lName")) {
            if (!$value) {
                //Not Entered
                return 10;
            } else if (strlen($value) < 3) {
                return 11; // "Below 3 characters");
            } else if (strlen($value) > 30) {
                return 12; // "Above 30 characters");
            }/* Check if is not alphanumeric */ else if (!preg_match($this->text_pattern, $value)) {
                return 13; // "Not alphanumeric");
            }
        } else if (($field == "Company_Name") or ($field == "Company_Type") or ($field == "Address") or ($field == "Town") or ($field == "Country")) {
            if (!$value=="") {
                if (!preg_match($this->text_pattern, $value)) {
                    return 13; // "Not alphanumeric");
                }
            }
        } else if ($field == "VAT_No") {
            if (!$value=="") {
                if (!preg_match("/^[0-9]{1,}$/", $value)) {
                    return 14; // "Not alphanumeric");
                } else if (strlen($value) < 9) {
                    return 21; // "Below 9 characters");
                } else if (strlen($value) > 9) {
                    return 22; // "Above 9 characters");
                }
            }
        } else if ($field == "ZIP") {
            if (!$value=="") {
                if (!preg_match("/^[0-9]{1,}$/", $value)) {
                    return 14; // "Not alphanumeric");
                } else if (strlen($value) < 5) {
                    return 15; // "Below 5 characters");
                } else if (strlen($value) > 5) {
                    return 16; // "Above 5 characters");
                }
            }
        } else if (($field == "Telephone") or ($field == "Mobile") or ($field == "Fax")) {
            if (!preg_match("/^[0-9]{1,}$/", $value)) {
                return 17; // "Not alphanumeric");
            } else if (strlen($value) < 10) {
                return 18; // "Below 5 characters");
            } else if (strlen($value) > 10) {
                return 19; // "Above 5 characters");
            }
        } else if ($field == "Email") {
            $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                    . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                    . "\.([a-z]{2,}){1}$";
            if (!$value) {
                //Not Entered
                return 10;
            } else if (!eregi($regex, $value)) {
                return 20;
            }
        }


        if ($database->editClient($value, $field, $id)) {
            return 0;  //New user added succesfully
        } else {
            return 2;  //Registration attempt failed
        }
    }
    
    function get_tiny_url($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    
    function secondsToTime($seconds){
        // extract hours
        $hours = floor($seconds / (60 * 60));

        // extract minutes
        $divisor_for_minutes = $seconds % (60 * 60);
        $minutes = floor($divisor_for_minutes / 60);

        // extract the remaining seconds
        $divisor_for_seconds = $divisor_for_minutes % 60;
        $seconds = ceil($divisor_for_seconds);

        // return the final array
        $obj = array(
            "h" => (int) $hours,
            "m" => (int) $minutes,
            "s" => (int) $seconds,
        );
        return $obj;
    }
    
    function dateChangeFormat($date){
        $date = explode("/", $date);
        $new_format = $date[2]."-".$date[1]."-".$date[0];
        return $new_format;
    }
}

/**
 * Initialize session object - This must be initialized before
 * the form object because the form uses session variables,
 * which cannot be accessed unless the session has started.
 */
$session = new Session;

/* Initialize form object */
$form = new Form;
?>
