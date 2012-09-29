/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
    
     $(".content-box-content:visible").siblings(".content-box-header").children(".showProject_btn").attr("style","-moz-transform: rotate(180deg);");
       
        //Show the Project div
    $(".showProject_btn").click( function() {
        var visibleDiv = $(this).parent().siblings(".content-box-content").is(":visible");
		
        $(this).parent().siblings(".content-box-content").slideToggle("medium");
		
        if (visibleDiv==false){
            $(this).attr("style","-moz-transform: rotate(180deg);");
        }
        else{
            $(this).attr("style","");
        }
    });
        /*Start of Fast Tab Menu*/
        $(".addClient_button").click(function(){
            $(".addClient").slideToggle();
            $(".addBilling").slideUp();
            $(".addPayment").slideUp();
            $(".addInvoice").slideUp();
            $(".addExpence").slideUp();
            $(".withDraw").slideUp();
            $(".deposit").slideUp();
        });

        $(".addBilling_button").click(function(){
            $(".addBilling").slideToggle();
            $(".addClient").slideUp();
            $(".addPayment").slideUp();
            $(".addInvoice").slideUp();
            $(".addExpence").slideUp();
            $(".withDraw").slideUp();
            $(".deposit").slideUp();
	});
         $(".addPayment_button").click(function(){
            $(".addPayment").slideToggle();
            $(".addBilling").slideUp();
            $(".addClient").slideUp();
            $(".addInvoice").slideUp();
            $(".addExpence").slideUp();
            $(".withDraw").slideUp();
            $(".deposit").slideUp();
	});

        $(".addInvoice_button").click(function(){
            $(".addInvoice").slideToggle();
            $(".addBilling").slideUp();
            $(".addClient").slideUp();
            $(".addPayment").slideUp();
            $(".addExpence").slideUp();
            $(".withDraw").slideUp();
            $(".deposit").slideUp();
	});
        $(".addExpence_button").click(function(){
            $(".addExpence").slideToggle();
            $(".addBilling").slideUp();
            $(".addClient").slideUp();
            $(".addPayment").slideUp();
            $(".addInvoice").slideUp();
            $(".withDraw").slideUp();
            $(".deposit").slideUp();
	});
        $(".withDraw_button").click(function(){
            $(".withDraw").slideToggle();
            $(".addBilling").slideUp();
            $(".addClient").slideUp();
            $(".addPayment").slideUp();
            $(".addInvoice").slideUp();
            $(".addExpence").slideUp();
            $(".deposit").slideUp();
	});
         $(".deposit_button").click(function(){
            $(".deposit").slideToggle();
            $(".addBilling").slideUp();
            $(".addClient").slideUp();
            $(".addPayment").slideUp();
            $(".addInvoice").slideUp();
            $(".addExpence").slideUp();
            $(".withDraw").slideUp();
	});

        $('.invoice').click(function(){
        if ($(this).attr('checked')) {
            $(".paymentInvoice").slideDown();
        }else {
            $(".paymentInvoice").slideUp();
        }
        });
        $(".addContact_button").click(function(){
            $(".addContact").slideToggle();
            $(".addAppoinment").slideUp();
        });
        $(".addAppoinment_button").click(function(){
            $(".addAppoinment").slideToggle();
            $(".addContact").slideUp();
        });
        $(".closeTab_button").click(function(){
            $(this).parent().parent().parent().slideUp();
	});
       
        /*End of Fast Tab Menu*/

        /*Start of AJAX Search and Project Population*/
        $("input[name=client]").autocomplete("get_client_list.php?action=ajaxrequest", {
	        width: 260,
	        matchContains: true,
	        selectFirst: false
	    }).keydown(function(){
                //if ($(this).val!=''){
                var datastring='id='+$(this).val();
                $("select#proj_select_addBill").attr("disabled","disabled");
                //var error = '<option disabled="disabled" selected="selected" value="">Loading..</option>';
                    $.ajax({
                        type: "POST",
                        url: "get_project_per_client.php?action=ajaxrequest",
                        data: datastring,
                        success: function(options){
                            $("select#proj_select_addBill").removeAttr("disabled").html(options);
                           
                        }
                    });
               });
           $("input[name=clientPay]").autocomplete("get_client_list.php?action=ajaxrequest", {
	        width: 260,
	        matchContains: true,
	        selectFirst: false
	    }).keydown(function(){
                //if ($(this).val!=''){
                var datastring='id='+$(this).val();
                $("select#proj_select_addPay").attr("disabled","disabled");
                //var error = '<option disabled="disabled" selected="selected" value="">Loading..</option>';
                    $.ajax({
                        type: "POST",
                        url: "get_project_per_client.php?action=ajaxrequest",
                        data: datastring,
                        success: function(options){ 
                            $("select#proj_select_addPay").removeAttr("disabled").html(options);

                        }
                    });
               });
            $("input[name=clientInvoice]").autocomplete("get_client_list.php?action=ajaxrequest", {
	        width: 260,
	        matchContains: true,
	        selectFirst: false
	    }).keydown(function(){
                //if ($(this).val!=''){
                var datastring='id='+$(this).val();
                $("select#proj_select_addInvoice").attr("disabled","disabled");
                //var error = '<option disabled="disabled" selected="selected" value="">Loading..</option>';
                    $.ajax({
                        type: "POST",
                        url: "get_project_per_client.php?action=ajaxrequest",
                        data: datastring,
                        success: function(options){
                            $("select#proj_select_addInvoice").removeAttr("disabled").html(options);

                        }
                    });
               });
        $("input[name=user]").autocomplete("get_user_list.php?action=ajaxrequest", {
            width: 260,
            matchContains: true,
            selectFirst: false
        });
        /*End of AJAX Search and Project Population*/
        
         $("input:radio[name='typeOfUser']").change(function(){
            if ($(this).val()=="4001"){
                $("#client_select").show();
                $("#contact_select").hide();
            }else if ($(this).val()=="4002"){
                $("#client_select").hide();
                $("#contact_select").show();
            }
         });
         
         $('.edit').editable('../process.php?action=ajaxrequest_clientEdit',{
            indicator : 'Saving...',
            cancel    : 'Cancel',
            submit    : 'Edit',
            event : 'dblclick',
            tooltip : 'Doubleclick to edit...',
            style   : 'display: inline',
            id   : 'elementid',
            name : 'newvalue',
            onsubmit: function(){
                var value = $("input", this).val();
                if (value==""){return confirm("Are you sure you want to delete value?");}
                else return confirm("Are you sure you want to edit value?");
            }
        });
        
        /*Report Activity*/
        $("#reportActivitySubmit").click(function() {

            var activity = $(".reportActivity_comment").val();
            var project_id = $(".reportActivity_proj").val();
            var typeOf = $(".typeOfActivity").val();
            
            var dataString = '&activity=' + activity + '&project_id=' + project_id + '&typeOf=' + typeOf + '&reportActivity_subcreate=1';
            if(activity=='')
                 {
                alert('Please write an Activity');
                 }
            else
            {
                $("#reportActivity_flash").show();
                $("#reportActivity_flash").fadeIn(400).html('<span class="loading">Loading Activity...</span>');
                $.ajax({
                   type: "POST",
                   url: "../process.php?action=ajaxrequest",
                   data: dataString,
                   cache: false,
                   success: function(html){
                        $("ol#reportActivity_update").prepend(html);
                        $('.reportActivity_comment').value='';
                        $("#reportActivity_flash").hide();
                    }
                });
            }
        return false;
	});
        
        
        
        /*Clients Data Table*/
        $('#allClientsTBL').dataTable();
        $('#Last30ActivityTBL').dataTable({
            "aaSorting": [[ 0, "desc" ]]
        });   
        $('.clientActivityTBL').dataTable({
            "aaSorting": [[ 0, "desc" ]]
        });
        $('#ClientProjectContactsTBL').dataTable();        
        $('#AppointmentsProjectTBL').dataTable();
        
        
        if ($("#datePick").length>0){
        $("#datePick").datetimepicker({
            dateFormat: "dd/mm/yy",
            showOn: "button",
            minDate:0,
            buttonImage: "../../styling/images/calendar-icon-16.png"
        });
        }
       
        
        
});



