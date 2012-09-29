<?

/**
 * Process.php
 * 
 * The Process class is meant to simplify the task of processing
 * user submitted forms, redirecting the user to the correct
 * pages if errors are found, or if form is successful, either
 * way. Also handles the logout procedure.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 19, 2004
 */
include("include/session.php");

class Process {
    /* Class constructor */

    function Process() {
        global $session;
        /* User submitted login form */
        if (isset($_POST['sublogin'])) {
            $this->procLogin();
        }
        /* User submitted registration form */ else if (isset($_POST['subjoin'])) {
            $this->procRegister();
        }
        /* User submitted forgot password form */ else if (isset($_POST['subforgot'])) {
            $this->procForgotPass();
        }
        /* User submitted edit account form */ else if (isset($_POST['subedit'])) {
            $this->procEditAccount();
        }/*User sumbitted login form at Client Administration*/
        else if (isset($_POST['client_sublogin'])) {
            $this->client_procLogin();
        }
        // User submitted add new Task form
        else if (isset($_POST['subnewtask'])) {
            $this->procNewTask();
        } else if (isset($_POST['subedittask'])) {
            $this->procEditTask();
        } else if (isset($_POST['subdeletetask'])) {
            $this->procDelTask();
        }

        //Update Task Status
        else if (isset($_POST['sub_upd_sts']) or isset($_POST['sub_down_sts'])) {
            $this->procUpdateTaskStatus();
        }
        //assign More Users to Task
        else if (isset($_POST['sub_addto_task'])) {
            $this->procAssignMore_Task();
        }
        //remove Task assignment
        else if (isset($_POST['sub_removefrom_task'])) {
            $this->procRemoveAssign_Task();
        }
        //User submitted add new project form
        else if (isset($_POST['subnewproject'])) {
            $this->procNewProject();
        }
        //User submitted edit project form
        else if (isset($_POST['subeditproject'])) {
            $this->procEditProject();
        }
        //Delete Project Form submitted
        else if (isset($_POST['subdeleteproject'])) {
            $this->procDeleteProject();
        }
        //Add User to Project
        else if (isset($_POST['sub_addto_project'])) {
            $this->procAddUserstoProject();
        } else if (isset($_POST['sub_removefrom_project'])) {
            $this->procRemoveUsersfromProject();
        }

        //Add New Comment
        else if (isset($_POST['subnewcomment'])) {
            $this->procAddComment();
        }
        
        //Add Custom Timer
        else if (isset ($_POST["subAddCustomTimer"])){
            $this->procAddCustomTimer();
        }

        //Project Properties
        else if (isset($_POST['subinfoproject'])) {
            $this->procProjectInfo();
        } else if (isset($_POST['test_ajax'])) {
            $this->procUpdateTaskStatusAJAX();
            //echo "asdf";
        } else if (isset($_POST['subSortedProjectList'])) {
            $this->procProjectListSorted();
        }
        
        //Timers
        else if (isset ($_POST['start_timing'])){
            $this->procStartTiming();
        }
        else if (isset ($_POST['stop_timing'])){
            $this->procStopTiming();
        }
        
        //TimeSheets
        else if (isset ($_POST['populate_task_select'])){
            $this->procPopulateTaskSelect();
        }
        else if (isset ($_POST['subTimesheet'])){
            $this->procReqTimesheet();
        }
        
        //Client Administration
        /*User submitted add new Client */
        else if (isset($_POST['client_subcreate'])) {
            $this->client_procCreate();
        }
        /*User Submit Client Billing*/
        else if (isset($_POST['billing_subcreate'])) {
            $this->billing_procCreate();
        }
         /*User Submit Client Payment*/
        else if (isset($_POST['payment_subcreate'])) {
            $this->payment_procCreate();
        }
        /*User Submit Client Invoice*/
        else if (isset($_POST['invoice_subcreate'])) {
            $this->invoice_procCreate();
        }
        /*User Submit Expense Form*/
        else if (isset($_POST['expence_subcreate'])) {
            $this->expense_procCreate();
        }
        /*User Submit Withdraw Form*/
        else if (isset($_POST['withdraw_subcreate'])) {
            $this->withdraw_procCreate();
        }
         /*User Submit Deposit Form*/
        else if (isset($_POST['deposit_subcreate'])) {
            $this->deposit_procCreate();
        } /*User Submit Additional Contact Form*/
        else if (isset($_POST['contact_subcreate'])) {
            $this->contact_procCreate();
        }
        else if (isset($_POST['appoinment_subcreate'])) {
            $this->appoinment_procCreate();
        }
        else if (isset($_POST['reportActivity_subcreate'])) {
            $this->reportActivity_procCreate();
        }
        else if ($_GET['action']=="ajaxrequest_clientEdit") {
            $this->client_procEdit();
        } 
        
       




        /**
         * The only other reason user should be directed here
         * is if he wants to logout, which means user is
         * logged in currently.
         */ else if ($session->logged_in) {
            $this->procLogout();
        }
        /**
         * Should not get here, which means user is viewing this page
         * by mistake and therefore is redirected.
         */ else {
            header("Location: main.php");
        }
    }

