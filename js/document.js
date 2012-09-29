// JavaScript Document
$(document).ready(function() {
    //J Debug
	
    if ($("#no_focus").length==0){
        $(":text:first").focus();
    }
	
	
    $(".content-box-content:visible").siblings(".content-box-header").children(".showProject_btn").attr("style","-moz-transform: rotate(180deg);");
	
	
    if ($("#mainmenu").length>0){
        //Align list-items accross width dynamically 
        var liwidth = $("#mainmenu ul").width() / $("#mainmenu ul li").size();
        var extrawidth = parseInt($("#mainmenu ul li").css("padding-left"));
        extrawidth += parseInt($("#mainmenu ul li").css("padding-right"));
        extrawidth += parseInt($("#mainmenu ul li").css("margin-left"));
        extrawidth += parseInt($("#mainmenu ul li").css("margin-right"));
        liwidth = liwidth - extrawidth;
        liwidth = Math.round(liwidth);
        liwidth -= 1;
        $("#mainmenu ul li").width(liwidth+"px");
        $("#mainmenu ul").lavaLamp();
    }

    $(".userInfo_button, #userMenuLink").click(function(){
        $(".userMenu").slideToggle();
        $("#notifications").slideUp();
    });
    $("#notificationsBtn, #notificationsLink").click(function(){
        $("#notifications").slideToggle();
        $(".userMenu").slideUp();
    });
    
    
        
    //Make important divs curved-cornered
    //$(".content-box").corner("10px");
    $(".content-box-header").corner("top 10px");
    $(".content-box-content").corner("bottom 10px");
    //$(".content-box-header-side").corner("top 15px");
	
    $(".rounded").corner();
    $("#mainmenu ul").corner();
    $("#mainmenu ul li").corner();
    $("#wrapper").corner();
    $("#left").corner();
    $("#right").corner();
    $("#footer").corner("top");
    $(".project").corner();
    $(".deleteproject").corner("round 30px");
    $(".success.add_task_msg").corner();
    $(".blue_btn, .red_btn, .green_btn").corner("8px");
    
    $("#chat_active").corner("top");

    

    setInterval(function () {
        $('.chatboxhead').corner("top");
    }, 100);
    
    $("#chat_list ul li").corner();
    
    
    if ($("#center > .project").length>1){
        $("#center").sortable({
            handle: '.content-box-header', 
            items:'.project',
            stop:function(event, ui) {
                $(this).sortable('toArray');
                var dataString = $(this).sortable('toArray').join(",").replace(/proj_/g,"");
		dataString = 'action=ajaxrequest&sortedList='+ dataString + '&subSortedProjectList=1';
                $.ajax({
                    type: "POST",
                    url: "process.php",
                    data: dataString,
                    cache: false,
                    success: function(html){
                    //Success Message
                    }
                });
            }
        });
    }
	
	
    //Click Alert Functions
    $(".sts").click( function() {
        return confirm("Είστε σίγουρος για την αλλαγή status στο task?");
    });
    $(".addtask_form_btn").click( function() {
        return confirm("Είστε σίγουρος για την εισαγωγή του task?");
    });
    $(".editproject_form_btn").click( function() {
        return confirm("Είστε σίγουρος για την επεξ/σία του project?");
    });
    $(".confirm").click( function() {
        return confirm("Are you sure?");
    });
	
	
	
    //link options of Project select
    $("#proj_select,#task_select").change(function(){
        var fname = window.location.pathname.substring(window.location.pathname.lastIndexOf("/")+1);
        if (this.value!=''){
            if ($(this).attr('id')=="proj_select"){
            	//window.location=fname+'?p='+this.value;
            	window.location='main.php'+'?p='+this.value;				
            }
            else if ($(this).attr('id')=="task_select"){
                //window.location=fname+'?t='+this.value;
                window.location='task.php'+'?t='+this.value;
            }
			
        }		
        else window.location=fname;
		
    });



	
	
    //change bgcolor on focus
    $("#center input:text,#center textarea").live("focus", function() {
        $(this).addClass('focused');
    });
    $("#center input:text,#center textarea").live("blur", function() {
        $(this).removeClass('focused');
    });
    /*
    if ($(".datePick").length>0){
        $(".datePick").datepicker({
            dateFormat: "dd/mm/yy",
            showOn: "button",
            buttonImage: "styling/images/calendar-icon-16.png"
        });
    }*/
    var dates = $(".datePick").datepicker({
        defaultDate: "+1w",
        autoSize: true,
        dateFormat: "dd/mm/yy",
        showOn: "both",
        buttonImage: "styling/images/calendar-icon-16.png",
        changeMonth: true,
        onSelect: function( selectedDate ) {
            var option = this.id == "from" ? "minDate" : "maxDate",
                instance = $( this ).data( "datepicker" ),
                date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings );
            dates.not( this ).datepicker( "option", option, date );
        }
    });
    
    
    
    //date input -> load date selectors and make disabled if no URL present
    if ($(".temp_date").length>0){
        $(".temp_date").jdPicker();
    }
        

	
    $(".url").keypress(function () {
        $(".temp_date").removeAttr('disabled');
        $(".temp_date").removeClass('disabled');
    });


	
    $(".temp_date").each(function (){
        if ($(this).val()!=''){
            $(this).removeAttr('disabled');
            $(this).removeClass('disabled');
        }
    });



	
    //initialisation of values
    $(".assignedUsers").val('');
	
	
    //Show the addTask div
    $(".addtask_link").click( function() {
        $(this).siblings(".addtask_div").slideToggle("medium");
    });
	
    //Show the Project Tasks div
    $(".showProject_btn").click( function() {
        var visibleDiv = $(this).parent().siblings(".content-box-content").is(":visible");
		
        $(this).parent().siblings(".content-box-content").slideToggle("medium");
		
        if (visibleDiv==false){
            $(this).attr("style","-moz-transform: rotate(180deg);");
            $(this).siblings(".span_TaskCounter").css("display","none");
        }
        else{
            $(this).attr("style","");
            $(this).siblings(".span_TaskCounter").css("display","block");
        }
    });
    
    //show The Custom Timer
    $("#addCustomTimerLink").click(function(){
        $("#addCustomTimerDetailsWrapper").slideToggle();
    });
    
    /*timesheets*/
    $("input:radio[name='lvl1filter']").change(function(){
        $(this).parent().siblings(".lvl1SelectContainer").show();
        $(this).parent().parent().siblings("span.error-msg").css("display","none");
        
        $(this).parent().parent().siblings().children(".lvl1SelectContainer").hide();
        if ($(this).val()=="project"){
            $("#lvl2Group").html("<option value=\"plain\">Plain</option><option value=\"user\">User</option><option value=\"task\">Task</option>");
        }else if ($(this).val()=="task"){
            $("#lvl2Group").html("<option value=\"plain\">Plain</option><option value=\"user\">User</option>");
        }else if ($(this).val()=="user"){
            $("#lvl2Group").html("<option value=\"user\">Plain</option><option value=\"project\">Project</option><option value=\task\">Task</option>");
        }else if ($(this).val()=="none"){
            $("#lvl2Group").html("");
        }
    });
    
    $("select#lvl1TaskProject").change(function(){
        var dataString = 'action=ajaxrequest&populate_task_select=1&project='+$(this).val();
        $("select#lvl1Task").html("<option>Loading...</option>")
        $.ajax({
            type: "POST",
            url: "process.php",
            data: dataString,
            cache: false,
            success: function(html){
                if (html!=""){
                    $("select#lvl1Task").html(html).removeAttr("disabled");
                }
            }
        });
    });
    
    /*end of timesheets*/




    //Click of the addTask btn
    $(".adduser_btn").click(function(){		//get value of the newuser	
        var newuser = $(this).siblings(".assignNewTask_select").val();
        var already = $(this).parent().siblings("td").children(".assignedUsers").val();
        var users_array = already.split(",");
		
        if (jQuery.inArray(newuser,users_array)==-1){
            var remove_user_link = "<span class=\"uassign\">"+newuser + " <a name=\"" + newuser + "\" class=\"removeUser\" href=\"#!\">x</a></span>";
			
            if (already!=''){
                $(this).parent().siblings("td").children(".assignedUsers").val(already+","+newuser);
            }
            else $(this).parent().siblings("td").children(".assignedUsers").val(newuser);
			
            $(this).parent().siblings("td").children(".users_assigned_links").append(remove_user_link);

        }
				
    });


        

	
    //X click next to assigned user
    $(".removeUser").live('click',function() {
        var curr_user = $(this).attr('name');
        var already = $(this).parent().parent().siblings(".assignedUsers").val();
        var users_array = already.split(",");
        users_array = jQuery.grep(users_array, function(value) {
            return value != curr_user;
        });
        users_array = users_array.join(",");
        $(this).parent().parent().siblings(".assignedUsers").val(users_array);
        $(this).parent().slideToggle("medium", function() {
            $(this).remove();
        } );		
    });


    //Avatar Selection
    $(".select_avatar").click(function(){
        $(this).children("input").attr('checked', true);
        $("#avatar_url").addClass("disabled");
        $("#avatars_td").removeClass("disabled");
        $("#avatar_type").val("predefined");
    });

    $("#avatar_url").focus(function(){
        $(this).removeClass("disabled");
        $("#avatars_td").addClass("disabled");
        $("#avatar_type").val("url");
    });

        

    if (($("#curr_page").val()!="") && ($(".current_avat").attr("id")!=$("#curr_page").val()-1)){
        var max = parseInt($("#no_pages").val());
        var current = parseInt($("#curr_page").val());
        $(".current_avat").css("display","none").removeClass("current_avat");
        $("#pager").html("Page "+ current +" of "+max);
        $("#"+(current-1)).css("display","block").addClass("current_avat");
    }


    $("#next_avatar_page, #prev_avatar_page").click(function(){
        var next_av = parseInt($(".current_avat").attr("id"));
        var max = parseInt($("#no_pages").val());
        if ($(this).val()=="Next"){
            next_av++;
            if (next_av == max) next_av = 0;

        }
        else{
            next_av--;
            if (next_av == -1) next_av = 2;
        }
        var pager = next_av+1;
        $(".current_avat").css("display","none").removeClass("current_avat");
        $("#pager").html("Page "+pager+" of "+max);
        $("#"+next_av).css("display","block").addClass("current_avat");

    });


    $("#chat_active.chat_on #chat_list_header_title").live("click",function(){
        $("#chat_list").slideToggle("medium");
    });


    //Ajax Submit Comment
    $(".submit_comment").click(function(){
		
        var comment = $("#comment").val();
        var task_id = $("#task_id").val();
        var newcomm = $("#subnewcomment").val();
		
        var dataString = 'comment='+ comment + '&task_id=' + task_id + '&subnewcomment=' + newcomm;
        if(comment==''){
            alert('Δώστε κείμενο σχολίου!');
        }
        else{
            $("#flash").show();
            $("#flash").fadeIn(400).html('<img src="styling/images/bar.gif" />');
            $("#comment").attr('disabled','disabled').addClass('inactive');
            $.ajax({
                type: "POST",
                url: "process.php",
                data: dataString,
                cache: false,
                success: function(html){
                    if (html!=1 || html!=2){
                        $("ol#comments_list").prepend(html);
                    }
                    else $("ol#comments_list").prepend("<li>Sfalma</li>");
                    $("ol#update li:first").fadeIn("slow");
                    $("#flash").hide();
                    $("#comment").attr('disabled','').removeClass('inactive').val('');
                }
            });
        }
        return false;
    });
	
    $(".stsajax").live('click', function(){
        //$("#test_p").html("asdf");
        var task_num = $(this).parent().siblings("input[name='tsk']").val();
        var task_action = $(this).attr("name");
        var dataString = 'action=ajaxrequest&test_ajax=12&tsk='+task_num+'&tsk_action='+task_action;
        var this_btn = $(this);
        var btns_cell = $(this).parent();
        var task_row = $(this).parent().parent();
        var status_label = $(this).parent().siblings(".task_sts_label");
		
        $.ajax({
            type: "POST",
            url: "process.php",
            context: this_btn,
            data: dataString,
            cache: false,
            success: function(ret){
                status_label.html(ret);
            	
                if (ret == 'Awaiting Confirmation'){
                    btns_cell.html('<input class="upd stsajax" name="upd_sts" type="button" />');
                }
                else if (ret == 'Not Started' || ret == 'In Progress'){
                    btns_cell.html('<input class="down stsajax" name="down_sts" type="button" /><input class="upd stsajax" name="upd_sts" type="button" />');
                }
                else if (ret == 'Completed'){
                    task_row.slideUp("medium", function() {
                        $(this).remove();
                    } );
                }
            }
        });
    	

    });
	
    /*Project Info functions*/
    if ($(".properties_group").length>0){


        $(".properties_group input.group_header").live('keyup keydown blur update focus click', function(){
            $(this).autoGrowInput({
                comfortZone: 2,
                minWidth: 50,
                maxWidth: 500
            })
        });
            
        $(".properties_group input.property_label").live('keyup keydown blur update focus click', function(){
            $(this).autoGrowInput({
                comfortZone: 2,
                minWidth: 50,
                maxWidth: 150
            })
        }).attr("maxlength","20");

        $(".properties_group input.property_value").live('keyup keydown blur update focus click', function(){
            $(this).autoGrowInput({
                comfortZone: 2,
                minWidth: 50,
                maxWidth: 400
            })
        }).attr("maxlength","50");
            
        /*
            $(".properties_group input.group_header").autoGrowInput({
                 comfortZone: 2,
                 minWidth: 50,
                 maxWidth: 500
            })

            $(".properties_group input.property_label").autoGrowInput({
                comfortZone: 2,
                minWidth: 50,
                maxWidth: 150
            }).attr("maxlength","20");

            $(".properties_group input.property_value").autoGrowInput({
                comfortZone: 2,
                minWidth: 50,
                maxWidth: 400
            }).attr("maxlength","50");
            
            */
            
            
            

        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };
            
        $(".properties_table tbody").sortable({
            handle: '.handle_property', 
            items:'tr', 
            helper: fixHelper
        });

        //$(".property_icons").parent("tr").hover(function(){$(this).children(".property_icons").css("visibility","visible");});

        $(".properties_table tr").live("mouseenter", function(){
            $(".property_icons",this).css("display","block");
        }).live("mouseleave", function(){
            $(".property_icons",this).css("display","none");
        });

        $(".add_property").live("click", function(){
            $(".properties_table tbody").sortable({
                handle: '.handle_property', 
                items:'tr', 
                helper: fixHelper
            });
            	
            var count = $("input.property_label").length; // not the absolute correct way   ----    parseInt($("#properties_counter").val());
            count++;
            $("#properties_counter").val(count);
            	
            var new_row = "<tr class=\"property\"> \
                    <td style=\"text-align:right;\"><input name=\"property_name"+count+"\" maxlength=\"20\" style=\"text-align: right; width: 95px;\" value=\"Property Name\" class=\"property_label\" type=\"text\">:</td><td><input name=\"property_value"+count+"\" maxlength=\"50\" style=\"width: 94px;\" value=\"Property Value\" class=\"property_value\" type=\"text\"></td><td></td><td> \
                        <span style=\"display: none;\" class=\"property_icons\"> \
                        <img class=\"handle_property\" src=\"styling/images/move_property_16.png\"> \
                        <img class=\"delete_property\" src=\"styling/images/delete_property_16.png\"> \
                        <img class=\"add_property\" src=\"styling/images/add_property_16.png\"> \
                        <img class=\"add_seperator_property\" src=\"styling/images/seperator_16.png\"> \
                        </span> \
                    </td></tr>";
            $(this).parent().parent().parent().after(new_row);
        });
            
        $(".add_seperator_property").live("click", function(){
            var count = $("td.seperator").length;
            count++;
            $("#properties_counter").val(count);
    	        
            var new_row = "<tr><td class=\"seperator\" colspan=\"4\"> \
            			<span class=\"property_icons sep\"> \
                        <img class=\"delete_property sep\" src=\"styling/images/delete_property_16.png\" /> \
                        <img class=\"handle_property\" src=\"styling/images/move_property_16.png\" /> \
                        </span> \
                        <span class=\"property_icons sep_r\"> \
                        <img class=\"add_property\" src=\"styling/images/add_property_16.png\"> \
                        <input type=\"hidden\" name=\"seperator"+count+"\" /></span> \
            		</td></tr>";
            	
            $(this).parent().parent().parent().after(new_row);
        });


        $(".delete_property").live("click", function(){
            var property = $(this).parent().parent().parent();
            if (property.siblings(".property").length>0){
                property.slideToggle("medium", function() {
                    $(this).remove();
                });
            }
        });


            

           
    }


    $(".add_group_property").live("click", function(){
        var count = $("input.group_header").length;
        count++;
        $("#properties_counter").val(count);
        var property_count = $("input.property_label").length; // not the absolute correct way   ----
        property_count++;
            
        var new_group = "<div class=\"properties_group\" style=\"display:none\"> \
                    <input name=\"group_name"+count+"\" type=\"text\" class=\"group_header\" value=\"Group Name\" style=\"width:130px;\" /> \
                    <a href=\"#!\" class=\"add_group_property\"></a> \
                    <a href=\"#!\" class=\"del_group_property\"></a> \
                    <table class=\"properties_table\"> \
                    <col class=\"column1\" /> \
                    <col class=\"column2\" /> \
                    <col class=\"column3\" /> \
                    <col class=\"column4\" /> \
                    <tr class=\"property\"> \
                    <td style=\"text-align:right;\"><input name=\"property_name"+property_count+"\" maxlength=\"20\" style=\"text-align: right; width: 95px;\" value=\"Property Name\" class=\"property_label\" type=\"text\">:</td><td><input name=\"property_value"+property_count+"\" maxlength=\"50\" style=\"width: 94px;\" value=\"Property Value\" class=\"property_value\" type=\"text\"></td><td></td><td> \
                        <span style=\"display: none;\" class=\"property_icons\"> \
                        <img class=\"handle_property\" src=\"styling/images/move_property_16.png\"> \
                        <img class=\"delete_property\" src=\"styling/images/delete_property_16.png\"> \
                        <img class=\"add_property\" src=\"styling/images/add_property_16.png\"> \
                        <img class=\"add_seperator_property\" src=\"styling/images/seperator_16.png\"> \
                        </span> \
                    </td></tr></table></div>";


        $(this).parent().after(new_group).next().slideToggle('medium');
    });

    $(".del_group_property").live("click", function(){
        if ($(".properties_group").length>1){
            $(this).parent().slideToggle("medium", function() {
                $(this).remove();
            } );
        }
    });
		
		

    //AJAX Search
    $("#project").autocomplete("get_project_list.php", {
        width: 260,
        matchContains: true,
        selectFirst: false
    });
		
    $("#user").autocomplete("get_user_list.php", {
        width: 260,
        matchContains: true,
        selectFirst: false
    });
    
    //AJAX Timers
    $(".taskStartTiming").live('click', function(){
        //$("#test_p").html("asdf");
        var task_num = $(this).parent().siblings("input[name='tsk']").val();
        var dataString = 'action=ajaxrequest&task_id='+task_num+'&start_timing=1';
        var this_btn = $(this);
        var row = $(this).parent().parent();
        
        $.ajax({
            type: "POST",
            url: "process.php",
            context: this_btn,
            data: dataString,
            cache: false,
            success: function(ret){
                if (ret == "1"){
                    this_btn.children("img").attr("src","../styling/images/pause_task_timer.png");
                    this_btn.parent().removeClass("notstarted").addClass("started");
                    this_btn.removeClass("taskStartTiming").addClass("taskStopTiming");
                    row.children("td").animateHighlight("#dd0000", 1000);
                }else if (ret == "0"){
                    this_btn.append("error");
                }
            }
        });
    });
    $(".taskStopTiming").live('click', function(){
        //$("#test_p").html("asdf");
        var task_num = $(this).parent().siblings("input[name='tsk']").val();
        var dataString = 'action=ajaxrequest&task_id='+task_num+'&stop_timing=1';
        var this_btn = $(this);
        var row = $(this).parent().parent();
        
        $.ajax({
            type: "POST",
            url: "process.php",
            context: this_btn,
            data: dataString,
            cache: false,
            success: function(ret){
                if (ret == "3"){
                    this_btn.children("img").attr("src","../styling/images/play_task_timer.png");
                    this_btn.parent().removeClass("started").addClass("notstarted");
                    this_btn.removeClass("taskStopTiming").addClass("taskStartTiming");
                    row.children("td").animateHighlight("#dd0000", 1000);
                }
                else if (ret == "2"){
                    this_btn.append("done2");
                }else if (ret == "1"){
                    this_btn.append("done1");
                }else if (ret == "0"){
                    this_btn.append("error0");
                }
            }
        });
    });
    
    $(".tasks tr.active").each(function(index) {
        var tipid = "#" + $("div.tooltip", this).attr("id");
        
        $(this).tooltip({ position: "center left", opacity: 1, tip: tipid, effect: 'slide'});
    });
    


        

		

});

$.fn.animateHighlight = function(highlightColor, duration) {
    var highlightBg = highlightColor || "#FFFF9C";
    var animateMs = duration || 1500;
    var originalBg = this.css("background-color");
    this.stop().css("background-color", highlightBg).animate({backgroundColor: originalBg}, animateMs);
};
