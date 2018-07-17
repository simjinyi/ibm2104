<?php
/* ajaxHandler.php
 * Calls the required functions based on the AJAX request made
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */
 
date_default_timezone_set('Asia/Kuala_Lumpur');

//Check if there exists session establishment, establish the session if not
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Scripts required
require_once("class/post.php");
require_once("class/user.php");

//Check if the home page is requesting for the news feed contents
if(isset($_POST['getNewsFeed'])) {
	
	//Sorting information
	$sortData = $_POST['sorting'];
	
	//Current page number
	$pageNo = $_POST['pageNo'];
	
	//Number of posts per page
	$postsPerPage = $_POST['postsPerPage'];
	
	//Check if the user wants to filter the posts based on the location
	$locationFilter = (isset($_POST['locationFilter']) ? $_POST['locationFilter'] : NULL);
	
	//Check if the user wants to view only his/her posts
	$postFilter = $_POST['postFilter'];
	
	//Set the cookies based on the user preference accordingly
	setcookie('sortBy', $sortData['sortBy']);
	setcookie('sortOrder', $sortData['sortOrder']);
	setcookie('postsPerPage', $postsPerPage);
	
	//Check the user login status
	$loginStatus = User::checkLoginStatus();
	
	//Call the function based on the user login status
	if($loginStatus) {
		echo Post::getNewsFeed($loginStatus, $sortData, $pageNo, $postsPerPage, $locationFilter, $postFilter, $_SESSION['id']);
	} else {
		echo Post::getNewsFeed($loginStatus, $sortData, $pageNo, $postsPerPage, $locationFilter, $postFilter);
	}
}

//Check if the user is reacting to the posts
if(isset($_POST['reaction'])) {
	
	//Call the reactPost function with the appropriate variables passed
	Post::reactPost($_POST['reaction'], $_POST['post_id'], $_SESSION['id']);
}

//Check if the user is deleting the posts
if(isset($_POST['deletePost'])) {
	
	//Call the deletePost function with the appropriate variables passed
	Post::deletePost($_POST['postID'], $_SESSION['id']);
}
?>