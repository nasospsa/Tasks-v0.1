/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready( function(){




    //Create Note
    $("#new_note_btn").click( function(){
        if ($("#notes_container > .note_div").size()<3){
            $("#notes_container").prepend("<div style=\"display:none\" class=\"note_div\"><textarea rows=\"4\"></textarea><a href=\"#!\" class=\"del_note\"></a><a href=\"#!\" title=\"Move Note\" class=\"move_note\"></a></div>");
            $("#notes_container div:first-child").slideDown("normal");
        }
        else{
            alert("No more than 3 Notes!");
        }
       
    });

    //Update Note
    $(".note_div textarea").live('blur', function(){
        if ($(this).val() != ""){
            

            var note_text = $(this).val();
            var note_id = $(this).parent().attr("id").substr(5);
            var dataString ="text="+note_text+"&id="+note_id;

            $.ajax({
                type: "POST",
                url: "include/notes-actions.php",
                data: dataString,
                context: $(this).parent(),
                cache: false,
                success: function(html){
                    $(this).attr("id","note-"+html);
                    $("#note_update").css("display","inline").fadeOut("slow");
                }
            });
        }


    }).keydown( function(){
       if ($(this).val().length > 90){
           $(this).val($(this).val().substr(0, 90));
       }
    });
    

    //Delete Note
    $(".del_note").live('click', function(){
       var note_id = $(this).parent().attr("id").substr(5);
       if (note_id!=''){
           
           

           var dataString ="action=delete&id="+note_id;
           
           
           $.ajax({
                type: "POST",
                url: "include/notes-actions.php",
                data: dataString,
                cache: false,
                success: function(){
                }
            });
       }

       $(this).parent().slideUp("normal", function() { $(this).remove(); } );
    });

    

    $( "#notes_container" ).sortable({ handle: '.move_note' });

});