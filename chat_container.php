<div id="chat_container">
<!--[if lte IE 7]>
<link type="text/css" rel="stylesheet" media="all" href="css/screen_ie.css" />
<![endif]-->
<link type="text/css" rel="stylesheet" media="all" href="styling/chat.css" />
<script type="text/javascript" src="js/chat.js"></script>
<?php
include 'include/view_active.php';
?>
<div id="chat_active" <? if ($active_users_count>0) echo "class=\"chat_on\""; ?>>
    <div id="chat_list_header">
        <span id="chat_list_header_title">Chat (<? echo $active_users_count ?>)</span>
    </div>
    <div id="chat_list">
        <ul>
        <?
        foreach ($active_users as $chat_user){?>
            <li><a href="javascript:void(0)" onClick="javascript:chatWith(<? echo "'".$chat_user."'" ?>)"><? echo $chat_user ?></a></li>
        <?
        }
        ?>
        </ul>
    </div>
</div>
</div>