<?php
//	Copyright (c) 2011-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $link, $linki, $title;
if (!isset($_SESSION['badgeid'])) {
    $logging_in = true;
    require_once('error_functions.php');
    require_once('CommonCode.php');
    //    require_once ('db_functions.php');
    //    require_once ('data_functions.php');
    //    require_once ('php_functions.php');
    $title = "Submit Password";
    // echo "Trying to connect to database.\n";
    if (prepare_db() === false) {
        $message_error = "Unable to connect to database.<br />No further execution possible.";
        RenderError($message_error);
        exit();
    }
    $badgeid = mysqli_real_escape_string($linki, $_POST['badgeid']);
    $password = stripslashes($_POST['passwd']);
    $query = "Select password from Participants where badgeid='$badgeid';";
    if (!$result = mysqli_query_exit_on_error($query)) {
        exit(); // Should have exited already
    }
    $dbobject = mysqli_fetch_object($result);
    mysqli_free_result($result);
    $dbpassword = $dbobject->password;
    if (md5($password) !== $dbpassword) {
        $message = "Incorrect badgeid or password.";
        require('login.php');
        exit(0);
    }
    $query = "Select badgename from CongoDump where badgeid='$badgeid';";
    if (!$result = mysqli_query_exit_on_error($query)) {
        exit(); // Should have exited already
    }
    $dbobject = mysqli_fetch_object($result);
    $badgename = $dbobject->badgename;
    $_SESSION['badgename'] = $badgename;
    mysqli_free_result($result);
    $pubsname = "";
    $query = "Select pubsname from Participants where badgeid='$badgeid';";
    if (!$result = mysqli_query_exit_on_error($query)) {
        exit(); // Should have exited already
    }
    $dbobject = mysqli_fetch_object($result);
    $pubsname = $dbobject->pubsname;
    mysqli_free_result($result);
    if (!($pubsname == "")) {
        $_SESSION['badgename'] = $pubsname;
    }
    $_SESSION['badgeid'] = $badgeid;
    $_SESSION['password'] = $dbpassword;
    set_permission_set($badgeid);
} else {
    $badgeid = $_SESSION['badgeid'];
}
$message2 = "";
if ($participant_array = retrieveParticipant($badgeid)) {
    if (may_I('Staff')) {
        require('StaffPage.php');
    } elseif (may_I('Participant')) {
        require('renderWelcome.php');
    } elseif (may_I('public_login')) {
        require('renderBrainstormWelcome.php');
    } else {
        $message_error = "There is a problem with your userid's permission configuration:  It doesn't have ";
        $message_error .= "permission to access any welcome page.  Please contact Zambia staff.";
        RenderError($message_error);
    }
    exit();
}
$message_error = $message2 . "<br />Error retrieving data from DB.  No further execution possible.";
RenderError($message_error);
exit();
?>
