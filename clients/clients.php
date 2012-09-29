<?php
include("../include/session.php");
if (!$session->logged_in) {
    header("Location: login.php");
}
if (!$session->isPA()) {
    header("Location: ../main.php");
}
if ($_GET["v"] == "All") {
    //$projects = $session->avail_projects;
    //$clients = $session->all_Clients();
    $pageTitle = "Clients";
    $menuCurrent["Clients"] = "current";
} else if ($_GET["v"] == "Client") {
    $selectedClientId = $_GET["cid"];
    $projects = $session->avail_projects;
    $ClientArray = $session->getClientArray($selectedClientId);
    $clientActivitiesbyProject = $session->getClientActivity($selectedClientId);
    $pageTitle = $ClientArray[lName] . " " . $ClientArray[fName];
    $menuCurrent["Clients"] = "current";
    $oneClient = true;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo $pageTitle . " | " . $session->web_name ?></title>
        <link rel="stylesheet" type="text/css" href="css/960.css" />
        <link rel="stylesheet" type="text/css" href="css/purple.css" />
        <link rel="stylesheet" type="text/css" href="css/fileuploader.css" />
        <link rel="stylesheet" type="text/css" href="../styling/jquery.autocomplete.css" />
        <link type="text/css" href="css/smoothness/ui.css" rel="stylesheet" />
        <style type="text/css" title="currentStyle">
            @import "../js/dataTables/css/demo_page.css";
            @import "../js/dataTables/css/demo_table.css";
        </style>
        
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
        <!--<script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js"></script>-->
        <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
        <script type="text/javascript" src="../js/dataTables/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="js/jeditable.js"></script>
        <script type="text/javascript" src="js/document.js"></script>
        <script type="text/javascript" src="js/blend/jquery.blend.js"></script>
        <script type="text/javascript" src="js/ui.sortable.js"></script>
        <script type="text/javascript" src="js/ui.dialog.js"></script>
        <script type="text/javascript" src="js/effects.js"></script>
        <script type="text/javascript" src="js/flot/jquery.flot.pack.js"></script>
        <script type="text/javascript" src="../js/jquery.autocomplete.js"></script>
        <script type="text/javascript" src="js/fileuploader.js"></script>
        <!--[if IE]>
        <script language="javascript" type="text/javascript" src="js/flot/excanvas.pack.js"></script>
        <![endif]-->
        <!--[if IE 6]>
        <link rel="stylesheet" type="text/css" href="css/iefix.css" />
        <script src="js/pngfix.js"></script>
    <script>
        DD_belatedPNG.fix('#menu ul li a span span');
    </script>
    <![endif]-->
        <script id="source" language="javascript" type="text/javascript" src="js/graphs.js"></script>

    </head>

    <body>
        <!-- WRAPPER START -->
        <div class="container_16" id="wrapper">

            <!-- Include logo, top menu and quick tabs menu -->
            <? include("menu.php"); ?>
            <!--End of logo, top menu and quick tabs menu -->

            <!-- CONTENT START -->
            <div class="grid_16" id="content">
                <!--  TITLE START  -->
                <div class="grid_9">
                    <? if (!$oneClient) { ?>
                        <h1 class="clients">Clients</h1>
                    <? } else { ?>
                        <h1 class="clients"><? echo $ClientArray[lName] . " " . $ClientArray[fName] ?></h1>
                    <? } ?>
                </div>
                <!--RIGHT TEXT/CALENDAR-->
                <div class="grid_6" id="eventbox"><span><? echo $database->getCashierAmount(); ?></span>
                    <div class="hidden_calendar"></div>
                </div>
                <!--RIGHT TEXT/CALENDAR END-->
                <div class="clear">
                </div>
                <!--  TITLE END  -->
                <!-- #PORTLETS START -->
                <div id="portlets">
                    <!-- FIRST SORTABLE COLUMN START -->
                    <? if ($oneClient) { ?>
                        <div class="content-box client">
                            <div class="content-box-header"><h2>Client Overview</h2><a style="" class="showProject_btn" href="#!"></a></div>
                            <div class="content-box-content">
                                <p class="info" id="info"><span class="info_inner">Double Click Values to Edit them!</span></p>
                                <div class="clientDetails">
                                    <ul>
                                        <li><h3>Personal Details</h3></li>
                                        <li><span class="label">First Name:</span><span class="edit" id="fName@<? echo $ClientArray[id]; ?>"><? echo $ClientArray[fName]; ?></span></li>
                                        <li><span class="label">Last Name:</span><span class="edit" id="lName@<? echo $ClientArray[id]; ?>"><? echo $ClientArray[lName]; ?></span></li>
                                        <li><span class="label">Project:</span>
                                            <ul>
                                                <? foreach (json_decode($ClientArray[prjs]) as $clientPrj) { ?>
                                                    <li><a href="http://tasks.gan-web.gr/main.php?v=&p=<? echo $clientPrj->title; ?>" target="_blank"><? echo $clientPrj->title; ?></a></li>
                                                <? } ?>
                                            </ul>
                                        </li>
                                        <li><h3>Contact Information</h3></li>

                                        <li><span class="label">Telephone:</span><span class="edit"><? echo $ClientArray[telephone]; ?></span></li>
                                        <li><span class="label">Mobile:</span><span class="edit"><? echo $ClientArray[mobile]; ?></span></li>
                                        <li><span class="label">Fax:</span><span class="edit"><? echo $ClientArray[fax]; ?></span></li>
                                        <li><span class="label">Email:</span><span class="edit"><? echo $ClientArray[email]; ?></span></li>
                                    </ul>
                                </div>
                                <div class="clientInfo">
                                    <ul>
                                        <li><h3>Billing Information</h3></li>
                                        <li><span class="label">Company Name:</span><span class="edit" id="Company_Name@<? echo $ClientArray[id]; ?>"><? echo $ClientArray[Company_Name]; ?></span></li>
                                        <li><span class="label">Company Type:</span><span class="edit" id="Company_Type@<? echo $ClientArray[id]; ?>"><? echo $ClientArray[Company_Type]; ?></span></li>
                                        <li><span class="label">Tax Office:</span><span class="edit" id="TAX_Office@<? echo $ClientArray[id]; ?>"><? echo $ClientArray[TAX_Office]; ?></span></li>
                                        <li><span class="label">VAT Number:</span><span class="edit" id="VAT_No@<? echo $ClientArray[id]; ?>"><? echo $ClientArray[VAT_No]; ?></span></li>
                                        <li><span class="label">Address:</span><span class="edit" id="AddressAddress@<? echo $ClientArray[id]; ?>"><? echo $ClientArray[Address]; ?></span></li>
                                        <li><span class="label">Town:</span><span class="edit" id="Town@<? echo $ClientArray[id]; ?>"><? echo $ClientArray[Town]; ?></span></li>
                                        <li><span class="label">ZIP:</span><span class="edit" id="ZIP@<? echo $ClientArray[id]; ?>"><? echo $ClientArray[ZIP]; ?></span></li>
                                        <li><span class="label">Country:</span><span class="edit" id="Country@<? echo $ClientArray[id]; ?>"><? echo $ClientArray[Country]; ?></span></li>
                                    </ul>
                                </div>
                                <div class="clientSocial">
                                    <ul>
                                        <li><h3>Social Information</h3></li>
                                        <li><span class="label">Facebook:</span><span class="edit"><? echo $ClientArray[Social][Facebook]; ?></span></li>
                                        <li><span class="label">Twitter:</span><span class="edit"><? echo $ClientArray[Social][Twitter]; ?></span></li>
                                        <li><span class="label">Skype:</span><span class="edit"><? echo $ClientArray[Social][Skype]; ?></span></li>
                                        <li><h3>Client Since: <? echo $ClientArray[created]; ?></h3></li>
                                    </ul>
                                </div>
                                <div class="clr"></div>
                            </div>
                        </div>
                    <? } ?>
                    <? if ($oneClient) {
                        foreach ($clientActivitiesbyProject as $project => $ClientProjectActivities) { ?>
                            <? if ($project != "sum_amount") { ?>
                                <div class="content-box">
                                    <div class="content-box-header">
                                        <h2>Client Project <a href="http://tasks.gan-web.gr/main.php?v=&p=<? echo $project; ?>" target="_blank"><? echo $project; ?></a></h2>
                                        <a style="" class="showProject_btn" href="#!"></a>
                                        <? if ($ClientProjectActivities[sum_amount] < 0) { ?>
                                            <span class="span_Ammount remainng">Remaining amount of <? echo -$ClientProjectActivities[sum_amount] ?> &euro;</span>
                                        <? } else if ($ClientProjectActivities[sum_amount] > 0) { ?>
                                            <span class="span_Ammount owe">You Owe <? echo $ClientProjectActivities[sum_amount] ?> &euro;</span>
                                        <? } else { ?>
                                            <span class="span_Ammount payed">Payed up</span>
                                        <? } ?>
                                    </div>
                                    <div class="content-box-content project" style="display:<? echo $_SESSION['visible_project' . $project] ? "block" : "none" ?>">
                                        <? unset($_SESSION['visible_project' . $project]); ?>
                                        <div class="content-box-header nested"><h2>Client Payment Activity</h2></div>
                                        <div class="content-box-content nested">



                                            <table class="clientActivityTBL">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Files</th>
                                                        <th>Activity</th>
                                                        <th>Description</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <? foreach ($ClientProjectActivities as $key => $activity) { ?>
                                                        <? $total_amount += $activity[amount]; ?>
                                                        <? if ($activity[type] == 3001) { ?>
                                                            <tr>
                                                                <td><? echo $activity[happened]; ?></td>
                                                                <? if (!empty($activity[filesURL])) {
                                                                    $file_url = $session->get_tiny_url('http://tasks.gan-web.gr/clients/financialOffers/pratto.pdf'); ?>
                                                                    <td><a href="<? echo $file_url; ?>"  target="_blank" ><img src="images/icons/pdf_20x20.png"/></a></td>
                                                                <? } else { ?>
                                                                    <td>No Files!</td>   
                                                                <? } ?>
                                                                <td>Billing</td>
                                                                <td><? echo $activity[desc]; ?></td>
                                                                <td><? echo $activity[amount]; ?>&euro;</td>

                                                            </tr>
                                                        <? } else if ($activity[type] == 3002) { ?>
                                                            <tr>
                                                                <td><? echo $activity[happened]; ?></td>
                                                                <? if (!empty($activity[filesURL])) {
                                                                    $file_url = $session->get_tiny_url('http://tasks.gan-web.gr/clients/financialOffers/pratto.pdf'); ?>
                                                                    <td><a href="<? echo $file_url; ?>"  target="_blank" ><img src="images/icons/pdf_20x20.png"/></a></td>
                                                                <? } else { ?>
                                                                    <td>No Files!</td>   
                                                                <? } ?>
                                                                <td>Payment</td>
                                                                <td><? echo $activity[desc]; ?></td>
                                                                <td><? echo $activity[amount]; ?>&euro;</td>

                                                            </tr>
                                                        <? } else if ($activity[type] == 3003) { ?>
                                                            <tr>
                                                                <td><? echo $activity[happened]; ?></td>
                                                                <? if (!empty($activity[filesURL])) {
                                                                    $file_url = $session->get_tiny_url('http://tasks.gan-web.gr/clients/financialOffers/pratto.pdf'); ?>
                                                                    <td><a href="<? echo $file_url; ?>"  target="_blank" ><img src="images/icons/pdf_20x20.png"/></a></td>
                                                                <? } else { ?>
                                                                    <td>No Files!</td>   
                                                                <? } ?>
                                                                <td>Invoice</td>
                                                                <td><? echo $activity[desc]; ?></td>
                                                                <td><? echo $activity[amount]; ?>&euro;</td>
                                                            </tr>
                                                        <? } ?>
                                                    <? } ?>
                                                </tbody>
                                            </table>
                                        </div>    

                                        <div class="clr"></div>
                                        <div class="content-box-header nested"><h2>Additional Contacts</h2></div>
                                        <div class="content-box-content nestedContent">
                                            <? if ($ClientContactsPerProjectArray = $session->getClientContactsPerProject($project)){
                                                $projectHasContacts = true;
                                                ?>
                                            <div> 
                                                <table id="ClientProjectContactsTBL">
                                                    <thead>
                                                        <tr>
                                                            <th>Position</th>
                                                            <th>Contact Name</th>
                                                            <th>Telephone</th>
                                                            <th>Mobile</th>
                                                            <th>Fax</th>
                                                            <th>Email</th>
                                                            <th>Social</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <? foreach ($ClientContactsPerProjectArray as $contact) { ?>
                                                        <tr>
                                                            <td><? echo $contact[position]; ?></td>
                                                            <td><? echo $contact[fName].' '. $contact[lName] ; ?></td>
                                                            <td><span class="editContact" id="Telephone@<? echo $contact[id]; ?>"><? echo $contact[telephone]; ?></span></td>
                                                            <td><span class="editContact" id="Mobile<? echo $contact[id]; ?>"><? echo $contact[mobile]; ?></span></td>
                                                            <td><span class="editContact" id="Fax@<? echo $contact[id]; ?>"><? echo $contact[fax]; ?></span></td>                                                        
                                                            <td><span class="editContact" id="Email@<? echo $contact[id]; ?>"><? echo $contact[email]; ?></span></td>
                                                            <td>
                                                                <? if (!empty($contact[Social][Facebook])) { ?>
                                                                    <a href="http://www.facebook.com/<? echo $contact[Social][Facebook]; ?>" target="_blank"><img src="images/icons/FaceBook_20x20.png"/></a>
                                                                <? } ?>
                                                                <? if (!empty($contact[Social][Twitter])) { ?>
                                                                    <a href="http://www.twitter.com/<? echo $contact[Social][Twitter]; ?>" target="_blank"><img src="images/icons/Twitter_20x20.png"/></a>
                                                                <? } ?>
                                                                <? if (!empty($contact[Social][Skype])) { ?>
                                                                    <a href="skype:<? echo $contact[Social][Skype]; ?>?call" target="_blank"><img src="images/icons/Skype_20x20.png"/></a>
                                                                <? } ?>
                                                            </td>
                                                        </tr>
                                                     <? } ?>
                                                     </tbody>
                                                  </table>
                                            </div>
                                            <div class="clr"></div>
                                            <? } else { ?>
                                            <div class="span_warning">
                                                <span >Client <u><? echo $ClientArray[lName] . " " . $ClientArray[fName]  ?></u> has no Additional Contacts for Project <u><? echo $project ?></u>!!</span>
                                            </div>
                                            <div class="clr"></div>
                                            <? } ?>
                                            <input type="button" style="float:left;" class="addContact_button" value="Add Contact"/>
                                            <div class="clr"></div>
                                            <div class="addContact" style="display:<?
                                                if ($form->error("vis_add_contact") != '') {
                                                    echo "block";
                                                    $err = true;
                                                } else {
                                                    echo "none";
                                                    $err = false;
                                                }
                                                ?>">
                                                 <form name="addContact_form" action="../process.php" method="POST">
                                                    <div class="contactCreate">
                                                        <div class="contactDetails">
                                                            <ul>
                                                                <li><h3>Personal Details</h3></li>
                                                                <li><strong>Position:</strong> <input type="text" name="position" class="inputText" value="<? echo $form->value("position") ?>" /><? echo $form->error("position"); ?></li>
                                                                <li><strong>First Name:</strong> <input type="text" name="firstname" class="inputText" value="<? echo $form->value("firstname") ?>" /><? echo $form->error("contact_fname"); ?></li>
                                                                <li><strong>Last Name:</strong> <input type="text" name="lastname" class="inputText" value="<? echo $form->value("lastname") ?>"/><? echo $form->error("contact_lname"); ?></li>
                                                                <li><strong>Project:</strong> <input type="text" class="inputText" disabled="disabled" value="<? echo $project; ?>"/><input type="hidden" name="proj" class="inputText" value="<? echo $project; ?>" /> </li>
                                                            </ul>
                                                        </div>
                                                        <div class="contactInfo">
                                                            <ul>
                                                                <li><h3>Contact Information</h3></li>
                                                                <li><strong>Telephone:</strong> <input type="text" name="telephone" class="inputText" value="<? echo $form->value("telephone") ?>"/><? echo $form->error("contact_telephone"); ?></li>
                                                                <li><strong>Mobile:</strong> <input type="text" name="mobile" class="inputText" value="<? echo $form->value("mobile") ?>"/><? echo $form->error("contact_mobile"); ?></li>
                                                                <li><strong>Fax:</strong> <input type="text" name="fax" class="inputText" value="<? echo $form->value("fax") ?>"/><? echo $form->error("contact_fax"); ?></li>
                                                                <li><strong>Email:</strong> <input type="text" name="email" class="inputText" value="<? echo $form->value("email") ?>"/><? echo $form->error("contact_email"); ?></li>
                                                            </ul>
                                                        </div>
                                                        <div class="contactSocial">
                                                            <ul>
                                                                <li><h3>Social Information</h3></li>
                                                                <li><strong>Facebook:</strong> <input type="text" name="facebook" class="inputText" value="<? echo $form->value("facebook") ?>"/><? echo $form->error("contact_facebook"); ?></li>
                                                                <li><strong>Twitter:</strong> <input type="text" name="twitter"class="inputText" value="<? echo $form->value("twitter") ?>"/><? echo $form->error("contact_twitter"); ?></li>
                                                                <li><strong>Skype:</strong> <input type="text" name="skype" class="inputText" value="<? echo $form->value("skype") ?>"/><? echo $form->error("contact_skype"); ?></li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <div class="submitButton">
                                                        <input type="button" style="float:left;" class="closeTab_button" value="Close Tab"/>
                                                        <input type="hidden" name="contact_subcreate"  value="1"/>
                                                        <input type="submit" class="black_button" value="Create Contact"/>
                                                    </div>
                                                    <div class="clr"></div>
                                                </form>
                                            </div>
                                          </div>
                                        <div class="clr"></div>
                                        <div class="content-box-header nested"><h2>Appoinments</h2></div>
                                        <div class="content-box-content nestedContent">
                                            <? if ($AppointmentsPerProjectArray = $session->getAppointmentsPerProject($project)){
                                                $projectHasAppointments = true;
                                                ?>
                                            <div> 
                                                <table id="AppointmentsProjectTBL">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Time</th>
                                                            <th>User</th>
                                                            <th>Name</th>
                                                            <th>Description</th>
                                                            <th>Notes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <? foreach ($AppointmentsPerProjectArray as $appointment) { ?>
                                                        <tr>
                                                            <td><? echo $appointment[date]; ?></td>
                                                            <td><? echo $appointment[time]; ?></td>
                                                            <td><? echo $appointment[user]; ?></td>
                                                            <? if ($appointment[typeOf]=="4001"){ ?>
                                                            <td><? echo $appointment[4001][fName].' '. $appointment[4001][lName] ; ?></td>
                                                            <?} else {?>
                                                            <td><? echo $appointment[4002][fName].' '. $appointment[4002][lName].' - '.$appointment[position]; ?></td>
                                                            <? } ?>
                                                            <td><? echo $appointment[description]; ?></td>
                                                            <td><? echo $appointment[notes]; ?></td>                                                        
                                                        </tr>
                                                     <? } ?>
                                                     </tbody>
                                                  </table>
                                            </div>
                                            <div class="clr"></div>
                                            <? } else { ?>
                                            <div class="span_warning">
                                                <span >You have no Appointments for Project <u><? echo $project ?></u>!!</span>
                                            </div>
                                            <div class="clr"></div>
                                            <? } ?>
                                            <input type="button" style="float:left;" class="addAppoinment_button" value="Add Appoinment"/>
                                            <div class="clr"></div>
                                            <div class="addAppoinment" style="display:<?
                                                if ($form->error("vis_add_appoinment") != '') {
                                                    echo "block";
                                                    $err = true;
                                                } else {
                                                    echo "none";
                                                    $err = false;
                                                }
                                                ?>">
                                            <form name="addAppoinment_form" action="../process.php" method="POST">
                                                <div class="appoinmentCreate">
                                                    <div class="appoinmentDetails">
                                                        <ul>
                                                            <li><h3>Appoinment Details</h3></li>
                                                            <li><strong>When?</strong><input type="text" name="dateTime" id="datePick" class="inputText" value="<? echo $form->value("date") ?>"/><? echo $form->error("appoinmentDate"); ?></li>
                                                            <li><strong>Who?</strong> <input type="text" name="user" class="inputText" value="<? echo $form->value("user") ?>"/><? echo $form->error("user"); ?></li>
                                                            <li><strong>Select type of person that you have the appoinment:</strong><br/> 
                                                            <input type="radio" name="typeOfUser" class="inputText" value="4001" />Client<br/>
                                                            <? if ($projectHasContacts){ ?>
                                                                <input type="radio" name="typeOfUser" class="inputText" value="4002" />Contact</li>
                                                            <? } ?>  
                                                            <li id="client_select" style="display:none;">
                                                                <input  type="text" name="contactUser" disabled="disabled" class="inputText" value="<? echo $ClientArray[fName]." ".$ClientArray[lName]; ?>">
                                                                <input type="hidden" name="contactClientID" class="inputText" value="<? echo $ClientArray[id]; ?>" />   
                                                            </li>
                                                            <li id="contact_select" style="display:none;">
                                                                <strong>With Whom?</strong>
                                                                <select class="inputText" name="contactUserID">
                                                                    <option disabled="disabled" selected="selected">Select User</option>
                                                                    <? foreach ($ClientContactsPerProjectArray as $contact) { ?>
                                                                    <option value="<? echo $contact[id]; ?>"> <? echo $contact[fName].' '. $contact[lName].' - '.$contact[position]; ?></option>
                                                                    <? } ?>
                                                                </select>
                                                            </li>
                                                            <li><strong>Description:</strong> <textarea rows="2" cols="20" name="description" class="inputText"></textarea><? echo $form->error("description"); ?></li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="submitButton">
                                                    <input type="button" style="float:left;" class="closeTab_button" value="Close Tab"/>
                                                    <input type="hidden" name="proj" class="inputText" value="<? echo $project; ?>" />
                                                    <input type="hidden" name="appoinment_subcreate"  value="1"/>
                                                    <input type="submit" class="black_button" value="Create Appoinment"/>
                                                </div>
                                                <div class="clr"></div>
                                            </form>
                                            </div>
                                        </div>
                                        <div class="clr"></div>
                                        <div class="content-box-header nested"><h2>Report Activity</h2></div>
                                        <div class="content-box-content nestedContent">
                                            <ol  id="reportActivity_update" class="reportActivity_timeline">
                                                <? $activities = $session->activitiesOfProject($project);
                                                if (count($activities)>0){
                                                    foreach ($activities as $activity){
                                                            $avatar=$session->getUserAvatar($activity[commentator]);
                                                            if ($activity[typeOfActivity] == "5001"){
                                                                    $activityType = "Incoming Call";
                                                                }else if ($activity[typeOfActivity] == "5002"){
                                                                    $activityType = "Outcoming Call";
                                                                }else if ($activity[typeOfActivity] == "5003"){
                                                                    $activityType = "Incoming Email";
                                                                }else{
                                                                    $activityType = "Outcoming Email";
        }
                                                    ?>
                                                    <li>
                                                        <div class="reportActivity_profile">
                                                        <img src="<? echo ($avatar=="")?GENERIC_AVATAR:$avatar ?>" class="reportActivity_img"/>
                                                        </div>
                                                        <div class="reportActivity_details">
                                                            <h3><? echo $activityType ?></h3>
                                                            <div><? echo $activity[text_activity] ?></div>
                                                            <div><? echo $activity[commentator] ?> | <? echo $activity[timestamp] ?></div>
                                                        </div>
                                                        <div class="clr"></div>
                                                    </li>
                                                 <?}
                                              } else { ?>
                                                <li>
                                                    <div class="span_warning">
                                                    <span >You have no Report Activity for Project <u><? echo $project ?></u>!!</span>
                                                    </div>
                                                </li>
                                            <? } ?>
                                            </ol>
                                            <div class="clr"></div>
                                            <div id="reportActivity_flash" align="left"></div>
                                            <div>
                                                <form action="#!" method="post">
                                                    <select name="typeOfActivity" class="typeOfActivity">
                                                      <option class="dropDown" value="5001">Incoming Call</option>
                                                      <option class="dropDown" value="5002">Outcoming Call</option>
                                                      <option class="dropDown" value="5003">Incoming Email</option>
                                                      <option class="dropDown" value="5004">Outcoming Email</option>
                                                    </select>
                                                    <div class="reportActivity_profile">
                                                        <img src="<? echo $session->userinfo["avatar"] ?>" class="reportActivity_img">
                                                    </div>
                                                    <textarea name="reportActivity_comment" class="reportActivity_comment" placeholder="First Select Type of Activity and write a description about it..."></textarea><br />
                                                    <input type="hidden" name="reportActivity_proj" class="reportActivity_proj" value="<? echo $project; ?>" />
                                                    <input type="hidden" name="reportActivity_subcreate"  value="1"/>
                                                    <input type="submit" id="reportActivitySubmit" class="black_button" value="Create Activity" />
                                            </form>
                                            </div>
                                        </div>
                                        <div class="clr"></div>
                                        <div class="content-box-header nested"><h2>Notes</h2></div>
                                        <div class="content-box-content nested">

                                        </div>
                                        <div class="clr"></div>
                                    </div>
                                </div>
                                <?
                            }
                        }
                    }
                    ?>
                    <div class="clr"></div>
                    <div class="content-box clients">
                        <div class="content-box-header"><h2><img src="images/icons/user.gif" width="16" height="16" alt="All Clients" /> All Clients</h2><a style="" class="showProject_btn" href="#!"></a></div>
                        <div class="content-box-content">
                        <? $allClientsOverviewArray = $session->allClientsOverviewArray(); ?>
                            <table id="allClientsTBL">
                                <thead>
                                    <tr>
                                        <th>Client Id</th>
                                        <th>Client Name</th>
                                        <th>Telephone</th>
                                        <th>Mobile</th>
                                        <th>E-mail</th>
                                        <th>Amount</th>
                                        <th>Social</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <? foreach ($allClientsOverviewArray as $client) { ?>
                                        <tr>
                                            <td><a href="clients.php?v=Client&cid=<? echo $client[id]; ?>"><? echo $client[id]; ?></a></td>
                                            <td><a href="clients.php?v=Client&cid=<? echo $client[id]; ?>"><? echo $client[name]; ?></a></td>
                                            <td><? echo $client[telephone]; ?></td>
                                            <td><? echo $client[mobile]; ?></td>
                                            <td><? echo $client[email]; ?></td>
                                            <td><? echo $client[total_due]; ?></td>
                                            <td>
                                                <? if (!empty($client[Social][Facebook])) { ?>
                                                    <a href="http://www.facebook.com/<? echo $client[Social][Facebook]; ?>" target="_blank"><img src="images/icons/FaceBook_20x20.png"/></a>
                                                <? } ?>
                                                <? if (!empty($client[Social][Twitter])) { ?>
                                                    <a href="http://www.twitter.com/<? echo $client[Social][Twitter]; ?>" target="_blank"><img src="images/icons/Twitter_20x20.png"/></a>
                                                <? } ?>
                                                <? if (!empty($client[Social][Skype])) { ?>
                                                    <a href="skype:<? echo $client[Social][Skype]; ?>?call" target="_blank"><img src="images/icons/Skype_20x20.png"/></a>
                                            <? } ?>
                                            </td>
                                        </tr>
                                    <? } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <? if (!$oneClient) { ?>
                        <div class="column" id="left">
                            <!--THIS IS A PORTLET-->
                            <div class="content-box transactions">
                                <div class="content-box-header"><h2><img src="images/icons/chart_bar.gif" width="16" height="16" alt="Activity" /> Last 30 Transactions</h2></div>
                                <div class="content-box-content">
                                 <? $activityLast30Array = $session->activityLast30(); ?>
                                    <table id="Last30ActivityTBL">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Activity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <? foreach ($activityLast30Array as $activity) { ?>
                                             <? if ($activity[type] == 3001) { ?>
                                                    <tr>
                                                        <td><? echo $activity[happened]; ?></td>
                                                        <td>Billing <? echo $activity[amount]; ?>&euro; at project <? echo $activity[project]; ?></td>
                                                    </tr>
                                             <? } else if ($activity[type] == 3002) { ?>
                                                    <tr>
                                                        <td><? echo $activity[happened]; ?></td>
                                                        <td>Payment <? echo $activity[amount]; ?>&euro; at project <? echo $activity[project]; ?></td>
                                                    </tr>
                                             <? } else if ($activity[type] == 3003) { ?>
                                                    <tr>
                                                        <td><? echo $activity[happened]; ?></td>
                                                        <td>Invoice <? echo $activity[amount]; ?>&euro; at project <? echo $activity[project]; ?></td>
                                                    </tr>
                                             <? } else if ($activity[type] == 3004) { ?>
                                                    <tr>
                                                        <td><? echo $activity[happened]; ?></td>
                                                        <td>Expence <? echo $activity[amount]; ?>&euro; at <? echo $activity[operator]; ?></td>
                                                    </tr>
                                             <? } else if ($activity[type] == 3005) { ?>
                                                    <tr>
                                                        <td><? echo $activity[happened]; ?></td>
                                                        <td>Withdraw <? echo $activity[amount]; ?>&euro; from <? echo $activity[user]; ?></td>
                                                    </tr>
                                                <? } ?>
                                        <? } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                    <div class="clear"></div>
                    <!--THIS IS A WIDE PORTLET-->

                    <!--  END #PORTLETS -->
                </div>
                <div class="clear"> </div>
                <!-- END CONTENT-->
            </div>
            <div class="clear"> </div>

            <!-- This contains the hidden content for modal box calls -->
            <div class='hidden'>
                <div id="inline_example1" title="This is a modal box" style='padding:10px; background:#fff;'>
                    <p><strong>This content comes from a hidden element on this page.</strong></p>

                    <p><strong>Try testing yourself!</strong></p>
                    <p>You can call as many dialogs you want with jQuery UI.</p>
                </div>
            </div>
        </div>
        <!-- WRAPPER END -->
        <!-- FOOTER START -->
        <div class="container_16" id="footer">
            Website Administration Share by <a href="http://nicetheme.net/">Nice Theme</a></div>
        <!-- FOOTER END -->
    </body>
    <script type="text/javascript">
        /*Create Upload buttons and areas*/
        function createUploaderBilling(){
            var billingUploader = new qq.FileUploader({
                element: document.getElementById('billing-file-uploader'),
                action: 'uploadFinancial.php',
                debug: true
            });
        }
        function createUploaderPayment(){
            var paymentUploader = new qq.FileUploader({
                element: document.getElementById('payment-file-uploader'),
                action: 'uploadPayment.php',
                debug: true
            });
        }
        function createUploaderInvoice(){
            var invoiceUploader = new qq.FileUploader({
                element: document.getElementById('invoice-file-uploader'),
                action: 'uploadPayment.php',
                debug: true
            });
        }
        function createUploaderExpense(){
            var expenseUploader = new qq.FileUploader({
                element: document.getElementById('expense-file-uploader'),
                action: 'uploadExpence.php',
                debug: true
            });
        }
        function start() {
            createUploaderBilling();
            createUploaderPayment();
            createUploaderInvoice();
            createUploaderExpense();
        }
        window.onload = start;
    </script>
</html>
