<?

/**

 * Mailer.php

 *

 * The Mailer class is meant to simplify the task of sending

 * emails to users. Note: this email system will not work

 * if your server is not setup to send mail.

 *

 * If you are running Windows and want a mail server, check

 * out this website to see a list of freeware programs:

 * <http://www.snapfiles.com/freeware/server/fwmailserver.html>

 *

 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)

 * Last Updated: August 19, 2004

 */
class Mailer {

    /**

     * sendWelcome - Sends a welcome message to the newly

     * registered user, also supplying the username and

     * password.

     */
    var $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";

//$headers .= "Content-type: text/html; charset=utf-8\r\n";
//$headers .= 'From: Webmaster <members@kapou.com>' . "\r\n"; 





    function sendWelcome($user, $email, $pass) {



        $from = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM_ADDR . ">";

        $subject = "Gan Tasks - Welcome!";

        $body = $user . ",\n\n"
                . "Welcome! You've just registered at <b>Gan Tasks Management Software</b>"
                . "with the following information:\n\n"
                . "Username: " . $user . "\n"
                . "Password: " . $pass . "\n\n"
                . "If you ever lose or forget your password, a new "
                . "password will be generated for you and sent to this "
                . "email address, if you would like to change your "
                . "email address you can do so by going to the "
                . "My Account page after signing in.\n\n"
                . "- <a href=\"http:\\tasks.gan-web.gr/\"";



        return mail($email, $subject, $body, $from);
    }

    /**

     * sendNewPass - Sends the newly generated password

     * to the user's email address that was specified at

     * sign-up.

     */
    function sendNewPass($user, $email, $pass) {

        $from = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM_ADDR . ">";

        $subject = "Gan Tasks - Your new password";

        $body = $user . ",\n\n"
                . "We've generated a new password for you at your "
                . "request, you can use this new password with your "
                . "username to log in to <b>Gan Tasks</b> Site.\n\n"
                . "Username: " . $user . "\n"
                . "New Password: " . $pass . "\n\n"
                . "It is recommended that you change your password "
                . "to something that is easier to remember, which "
                . "can be done by going to the My Account page "
                . "after signing in.\n\n"
                . "- Gan Tasks";



        return mail($email, $subject, $body, $from);
    }

    function notifyAdmins($subject, $message) {

        global $database;

        $mail_confirm = true;

        $admins = $database->db_getUsers(SUPER_ADMIN_LEVEL, ADMIN_LEVEL);

        foreach ($admins as $admin) {

            $from = $this->headers . "From: Tasks Management Gan-Web <noreply@gan-web.gr>";



            $mail_confirm = $mail_confirm & mail($admin[email], '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $from);
        }

        return $mail_confirm;
    }

    function notifyProjectUsers($subject, $message, $project) {
        global $database;
        $mail_confirm = true;
        //$users = $database->getWorking_on_Project_m($project);
        $users = $database->db_getProjectUsers($project, PROJECT_ADMIN_LEVEL, EMPLOYEE_LEVEL);

        foreach ($users as $user) {
            $from = $this->headers . "From: Tasks Management Gan-Web <noreply@gan-web.gr>";
            $mail_confirm = $mail_confirm & mail($user[email], '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $from);
        }

        return $mail_confirm;
    }

    function newProjectNotification($proj, $user) {
        $message = "Δημιουργήθηκε νέο Project με όνομα:<br/>"
                . $proj . "<br/><br/>"
                . "απο τον χρήστη " . $user . "<br/><br/><br/><br/>"
                . "Αυτόματο Μήνυμα";
        $subject = "Νέο Project";

        return $this->notifyAdmins($subject, $message);
    }

    function editProjectNotification($proj, $user) {

        $message = "Τροποποιήθηκε το Project με όνομα:<br/>"
                . $proj . "<br/><br/>"
                . "απο τον χρήστη " . $user . "<br/><br/><br/><br/>"
                . "Αυτόματο Μήνυμα";

        $subject = "Τροποποίηση Project";



        $this->notifyAdmins($subject, $message);

        $this->notifyProjectUsers($subject, $message, $proj);

        return true;
    }

    function delProjectNotification($proj, $user) {

        $message = "Διαγράφηκε το Project με όνομα:<br />"
                . $proj . "<br /><br />"
                . "απο τον χρήστη " . $user . "<br /><br /><br /><br />"
                . "Αυτόματο Μήνυμα";

        $subject = "Διαγραφή Project";

        $this->notifyAdmins($subject, $message);

        return true;
    }

    function newTaskNotification($task, $proj, $user, $assigned) {
        $message = "Δημιουργήθηκε νέο Task με όνομα :<br/>"
                . "<span style=\"font-size:15px;\"><strong>" . $task . "</strong></span><br />"
                . "για το Project <i>" . $proj . "</i><br/><br /><br/>"
                . "Από τον χρήστη <strong>" . $user . "</strong><br /><br />"
                . "και Ανατέθηκε στους <strong>" . implode(",", $assigned) . "</strong>.<br /><br /><br />"
                . "Αυτόματο Μήνυμα";
        $subject = "Νέο Task στο Project " . $proj;

        $this->notifyAdmins($subject, $message);

        return $this->notifyProjectUsers($subject, $message, $proj);
    }

    function editTaskNotification($task, $project, $user) {
        $message = "Τροποποιήθηκε το Task <span style=\"font-size:15px;\"><strong>" . $task . "</strong></span><br/><br /><br/>"
                . "Από το Project <i>" . $project . "</i><br/><br/><br/>"
                . "Από τον χρήστη <strong>" . $user . "</strong><br /><br /><br />"
                . "Αυτόματο Μήνυμα";
        $subject = "Τροποιποίηση Task στο Project " . $proj;

        $this->notifyAdmins($subject, $message);

        return $this->notifyProjectUsers($subject, $message, $proj);
    }

    function deleteTaskNotification($task, $project, $user) {
        $message = "Διαγράφηκε το Task <span style=\"font-size:15px;\"><strong>" . $task . "</strong></span><br/><br /><br/>"
                . "Από το Project <i>" . $project . "</i><br/><br/><br/>"
                . "Από τον χρήστη " . $user . "<br /><br /><br />"
                . "Αυτόματο Μήνυμα";
        $subject = "Διαγραφή Task";

        $this->notifyAdmins($subject, $message);

        return $this->notifyProjectUsers($subject, $message, $proj);
    }

}

;



/* Initialize mailer object */

$mailer = new Mailer;
?>