<?php
	//Start session
	session_start();
	set_time_limit(0);
	//Check whether the session variable SESS_MEMBER_ID is present or not
	if(!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
		header("location: ../login/access-denied.php");
		exit();
	}
?>
