<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of statistics
 *
 * @author melenios_820
 */
class Statistics {
    function TotalActivitiesPerUser(){
        global $database;
        
        $users = $database->activities_getNumNotificationsPerUser();
        $data ="";
        $idx=0;
        foreach ($users as $user) {
            if (empty ($user[username])) $user[username]="NULL";
            if ($idx!=0){
                $data[] = "['$user[username]', ".number_format($user[activities], 2)."]";
            }
            else
                $data[] = "{name:'$user[username]',y:".number_format($user[activities], 2).",sliced:true,selected:true}";
            $idx++;
        }
        $data = implode(",", $data);
        return $data;
    }
    
    
    
    function lastDaysTotalActivitiesPerUser($num_of_days){
        global $database;
        
        $users = $database->db_getUsers(SUPER_ADMIN_LEVEL, ADMIN_LEVEL, PROJECT_ADMIN_LEVEL, EMPLOYEE_LEVEL);
        
        $idx=0;
        foreach ($users as $user) {
            $userActivities[$user[username]] = $database->activities_getDailyActsOfUser($user[username],$num_of_days);
            
            foreach ($userActivities[$user[username]] as $value) {
                $data[$user[username]][] = $value[activities];
            }
            $data[$user[username]] = implode(",", $data[$user[username]]);
            if ($idx!=0){
                echo ",";
            }
            $idx++;
            
            echo "{name:'$user[username]',data:[".$data[$user[username]]."]}";
        }
        return TRUE;
    }
    
    function lastDaysString($num_of_days){
        $timestamp = time();
        for ($index = $num_of_days-1; $index >=-1; $index--) {
            $tm = 86400 * $index; // 60 * 60 * 24 = 86400 = 1 day in seconds //Get last 30 days
            $tm = $timestamp - $tm;
            $date[] = date("d/m", $tm);
        }
        return "'".implode("','", $date)."'";
    }
}

$statistics = new Statistics;

?>
