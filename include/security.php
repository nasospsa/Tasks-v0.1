<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of security
 *
 * @author melenios_820
 */
class Security {
    //put your code here
    public function valid_EUDate($datestring){
        $date = explode("/", $datestring);
        return checkdate((int)$date[1], (int)$date[0], (int)$date[2]);
    }
    
    public function greaterDate($date_from,$date_to){
        $date1 = explode("/", $date_from);
        $date2 = explode("/", $date_to);
        
        if ($date1[2]<$date2[2]){
            return TRUE;
        }
        else if ($date1[2]>$date2[2]){
            return FALSE;
        }
        else{
            if ($date1[1]<$date2[1]){
                return TRUE;
            }
            else if ($date1[1]>$date2[1]){
                return FALSE;
            }
            else if ($date1[0]<=$date2[0]){
                return TRUE;
            }
            else{
                return FALSE;         
            }
        }
    }
    
    
}

$security = new Security;

?>