    /**
     * procLogin - Processes the user submitted login form, if errors
     * are found, the user is redirected to correct the information,
     * if not, the user is effectively logged in to the system.
     */
    function procLogin() {
        global $session, $form;
        /* Login attempt */
        $retval = $session->login($_POST['user'], $_POST['pass'], isset($_POST['remember']));
        /* Login successful */
        if ($retval) {
            header("Location: " . $session->referrer);
        }
        /* Login failed */ else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
    }

    function client_procLogin() {
        global $session, $form;
        /* Login attempt */
        $retval = $session->client_login($_POST['user'], $_POST['pass']);
        /* Login successful */
        if ($retval) {
            header("Location: " . $session->referrer);
        }
        /* Login failed */ else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
    }

    /**
     * procLogout - Simply attempts to log the user out of the system
     * given that there is no logout form to process.
     */
    function procLogout() {
        global $session;
        $retval = $session->logout();
        header("Location: main.php");
    }

    /**
     * procRegister - Processes the user submitted registration form,
     * if errors are found, the user is redirected to correct the
     * information, if not, the user is effectively registered with
     * the system and an email is (optionally) sent to the newly
     * created user.
     */
    function procRegister() {
        global $session, $form;
        /* Convert username to all lowercase (by option) */
        if (ALL_LOWERCASE) {
            $_POST['user'] = strtolower($_POST['user']);
        }
        /* Registration attempt */
        $retval = $session->register($_POST['user'], $_POST['pass'], $_POST['email']);

        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['reguname'] = $_POST['user'];
            $_SESSION['regsuccess'] = true;
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */ else if ($retval == 2) {
            $_SESSION['reguname'] = $_POST['user'];
            $_SESSION['regsuccess'] = false;
            header("Location: " . $session->referrer);
        }
    }

