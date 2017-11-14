<?php
//	Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.
global $link, $linki, $title;
if (!isset($_SESSION['badgeid'])) {
	$logging_in = true;
	require_once ('error_functions.php');
	require_once ('CommonCode.php');
	//    require_once ('db_functions.php');
	//    require_once ('data_functions.php');
	//    require_once ('php_functions.php');
	$title = "Submit Password";
	// echo "Trying to connect to database.\n";
	if (prepare_db() === false) {
		$message_error = "Unable to connect to database.<br />No further execution possible.";
		RenderError($title, $message_error);
		exit();
	}
	$badgeid = mysqli_real_escape_string($linki, $_POST['badgeid']);
	$password = stripslashes($_POST['passwd']);
	$result = mysqli_query_with_error_handling("Select password from Participants where badgeid='$badgeid';");
	if (!$result) {
		$message="Incorrect badgeid or password.";
		require ('login.php');
		exit();
	}
	$dbobject = mysqli_fetch_object($result);
	$result -> close();
	$dbpassword = $dbobject -> password;
	//echo $badgeid."<BR>".$dbpassword."<BR>".$password."<BR>".md5($password);
	//exit(0);
	if (md5($password) !== $dbpassword) {
		$message = "Incorrect badgeid or password.";
		require ('login.php');
		exit(0);
	}
	$result = mysqli_query_with_error_handling("Select badgename from CongoDump where badgeid='$badgeid';");
	if ($result) {
		$dbobject = mysqli_fetch_object($result);
		$badgename = $dbobject -> badgename;
		$_SESSION['badgename'] = $badgename;
		$result -> close();
	} else {
		$_SESSION['badgename']="";
	}
	$pubsname = "";
	$result = mysqli_query_with_error_handling("Select pubsname from Participants where badgeid='$badgeid';");
	if ($result) {
        $dbobject = mysqli_fetch_object($result);
		$pubsname = $dbobject -> pubsname;
        $result -> close();
    }
	if (!($pubsname == "")) {
		$_SESSION['badgename'] = $pubsname;
	}
	$_SESSION['badgeid'] = $badgeid;
	$_SESSION['password'] = $dbpassword;
	set_permission_set($badgeid);
	//error_log("Zambia: Completed set_permission_set.\n");
} else {
	$badgeid = $_SESSION['badgeid'];
}
$message2="";
if (retrieve_participant_from_db($badgeid) === 0) {
	if (may_I('Staff')) {
		require ('StaffPage.php');
	} elseif (may_I('Participant')) {
		require ('renderWelcome.php');
	} elseif (may_I('public_login')) {
		require ('renderBrainstormWelcome.php');
	} else {
		$message_error="There is a problem with your userid's permission configuration:  It doesn't have ";
		$message_error.="permission to access any welcome page.  Please contact Zambia staff.";
		RenderError($title, $message_error);
	}
	exit();
}
$message_error = $message2."<br />Error retrieving data from DB.  No further execution possible.";
RenderError($title, $message_error);
exit();
?>
