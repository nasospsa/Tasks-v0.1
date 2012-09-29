<!--LOGO-->
            <div class="top_header">
                 <div class="grid_8" id="logo"><a href="#" style="color: #FFFFFF">Gan Web - Client Administration</a></div>
                        <div class="grid_8">
                            <!-- USER TOOLS START -->
                            <div id="user_tools"><span> Welcome <? echo $session->username ?> | <a id="logout" href="../process.php">Logout</a></span>
                             <span id="homeAllTasksLink">
                                <a href="../" target="_blank">Tasks</a>
                            </span>
                            </div>
                        </div>
                 
            </div>

           
            <!-- USER TOOLS END -->
            <div class="grid_16" id="header">
                <!-- MENU START -->
                <div id="menu">
                    <ul class="group" id="menu_group_main">
                        <?if ($menuCurrent["DashBoard"] == "current"){ ?>
                        <li class="item first" id="one"><a href="main.php?v=Dashboard" class="main current"><span class="outer"><span class="inner dashboard">Dashboard</span></span></a></li>
                        <? }else { ?>
                        <li class="item first" id="one"><a href="main.php?v=Dashboard" class="main"><span class="outer"><span class="inner dashboard">Dashboard</span></span></a></li>
                        <? } ?>
                        <?if ($menuCurrent["Clients"] == "current"){ ?>
                        <li class="item middle" id="two"><a href="clients.php?v=All" class="main current"><span class="outer"><span class="inner users">Clients</span></span></a></li>
                        <? }else { ?>
                        <li class="item middle" id="two"><a href="clients.php?v=All" class="main"><span class="outer"><span class="inner users">Clients</span></span></a></li>
                         <? } ?>
                        <li class="item middle" id="three"><a href="#" class="main"><span class="outer"><span class="inner reports png">Reports</span></span></a></li>
                        <li class="item last" id="four"><a href="#" class="main"><span class="outer"><span class="inner cashbox">Funds</span></span></a></li>
                    </ul>
                </div>
                <!-- MENU END -->
                <div>
                    <?
            if (isset($_SESSION['success'])) {
                /* Successful Message */
                if ($_SESSION['success']) {
                    ?>

<p class="info" id="success"><span class="info_inner"><? echo $_SESSION['header'] ?></span></p>
                        

                        <?
                    }
                    /* Failure Message */ else {
                        ?>
                        <p class="info" id="error"><span class="info_inner"><? echo $_SESSION['header'] ?></span></p>
                        <?
                    }
                    unset($_SESSION['success']);
                    unset($_SESSION['header']);
                }
                ?>
                </div>
            </div>
            <div class="grid_16">
                <!-- TABS START -->
                <div id="tabs">
                    <div class="container">
                        <ul>
                            <li><a href="#" class="addClient_button"><span>Add Client</span></a></li>
                            <li><a href="#" class="addBilling_button"><span>Add Billing</span></a></li>
                            <li><a href="#" class="addPayment_button"><span>Add Payment</span></a></li>
                            <li><a href="#" class="addInvoice_button"><span>Add Invoice</span></a></li>
                            <li><a href="#" class="addExpence_button"><span>Add Expense</span></a></li>
                            <li><a href="#" class="withDraw_button"><span>Withdraw</span></a></li>
                            <li><a href="#" class="deposit_button"><span>Deposit</span></a></li>
                        </ul>
                    </div>

                    <div class="addClient" style="display:<?
                        if ($form->error("vis_add_client") != '') {
                            echo "block";
                            $err = true;
                        } else {
                            echo "none";
                            $err = false;
                        }
                        ?>">
                        <form name="addClient_form" action="../process.php" method="POST">
                            <div class="clientCreate">
                                <div class="clientDetails">
                                    <ul>
                                        <li><h3>Personal Details</h3></li>
                                        <li><strong>First Name:</strong> <input type="text" name="firstname" class="inputText" value="<? echo $form->value("firstname") ?>" /><? echo $form->error("fname"); ?></li>
                                        <li><strong>Last Name:</strong> <input type="text" name="lastname" class="inputText" value="<? echo $form->value("lastname") ?>"/><? echo $form->error("lname"); ?></li>
                                        <li><strong>Project:</strong><br/>
                                            <? if (count($projects) > 1) { ?>
                                                <select name="proj" id="proj_select" class="inputText"  >
                                                    <option class="dropDown" disabled="disabled" value="" selected>Select Project</option>
                                                <?
                                                foreach ($projects as $project => $tasks) {
                                                    echo "<option ";
                                                    if ($project == $selectedPro) {
                                                        echo "selected ";
                                                    }
                                                    echo "class=\"dropDown\" value=\"$project\">$project</option>";
                                                }
                                                unset($project);
                                                ?>
                                            </select>
                                            <?
                                            } else if (count($projects) == 1) {
                                                //reset($projects_array_tasks);
                                                echo "<input disabled type=\"text\" value=\"" . key($projects) . "\" />";
                                                echo "<input nane=\"proj\" type=\"hidden\" value=\"" . key($projects) . "\" />";
                                            } else {
                                                echo "<strong>No Project</strong>";
                                            }
                                            ?>
                                        </li>
                                        <li><h3>Contact Information</h3></li>

                                        <li><strong>Telephone:</strong> <input type="text" name="telephone" class="inputText" value="<? echo $form->value("telephone") ?>"/><? echo $form->error("telephone"); ?></li>
                                        <li><strong>Mobile:</strong> <input type="text" name="mobile" class="inputText" value="<? echo $form->value("mobile") ?>"/><? echo $form->error("mobile"); ?></li>
                                        <li><strong>Fax:</strong> <input type="text" name="fax" class="inputText" value="<? echo $form->value("fax") ?>"/><? echo $form->error("fax"); ?></li>
                                        <li><strong>Email:</strong> <input type="text" name="email" class="inputText" value="<? echo $form->value("email") ?>"/><? echo $form->error("email"); ?></li>
                                    </ul>
                                </div>
                                <div class="clientInfo">
                                    <ul>
                                        <li><h3>Billing Information</h3></li>
                                        <li><strong>Company Name:</strong> <input type="text" name="companyName" class="inputText" value="<? echo $form->value("companyName") ?>"/><? echo $form->error("companyName"); ?></li>
                                        <li><strong>Company Type:</strong> <input type="text" name="companyType" class="inputText" value="<? echo $form->value("companyType") ?>"/><? echo $form->error("companyType"); ?></li>
                                        <li><strong>Tax Office:</strong> <input type="text" name="taxOffice" class="inputText" value="<? echo $form->value("taxOffice") ?>"/><? echo $form->error("taxOffice"); ?></li>
                                        <li><strong>VAT Number:</strong> <input type="text" name="vatNumber" class="inputText" value="<? echo $form->value("vatNumber") ?>"/><? echo $form->error("vatNumber"); ?></li>
                                        <li><strong>Address:</strong> <input type="text" name="address" class="inputText" value="<? echo $form->value("address") ?>"/><? echo $form->error("address"); ?></li>
                                        <li><strong>Town:</strong> <input type="text" name="town" class="inputText" value="<? echo $form->value("town") ?>"/><? echo $form->error("town"); ?></li>
                                        <li><strong>ZIP:</strong> <input type="text" name="zip" class="inputText" value="<? echo $form->value("zip") ?>"/><? echo $form->error("zip"); ?></li>
                                        <li><strong>Country:</strong> <input type="text" name="country" class="inputText" value="<? echo $form->value("country") ?>"/><? echo $form->error("country"); ?></li>
                                    </ul>
                                </div>
                                <div class="clientSocial">
                                    <ul>
                                        <li><h3>Social Information</h3></li>
                                        <li><strong>Facebook:</strong> <input type="text" name="facebook" class="inputText" value="<? echo $form->value("facebook") ?>"/><? echo $form->error("facebook"); ?></li>
                                        <li><strong>Twitter:</strong> <input type="text" name="twitter"class="inputText" value="<? echo $form->value("twitter") ?>"/><? echo $form->error("twitter"); ?></li>
                                        <li><strong>Skype:</strong> <input type="text" name="skype" class="inputText" value="<? echo $form->value("skype") ?>"/><? echo $form->error("skype"); ?></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="submitButton">
                                <input type="button" style="float:left;" class="closeTab_button" value="Close Tab"/>
                                <input type="hidden" name="client_subcreate"  value="1"/>
                                <input type="submit" class="black_button" value="Create Client"/>
                            </div>
                            <div class="clr"></div>
                        </form>

                    </div>
                    <div class="addBilling" style="display:<?
                        if ($form->error("vis_add_billing") != '') {
                            echo "block";
                            $err = true;
                        } else {
                            echo "none";
                            $err = false;
                        }
                        ?>">

                        <form name="addBilling_form" action="../process.php" method="POST">
                            <div class="billingCreate">
                                <div class="billingDetails">
                                    <ul>
                                        <li><h3>Billing Details</h3></li>
                                        <li><strong>Client:</strong> <input type="text" name="client"  class="inputText" value="<? echo $form->value("client") ?>"/><? echo $form->error("client"); ?></li>
                                        <li><strong>Project:</strong>
                                            <select name="proj" id="proj_select_addBill" disabled="disabled" class="inputText">
                                                <option disabled="disabled" selected="selected" class="dropDown" value="0">Select Client</option>
                                            </select>
                                            <? echo $form->error("proj"); ?>
                                        </li>
                                        <li><strong>Amount:</strong><input type="text" name="amount" class="inputText" value="<? echo $form->value("amount") ?>"/><? echo $form->error("amount"); ?></li>
                                        <li><strong>Description:</strong><textarea rows="2" cols="35" name="description" class="inputText"></textarea><? echo $form->error("description"); ?></li>
                                        <li><strong>Financial Offer:</strong>
                                            <div id="billing-file-uploader">
                                                <noscript>
                                                    <p>Please enable JavaScript to use file uploader.</p>
                                                    <!-- or put a simple form for upload here -->
                                                </noscript>
                                            </div>
                                         </li>
                                     </ul>
                                </div>
                            </div>
                            <div class="submitButton">
                                <input type="button" style="float:left;" class="closeTab_button" value="Close Tab"/>
                                <input type="hidden" name="billing_subcreate"  value="1"/>
                                <input type="submit" class="black_button" value="Create Billing"/>
                            </div>
                            <div class="clr"></div>
                        </form>
                    </div>
                    <div class="addPayment" style="display:<?
                        if ($form->error("vis_add_payment") != '') {
                            echo "block";
                            $err = true;
                        } else {
                            echo "none";
                            $err = false;
                        }
                        ?>">
                        <form name="addPayment_form" action="../process.php" method="POST">
                            <div class="paymentCreate">
                                <div class="paymentDetails">
                                    <ul>
                                        <li><h3>Payment Details</h3></li>
                                        <li><strong>Client:</strong> <input type="text" name="clientPay" class="inputText" value="<? echo $form->value("clientPay") ?>"/><? echo $form->error("clientPay"); ?></li>
                                        <li><strong>Project:</strong>
                                        <select name="proj" id="proj_select_addPay" disabled="disabled" class="inputText">
                                                <option disabled="disabled" selected="selected" class="dropDown" value="0">Select Client</option>
                                        </select>
                                            <? echo $form->error("proj"); ?>
                                        </li>
                                        <li><strong>Amount:</strong> <input type="text" name="amount" class="inputText" value="<? echo $form->value("amount") ?>"/><? echo $form->error("amount"); ?></li>
                                        <li><strong>Description:</strong> <textarea rows="2" cols="20" name="description" class="inputText"></textarea></li>
                                    </ul>
                                </div>
                                <strong>Create Invoice?</strong> 
                               
                                
                                <input type="checkbox" name="payment_invoiceCreate" class="invoice" id="check"/>
                                <div class="paymentInvoice">
                                    <ul>
                                        <li><strong>Amount:</strong> <input type="text" name="invoiceAmount" class="inputText" value="<? echo $form->value("invoiceAmount") ?>"/><? echo $form->error("invoiceAmount") ?></li>
                                        <li><strong>VAT:</strong><input type="text" disabled="disabled" name="vatDis" class="inputText" value="23%" />
                                        <input type="hidden" name="vat" class="inputText" value="0.23" /></li>
                                        <li><strong>Invoice:</strong>
                                            <div id="payment-file-uploader">
                                                <noscript>
                                                    <p>Please enable JavaScript to use file uploader.</p>
                                                    <!-- or put a simple form for upload here -->
                                                </noscript>
                                            </div>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                            <div class="submitButton">
                                <input type="button" style="float:left;" class="closeTab_button" value="Close Tab"/>
                                <input type="hidden" name="payment_subcreate"  value="1"/>
                                <input type="submit" class="black_button" value="Create Payment"/>
                            </div>
                            <div class="clr"></div>
                        </form>
                    </div>
                    <div class="addInvoice" style="display:<?
                        if ($form->error("vis_add_invoice") != '') {
                            echo "block";
                            $err = true;
                        } else {
                            echo "none";
                            $err = false;
                        }
                        ?>">
                        <form name="addInvoice_form" action="../process.php" method="POST">
                            <div class="invoiceCreate">
                                <div class="invoiceDetails">
                                    <ul>
                                        <li><h3>Invoice Details</h3></li>
                                        <li><strong>Client:</strong> <input type="text" name="clientInvoice" id="client" class="inputText" value="<? echo $form->value("clientInvoice") ?>"/><? echo $form->error("clientPay"); ?></li>
                                        <li><strong>Project:</strong>
                                        <select name="proj" id="proj_select_addInvoice" disabled="disabled" class="inputText">
                                                <option disabled="disabled" selected="selected" class="dropDown" value="0">Select Client</option>
                                        </select>
                                        <? echo $form->error("proj"); ?>
                                        </li>
                                        <li><strong>Amount:</strong> <input type="text" name="amount" class="inputText" value="<? echo $form->value("amount") ?>"/><? echo $form->error("amount"); ?></li>
                                        <li><strong>VAT:</strong> <input type="text" disabled="disabled" name="vatDis" class="inputText" value="23%" />
                                        <input type="hidden" name="vat" class="inputText" value="0.23" /></li>
                                        <li><strong>Invoice:</strong>
                                            <div id="invoice-file-uploader">
                                                <noscript>
                                                    <p>Please enable JavaScript to use file uploader.</p>
                                                    <!-- or put a simple form for upload here -->
                                                </noscript>
                                            </div>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                            <div class="submitButton">
                                <input type="button" style="float:left;" class="closeTab_button" value="Close Tab"/>
                                <input type="hidden" name="invoice_subcreate"  value="1"/>
                                <input type="submit" class="black_button" value="Create Invoice"/>
                            </div>
                            <div class="clr"></div>
                        </form>
                    </div>
                    <div class="addExpence" style="display:<?
                        if ($form->error("vis_add_expense") != '') {
                            echo "block";
                            $err = true;
                        } else {
                            echo "none";
                            $err = false;
                        }
                        ?>">
                        <form name="addExpense_form" action="../process.php" method="POST">
                            <div class="expenceCreate">
                                <div class="expenseDetails">
                                    <ul>
                                        <li><h3>Expense Details</h3></li>
                                        <li><strong>Operator:</strong> <input type="text" name="operator" class="inputText" value="<? echo $form->value("operator") ?>"/><? echo $form->error("operator"); ?></li>
                                        <li><strong>Amount:</strong> <input type="text" name="amount" class="inputText" value="<? echo $form->value("amount") ?>"/><? echo $form->error("amount"); ?></li>
                                        <li><strong>VAT:</strong> <input type="text" disabled="disabled" name="vatDis" class="inputText" value="23%" />
                                        <input type="hidden" name="vat" class="inputText" value="0.23" /></li>
                                        <li><strong>Invoice:</strong>
                                            <div id="expense-file-uploader">
                                                <noscript>
                                                    <p>Please enable JavaScript to use file uploader.</p>
                                                    <!-- or put a simple form for upload here -->
                                                </noscript>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="submitButton">
                                <input type="button" style="float:left;" class="closeTab_button" value="Close Tab"/>
                                <input type="hidden" name="expence_subcreate"  value="1"/>
                                <input type="submit" class="black_button" value="Create Expence"/>
                            </div>
                            <div class="clr"></div>
                        </form>
                    </div>
                    <div class="withDraw" style="display:<?
                        if ($form->error("vis_add_withdraw") != '') {
                            echo "block";
                            $err = true;
                        } else {
                            echo "none";
                            $err = false;
                        }
                        ?>">
                        <form name="addWithdraw_form" action="../process.php" method="POST">
                            <div class="withdrawCreate">
                                <div class="withdrawDetails">
                                    <ul>
                                        <li><h3>Withdraw Details</h3></li>
                                        <li><strong>User:</strong> <input type="text" name="user" class="inputText" value="<? echo $form->value("user") ?>"/><? echo $form->error("user"); ?></li>
                                        <li><strong>Amount:</strong> <input type="text" name="amount" class="inputText" value="<? echo $form->value("amount") ?>"/><? echo $form->error("amount"); ?></li>
                                        <li><strong>Description:</strong><textarea rows="2" cols="20" name="description" class="inputText"></textarea></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="submitButton">
                                <input type="button" style="float:left;" class="closeTab_button" value="Close Tab"/>
                                <input type="hidden" name="withdraw_subcreate"  value="1"/>
                                <input type="submit" class="black_button" value="Create Withdraw"/>
                            </div>
                            <div class="clr"></div>
                        </form>
                    </div>
                    
                    <div class="deposit" style="display:<?
                        if ($form->error("vis_add_deposit") != '') {
                            echo "block";
                            $err = true;
                        } else {
                            echo "none";
                            $err = false;
                        }
                        ?>">
                        <form name="addDeposit_form" action="../process.php" method="POST">
                            <div class="depositCreate">
                                <div class="depositDetails">
                                    <ul>
                                        <li><h3>Deposit Details</h3></li>
                                        <li><strong>User:</strong> <input type="text" name="user" class="inputText" value="<? echo $form->value("user") ?>"/><? echo $form->error("user"); ?></li>
                                        <li><strong>Amount:</strong> <input type="text" name="amount" class="inputText" value="<? echo $form->value("amount") ?>"/><? echo $form->error("amount"); ?></li>
                                        <li><strong>Description:</strong><textarea rows="2" cols="20" name="description" class="inputText"></textarea></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="submitButton">
                                <input type="button" style="float:left;" class="closeTab_button" value="Close Tab"/>
                                <input type="hidden" name="deposit_subcreate"  value="1"/>
                                <input type="submit" class="black_button" value="Create Deposit"/>
                            </div>
                            <div class="clr"></div>
                        </form>
                    </div>
                </div>
                <!-- TABS END -->
            </div>