    /**
     * procForgotPass - Validates the given username then if
     * everything is fine, a new password is generated and
     * emailed to the address the user gave on sign up.
     */
    function procForgotPass() {
        global $database, $session, $mailer, $form;
        /* Username error checking */
        $subuser = $_POST['user'];
        $field = "user";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "Username not entered<br>");
        } else {
            /* Make sure username is in database */
            $subuser = stripslashes($subuser);
            if (strlen($subuser) < 5 || strlen($subuser) > 30 ||
                    !eregi("^([0-9a-z])+$", $subuser) ||
                    (!$database->usernameTaken($subuser))) {
                $form->setError($field, "Username does not exist<br>");
            }
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
        }
        /* Generate new password and email it to user */ else {
            /* Generate new password */
            $newpass = $session->generateRandStr(8);

            /* Get email of user */
            $usrinf = $database->getUserInfo($subuser);
            $email = $usrinf['email'];

            /* Attempt to send the email with new password */
            if ($mailer->sendNewPass($subuser, $email, $newpass)) {
                /* Email sent, update database */
                $database->updateUserField($subuser, "password", md5($newpass));
                $_SESSION['forgotpass'] = true;
            }
            /* Email failure, do not change password */ else {
                $_SESSION['forgotpass'] = false;
            }
        }

        header("Location: " . $session->referrer);
    }

    /**
     * procEditAccount - Attempts to edit the user's account
     * information, including the password, which must be verified
     * before a change is made.
     */
    function procEditAccount() {
        global $session, $form;
        /* Account edit attempt */
        if ($_POST['avatar_type'] == "predefined") {
            $avatar = $_POST['avatar'];
        } else {
            $avatar = $_POST['avatar_url'];
        }

        $retval = $session->editAccount($_POST['curpass'], $_POST['newpass'], $_POST['email'], $avatar);

        /* Account edit successful */
        if ($retval) {
            $_SESSION['useredit'] = true;
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
    }

    /* procNewTask - Attempts to add a new task to a specific project
      only after users who have been assigned to the task are eligible from the user */

    function procNewTask() {
        global $session, $form;
        //Attempt
        $retval = $session->newtask($_POST['task_title'], $_POST['task_desc'], $_POST['task_priority'], $_POST['task_assign'], $_POST['project']);

        if ($retval == 0) {
            $_SESSION['task_success' . $_POST['project']] = true;
            header("Location: " . $session->referrer);
        } else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['task_success' . $_POST['project']] = false;
            header("Location: " . $session->referrer);
        }
    }

    function procEditTask() {
        global $session, $form;

        $retval = $session->edittask($_POST['id'], $_POST['task_title'], $_POST['task_desc'], $_POST['project']);

        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "To task " . $_POST['task_title'] . " τροποποιήθηκε!";
            header("Location: task.php?t=" . $_POST['id']);
        } else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Υπήρξε σφάλμα!";
            header("Location: task.php?t=" . $_POST['id']);
        }
    }

    function procDelTask() {
        global $session, $form;

        $retval = $session->deletetask($_POST['id'], $_POST['task_title'], $_POST['project_title']);

        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "To task " . $_POST['task_title'] . " διαγράφηκε!";
            header("Location: main.php");
        } else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Υπήρξε σφάλμα!";
            header("Location: task.php?t=" . $_POST['id']);
        }
    }

    function procUpdateTaskStatus() {
        global $session, $form;

        if (isset($_POST['sub_upd_sts'])) {
            $retval = $session->updatetask($_POST['tsk']);
        } else if (isset($_POST['sub_down_sts'])) {
            $retval = $session->downgradetask($_POST['tsk']);
        }


        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "Το task άλλαξε status!";
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Συνέβη 1 λάθος με την αλλαγή status";
            header("Location: " . $session->referrer);
        } else {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Συνέβη κάποιο λάθος με την αλλαγή status";
            header("Location: " . $session->referrer);
        }
    }

    function procUpdateTaskStatusAJAX() {

        global $session, $form;

        //need ajax buttons returned
        //1 option, only UPGRADE
        //2 option, only DOWNGRADE
        //3 option, BOTH
        // need retval and status return

        if (isset($_POST['tsk_action'])) {
            //$retval = $session->updatetask($_POST['tsk']);
            $retval = $session->editTaskStatus_AJAX($_POST["tsk"], $_POST['tsk_action']);
            echo $retval;
        }
        else
            echo "fail";
    }

    function procAssignMore_Task() {
        global $session, $form;

        $users_array = array();
        foreach ($_POST as $key => $value) {
            if (preg_match("/^add[0-9]{1,2}$/", $key)) {
                $users_array[] = $value;
            }
        }

        $retval = $session->addUserstoTask($users_array, $_POST['task']);

        if ($retval == 0) {
            $_SESSION['success'] = true;
            $task_details = $session->avail_tasks_straight[$_POST['task']];
            $_SESSION['header'] = "To task " . $task_details[title] . " τροποποιήθηκε!";
            header("Location: task.php?t=" . $_POST['task']);
        } else if ($retval == 1) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = var_dump($form->getErrorArray());
            //$_SESSION['value_array'] = $_POST;
            //$_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Υπήρξε σφάλμα στην τροποποίηση του task!";
            header("Location: " . $session->referrer);
        }
    }

    function procRemoveAssign_Task() {
        global $session, $form;
        $users_array = array();
        foreach ($_POST as $key => $value) {
            if (preg_match("/^remove[0-9]{1,2}$/", $key)) {
                $users_array[] = $value;
            }
        }

        $retval = $session->removeUsersfromTask($users_array, $_POST['task']);

        if ($retval == 0) {
            $_SESSION['success'] = true;
            $task_details = $session->avail_tasks_straight[$_POST['task']];
            $_SESSION['header'] = "To task " . $task_details[title] . " τροποποιήθηκε!";
            header("Location: task.php?t=" . $_POST['task']);
        } else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Υπήρξε σφάλμα στην τροποποίηση του task!";
            header("Location: " . $session->referrer);
        }
    }

    function procNewProject() {
        global $session, $form;
        //Attempt
        $retval = $session->newproject($_POST['project_title'], $_POST['project_desc'], $_POST['project_url'], $_POST['project_domain_exp'], $_POST['project_host_exp']);
        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = $_POST['project_title'];
            header("Location: main.php");
        } else if ($retval == 1) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Error 1";
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Error 2";
            header("Location: " . $session->referrer);
        }
    }

    function procEditProject() {
        global $session, $form;
        //Attempt
        $retval = $session->editProject($_POST['project_title'], $_POST['project_desc'], $_POST['project_url'], $_POST['project_domain_exp'], $_POST['project_host_exp'], $_POST['old_title']);

        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "To project " . $_POST['project_title'] . " τροποποιήθηκε!";
            header("Location: main.php");
        } else if ($retval == 1) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Υπήρξε σφάλμα στα πεδία της φόρμας!";
            
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Υπήρξε σφάλμα στην τροποποίηση του project!";
            header("Location: " . $session->referrer);
        }
    }

    function procDeleteProject() {
        global $session;
        $retval = $session->delproject($_POST['project_title']);

        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "To project " . $_POST['project_title'] . " διαγράφηκε!";
            header("Location: main.php");
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Υπήρξε σφάλμα!";
            header("Location: editProject.php?p=" . $_POST['project_title']);
        }
    }

    function procAddUserstoProject() {
        global $session, $form;

        $users_array = array();

        foreach ($_POST as $key => $value) {
            if (preg_match("/^add[0-9]{1,2}$/", $key)) {
                $users_array[] = $value;
            }
        }

        $retval = $session->addUserstoProject($users_array, $_POST['project']);

        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "To project " . $_POST['project'] . " τροποποιήθηκε!";
            header("Location: main.php");
        } else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Υπήρξε σφάλμα στην τροποποίηση του project!";
            header("Location: " . $session->referrer);
        }
    }

    function procRemoveUsersfromProject() {
        global $session, $form;

        $users_array = array();
        foreach ($_POST as $key => $value) {
            if (preg_match("/^remove[0-9]{1,2}$/", $key)) {
                $users_array[] = $value;
            }
        }

        $retval = $session->removeUsersfromProject($users_array, $_POST['project']);

        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "To project " . $_POST['project'] . " τροποποιήθηκε!";
            header("Location: main.php");
        } else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Υπήρξε σφάλμα στην τροποποίηση του project!";
            header("Location: " . $session->referrer);
        }
    }

    function procAddComment() {
        global $session;
        $retval = $session->addComment($_POST['comment'], $_POST['task_id']);
        $retval = 0;
        if ($retval == 0) {

            $returned_html = "<li>
			<div class=\"profile\"><img src=\"" . $session->userinfo["avatar"] . "\" width=\"75\" /></div>
			<div class=\"comment_details\">
			<div>";
            $returned_html .=$_POST['comment'];
            $returned_html .="</div><div>";
            $returned_html .= $session->username . "";
            $returned_html .=" | a few moments ago</div></div></li>";

            echo $returned_html;
        } else {
            return $retval;
        }
    }
    function procAddCustomTimer(){
        global $session,$form;
        //elegxos gia to task, an exei prosvasei,
        //prepei na exei prosvasei sto task
        $retval = $session->addCustomTimer($_POST[tsk],$_POST[timerMins]);

        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "$_POST[timerMins] Minutes were added to the task";
            header("Location: " . $session->referrer);
        } else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "There was an error with adding Time to the task";
            header("Location: " . $session->referrer);
        }
    }

    function procProjectInfo() {
        global $session;
        $retval = $session->addProjectProperties($_POST);

        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "Τα credentials του project " . $_POST['project'] . " τροποποιήθηκαν!";
            header("Location: " . $session->referrer);
        } else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Υπήρξε σφάλμα στα credentials του project!";
            header("Location: " . $session->referrer);
        }
    }

    function procProjectListSorted() {
        global $session;
        $session->updateProjectSortedList($_POST['sortedList']);
    }
    
    
    function procStartTiming(){
        global $session;
        if ($session->startTaskTiming($_POST['task_id'])){
            echo "1";
        }
        else echo "0";
    }
    function procStopTiming(){
        global $session;
        echo $session->stopTaskTiming($_POST['task_id']);
    }
    
    function procPopulateTaskSelect(){
        global $session;
        $session->AJAX_PopulateTasks($_POST['project']);
    }
    
    function procReqTimesheet(){
        global $session,$form;
        $ret = $session->getTimesheet($_POST);
        if ($ret==1){
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Erros within the form!";
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
        } else if ($ret==3){
            $_SESSION['success'] = false;
            $_SESSION['header'] = "No Results!";
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
        } else{
            $_SESSION['content'] = $ret;
            $_SESSION['value_array'] = $_POST;
        }
        header("Location: " . $session->referrer);
    }
    
    /*Client Administration
     * 
     * 
     * 
     * Gan Web RULZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ
     * 
     * 
     *
     */


    /*Create New Client*/
    function client_procCreate() {
        global $session, $form;

        /* Registration attempt */
        $clientFields[firstname] = $_POST['firstname'];
        $clientFields[lastname] = $_POST['lastname'];
        $clientFields[proj] = $_POST['proj'];
        $clientFields[telephone] = $_POST['telephone'];
        $clientFields[mobile] = $_POST['mobile'];
        $clientFields[fax] = $_POST['fax'];
        $clientFields[email] = $_POST['email'];
        $clientFields[facebook] = $_POST['facebook'];
        $clientFields[twitter] = $_POST['twitter'];
        $clientFields[skype] = $_POST['skype'];
        $clientFields[companyName] = $_POST['companyName'];
        $clientFields[companyType] = $_POST['companyType'];
        $clientFields[taxOffice] = $_POST['taxOffice'];
        $clientFields[vatNumber] = $_POST['vatNumber'];
        $clientFields[address] = $_POST['address'];
        $clientFields[town] = $_POST['town'];
        $clientFields[zip] = $_POST['zip'];
        $clientFields[country] = $_POST['country'];
        
        $retval = $session->client_register($clientFields);

        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "Client Created Successfully!";
            header("Location: " . $session->referrer);
        }
        /* Error found with form */
        else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error!";
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */
        else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error at database!";
            header("Location: " . $session->referrer);
        }   
    }

    /*Create New Billing*/
    function billing_procCreate() {
        global $session, $form;

        /* Registration attempt */
        $billingFields[client] = $_POST['client'];
        $billingFields[proj] = $_POST['proj'];
        $billingFields[amount] = $_POST['amount'];
        $billingFields[description] = $_POST['description'];
        $billingFields[fileURL] = $_POST['fileURL'];
        
        $retval = $session->billing_create($billingFields);

        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "New Billing Created Successfully!";
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error!";
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */ else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error at database!";
            header("Location: " . $session->referrer);
        }
    }

    /*Create New Payment*/
    function payment_procCreate() {
        global $session, $form;
       
        /* Registration attempt */
        $paymentFields[client] = $_POST['clientPay'];
        $paymentFields[proj] = $_POST['proj'];
        $paymentFields[amount] = $_POST['amount'];
        $paymentFields[description] = $_POST['description'];
        
        /*If Invoice checked*/
        if (isset($_POST['payment_invoiceCreate'])){
           $paymentFields[invoiceAmount] = $_POST['invoiceAmount'];
           $paymentFields[vat] = $_POST['vat'];
           $paymentFields[fileURL] = $_POST['fileURL'];
        }

        $retval = $session->payment_create($paymentFields);

        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "Payment Created Successfully!";
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error!";
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */ else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error at database!";
            header("Location: " . $session->referrer);
        }
    }

    /*Create New Invoice*/
    function invoice_procCreate() {
        global $session, $form;
        
        /* Registration attempt */
        $invoiceFields[client] = $_POST['clientInvoice'];
        $invoiceFields[proj] = $_POST['proj'];
        $invoiceFields[amount] = $_POST['amount'];
        $invoiceFields[vat] = $_POST['vat'];
        $invoiceFields[fileURL] = $_POST['fileURL'];


        $retval = $session->invoice_create($invoiceFields);

        /* Registration Successful */
        if ($retval == 0) {

            $_SESSION['success'] = true;
            $_SESSION['header'] = "Invoice Created Successfully!";
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error!";
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */ else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error at database!";
            header("Location: " . $session->referrer);
        }
    }

    /*Create New Expense*/
    function expense_procCreate() {
        global $session, $form;
        
        /* Registration attempt */
        $expenseFields[operator] = $_POST['operator'];
        $expenseFields[amount] = $_POST['amount'];
        $expenseFields[vat] = $_POST['vat'];
        $expenseFields[fileURL] = $_POST['fileURL'];


        $retval = $session->expense_create($expenseFields);

        /* Registration Successful */
        if ($retval == 0) {

            $_SESSION['success'] = true;
            $_SESSION['header'] = "Expense Created Successfully!";
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error!";
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */ else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error at database!";
            header("Location: " . $session->referrer);
        }
    }

    /*Create New Withdraw*/
    function withdraw_procCreate() {
        global $session, $form;
        
        /* Registration attempt */
        $withdrawFields[user] = $_POST['user'];
        $withdrawFields[amount] = $_POST['amount'];
        $$withdrawFields[description] = $_POST['description'];

        $retval = $session->withdraw_create($withdrawFields);

        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "Withdraw Created Successfully!";
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error!";
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */ else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error at database!";
            header("Location: " . $session->referrer);
        }
    }
    /*Create New Withdraw*/
    function deposit_procCreate() {
        global $session, $form;
        
        /* Registration attempt */
        $depositFields[user] = $_POST['user'];
        $depositFields[amount] = $_POST['amount'];
        $depositFields[description] = $_POST['description'];

        $retval = $session->deposit_create($depositFields);

        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "Deposit Created Successfully!";
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error!";
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */ else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error at database!";
            header("Location: " . $session->referrer);
        }
    }
    
    function contact_procCreate() {
        global $session, $form;

        /* Registration attempt */
        $contactFields[firstname] = $_POST['firstname'];
        $contactFields[lastname] = $_POST['lastname'];
        $contactFields[proj] = $_POST['proj'];
        $contactFields[telephone] = $_POST['telephone'];
        $contactFields[mobile] = $_POST['mobile'];
        $contactFields[fax] = $_POST['fax'];
        $contactFields[email] = $_POST['email'];
        $contactFields[facebook] = $_POST['facebook'];
        $contactFields[twitter] = $_POST['twitter'];
        $contactFields[skype] = $_POST['skype'];
        $contactFields[position] = $_POST['position'];
        
        $retval = $session->contact_register($contactFields);

        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "Contact Created Successfully!";
            header("Location: " . $session->referrer);
        }
        /* Error found with form */
        else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error!";
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */
        else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error at database!";
            header("Location: " . $session->referrer);
        }   
        
    }
    
     function appoinment_procCreate() {
        global $session, $form;

        /* Registration attempt */
        $dateTimeArray = explode(" ", $_POST['dateTime']);
        $appoinmentFields[date] = $dateTimeArray[0];
        $appoinmentFields[time] = $dateTimeArray[1];
        $appoinmentFields[typeOf] = $_POST['typeOfUser'];
        If ($appoinmentFields[typeOf] == 4001){
            $appoinmentFields[contactUser] = $_POST['contactClientID'];
        }else{
            $appoinmentFields[contactUser] = $_POST['contactUserID'];
        }
        $appoinmentFields[user] = $_POST['user'];
        $appoinmentFields[description] = $_POST['description'];
        $appoinmentFields[proj] = $_POST['proj'];
        
        $retval = $session->appoinment_register($appoinmentFields);

        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['success'] = true;
            $_SESSION['header'] = "Appoinment Created Successfully!";
            header("Location: " . $session->referrer);
        }
        /* Error found with form */
        else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error!";
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */
        else if ($retval == 2) {
            $_SESSION['success'] = false;
            $_SESSION['header'] = "Sorry, There was an error at database!";
            header("Location: " . $session->referrer);
        }   
        
    }
    
    function reportActivity_procCreate() {
        global $session, $form;

        /* Registration attempt */
        $reportActivityFields[user] = $session->userinfo[username];
        $reportActivityFields[activity] = $_POST['activity'];
        $reportActivityFields[project] = $_POST['project_id'];
        $reportActivityFields[typeOfActivity] = $_POST['typeOf'];
        if ($reportActivityFields[typeOfActivity] == "5001"){
            $activityType = "Incoming Call";
        }else if ($reportActivityFields[typeOfActivity] == "5002"){
            $activityType = "Outcoming Call";
        }else if ($reportActivityFields[typeOfActivity] == "5003"){
            $activityType = "Incoming Email";
        }else{
            $activityType = "Outcoming Email";
        }
        
        $retval = $session->reportActivity_register($reportActivityFields);
        
        if ($retval==0){
            $returnedHtml = "<li class=\"reportActivity_box\">
                            <div class=\"reportActivity_profile\">
                            <img src=\"".$session->userinfo[avatar]."\" class=\"reportActivity_img\"/>
                            </div>
                            <div class=\"activity_details\">
                                <h3>$activityType</h3>
                                <div>$reportActivityFields[activity]</div>
                                <div>$reportActivityFields[user]|a few seconds ago</div>
                            </div>
                            <div class=\"clr\"></div>
                            </li>";
            echo $returnedHtml;
        }
       
        
    }
    
    function client_procEdit() {
        global $session, $form;

        /* Edit attempt */
        $id = $_POST["elementid"];
        $value = $_POST["newvalue"];
        $id = explode("@", $id);

        $field = $id[0];
        $id = $id[1];
        
        $retval = $session->client_edit($value,$field,$id);

        
        if ($retval==0){
            echo $value;
        }else if ($retval==10){
            echo "Error! Value Not Entered";
        }else if ($retval==11){
            echo "Error! Below 3 characters";
        }else if ($retval==12){
            echo "Error! Above 30 characters";
        }else if ($retval==13){
            echo "Error! Not alphanumeric";
        }else if ($retval==14){
            echo "Error! ZIP not numeric";
        }else if ($retval==15){
            echo "Error! ZIP Below 5 characters";
        }else if ($retval==16){
            echo "Error! ZIP Above 5 characters";
        }else if ($retval==17){
            echo "Error! Telephone not numeric";
        }else if ($retval==18){
            echo "Error! Telephone Below 10 characters";
        }else if ($retval==19){
            echo "Error! Telephone Above 10 characters";
        }else if ($retval==20){
            echo "Error! Not valid Email";
        }else {
            echo "Unknown Error!";
        }
    }
    
}


/* Initialize process */
$process = new Process;
?>
