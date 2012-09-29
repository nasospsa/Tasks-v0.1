<?php
/**
 * Description of notifications
 *
 * @author melenios_820
 */
class Notifications {
    public $tester;
    function getUserNotifications($mode){
        global $session,$database;
        if ($mode == SERIALIZED){
            if ($session->isAdmin()){
                $notificationsArray = $database->activities_getAllNotifications();
            }
            else if ($session->isPA()){
                //get projects
                foreach ($session->avail_projects as $project) {
                    $projects[] = $project[title];
                }
                $notificationsArray = $database->activities_getPerProjectNotifications($projects);
            }
            else if ($session->isWokring()){
                $notificationsArray = $database->activities_getUserMadeNotifications($session->username);
            }
            $index=1;
            
            if (is_array($notificationsArray)){
            foreach ($notificationsArray as $notification) {
                //$notification[acting_user] = str_replace($session->username, "You", $notification[acting_user]);
                if ($index<9){//Last ..8
                    
                    switch ($notification[activity_type]) {
                        case New_Project:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> created project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "New_Project";
                            break;
                        case Edit_Project:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> edited project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "Edit_Project";
                            break;
                        case Del_Project:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> deleted project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "Del_Project";
                            break;
                        case Add_Project_Assignement:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> added <span class=\"notificationAffected\">'".$notification[affected_users]."'</span> to project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "Add_Project_Assignement";
                            break;
                        case Remove_Project_Assignement:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> removed <span class=\"notificationAffected\">'".$notification[affected_users]."'</span> from project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "Remove_Project_Assignement";
                            break;
                        case New_Task:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> created task <span class=\"notificationTask\">'".$notification[task]."'</span> on project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "New_Task";
                            break;
                        case Edit_Task:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> edited task <span class=\"notificationTask\">'".$notification[task]."'</span> on project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "Edit_Task";
                            break;
                        case Del_Task:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> deleted task <span class=\"notificationTask\">'".$notification[task]."'</span> on project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "Del_Task";
                            break;
                        case Add_Task_Assignement:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> added <span class=\"notificationAffected\">'".$notification[affected_users]."'</span> to task <span class=\"notificationTask\">'".$notification[task]."'</span> on project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "Add_Task_Assignement";
                            break;
                        case Remove_Task_Assignement:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> added <span class=\"notificationAffected\">'".$notification[affected_users]."'</span> to task <span class=\"notificationTask\">'".$notification[task]."'</span> on project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "Remove_Task_Assignement";
                            break;
                        case Comment_Task:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> commented on task <span class=\"notificationTask\">'".$notification[task]."'</span> on project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "Comment_Task";
                            break;
                        case Upgrade_Task:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> upgraded task <span class=\"notificationTask\">'".$notification[task]."'</span> on project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "Upgrade_Task";
                            break;
                        case Downgrade_Task:
                            $message = "<span class=\"notificationUser\">".$notification[acting_user]."</span> downgraded task <span class=\"notificationTask\">'".$notification[task]."'</span> on project <a class=\"notificationProject\" href=\"?p=".$notification[project]."\"><span class=\"notificationProject\">'".$notification[project]."'</span></a>";
                            $class = "Downgrade_Task";
                            break;
                        default:
                            $message = "";
                    }
                    if ($message!=""){
                        $index++;
                        $happened = strtotime($notification[happened]);
                        if (date('Ymd') == date('Ymd', $happened)){
                            $happened = date('\T\o\d\a\y  H:i, j/n/\'y',$happened);
                        } else if (date('Ymd', strtotime('yesterday')) == date('Ymd', $happened)){
                            $happened = date('\Y\e\s\t\e\r\d\a\y H:i, j/n/\'y',$happened);
                        } else{
                            $happened = date('l H:i, j/n/\'y',$happened);
                        }                        
                        $message .= "<br/><span class=\"notificationTime\">$happened</span>";
                        $return_rows[] = array("class"=>$class,"message"=>$message);
                    }
                }
                else break;
            }
            }
            
            return $return_rows;
        }
    }
    
    
}

$notifications = new Notifications;

?>
