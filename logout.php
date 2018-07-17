<?php
/* logout.php
 * Let the user logout from the system
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */
 
date_default_timezone_set('Asia/Kuala_Lumpur');

//Check if there exists session establishment, establish the session if not
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Scripts required
require_once("class/user.php");
require_once("class/notification.php");

//Check if the user is logged in
if(User::checkLoginStatus()) {
	
	//Redirect the user back to the home page if logged out successfully
	if(User::logout()) {
		$notification = new Notification('You have logged out successfully', 'success');
		$notification->redirectWithMsg('index.php');
	} else {
		
		//Failed to log the user out, redirect the user to the home page
		$notification = new Notification('Failed to log out, please try again', 'danger');
		$notification->redirectWithMsg('index.php');
	}
} else {
	
	//The user if not logged in, redirect the user to the home page
	$notification = new Notification('You are not logged in', 'warning');
	$notification->redirectWithMsg('index.php');
}
?>