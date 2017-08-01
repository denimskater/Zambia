<?php
//	Copyright (c) 2011-2017 The Zambia Group. All rights reserved. See copyright document for more details.
    global $participant, $message_error, $message2, $congoinfo;
    $title = "Welcome";
    require ('PartCommonCode.php');
    if (retrieve_participant_from_db($badgeid) === 0) {
        require ('renderWelcome.php');
        exit();
    }
    $message_error=$message2."<br />Error retrieving data from DB.  No further execution possible.";
    RenderError($title, $message_error);
    exit();
?>
