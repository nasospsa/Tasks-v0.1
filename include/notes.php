<?php
$q = "SELECT * FROM notes WHERE user='" . $session->username . "'";
$result = $database->query($q);
?>

<div id="notes_wrapper">
    <script type="text/javascript" src="js/notes.js"></script>
    <link type="text/css" rel="stylesheet" media="all" href="styling/notes.css" />
    <input type="button" value="New Note" id="new_note_btn" />
    <span id="note_update">Saved!</span>

    <div id="notes_container">
        <? while ($note = mysql_fetch_assoc($result)) {
 ?>
            <div id="note-<? echo $note["id"] ?>" class="note_div">
                <textarea rows="4"><? echo $note["note"] ?></textarea><a href="#!" title="Delete Note" class="del_note"></a><a href="#!" title="Move Note" class="move_note"></a>
            </div>
        <?
        }
        ?>
    </div>
</div>
