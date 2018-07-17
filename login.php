<?php
/* login.php
 * Let the user login to the system
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */

date_default_timezone_set('Asia/Kuala_Lumpur');

//Check if there exists session establishment, establish the session if not
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Scripts required
require_once("class/template.php");
require_once("class/user.php");
require_once("class/notification.php");
require_once("class/data.php");

//Check if the user is logged in
if(User::checkLoginStatus()) {
	$notification = new Notification('You have already logged in', 'warning');
	$notification->redirectWithMsg('index.php');
}

//Template of the website
if(User::checkLoginStatus()) {
	$website = new WebsiteData(User::checkLoginStatus(), $_SESSION['fullName']);
} else {
	$website = new WebsiteData();
}

$pageHeader = new Header(WebsiteData::$websiteTitle, "Login", "Login to Your Account To Access More Features", WebsiteData::$navItems);
$pageFooter = new Footer(WebsiteData::$websiteTitle, date('Y'));
$template = new Template($pageHeader, $pageFooter);

//Empty sting to concatenate the HTML output
$content = "";

//Check if the login form is submitted
if(isset($_POST['submit'])) {
	
	//Get the credentials from the user
	$email 	  = htmlentities($_POST['email']);
	$password = htmlentities($_POST['password']);
	
	//If the user is logged into the system successfully, redirect him/her to the home page
	if(User::login($email, $password)) {
		$notification = new Notification('You Have Logged In Successfully', 'success');
		$notification->redirectWithMsg('index.php');
	} else {
		
		//Prompt error message
		$notification = new Notification('Invalid Email Address or Password, Please Try Again', 'danger');
		$notification->redirectWithMsg($_SERVER['PHP_SELF']);
	}
	
} else {
	
	//Content of the page
	$content .= '<section class="mt-5 mb-5" id="content">';
		$content .= '<div class="container">';
			$content .= '<div class="row">';
				$content .= '<div class="col-lg-12">';
					$content .= '<div class="page-header">';
						$content .= '<h1>Login</h1>';
						$content .= '<hr/>';
					$content .= '</div>';
					$content .= '<form action="'. $_SERVER['PHP_SELF'] .'" method="POST">';	
					
						//Email input
						$content .= '<div class="form-group">';
							$content .= '<label class="form-control-label" for="email">Email Address:</label>';
							$content .= '<input class="form-control" type="text" name="email" id="email" placeholder="Email Address" required="required"/>';
						$content .= '</div>';
						
						//Password input
						$content .= '<div class="form-group">';
							$content .= '<label class="form-control-label" for="password">Password:</label>';
							$content .= '<input class="form-control" type="password" name="password" id="password" placeholder="Password" required="required"/>';
						$content .= '</div>';
						
						//Submit button
						$content .= '<div class="form-group">';
							$content .= '<input class="btn btn-primary" type="submit" name="submit" value="Login"/>';
						$content .= '</div>';
					$content .= '</form>';			
				$content .= '</div>';
			$content .= '</div>';	
		$content .= '</div>';
	$content .= '</section>';
	
	//Message section
	if(isset($_GET['msg']) && isset($_GET['status'])) {
		$notification = new Notification($_GET['msg'], $_GET['status']);
		$content .= $notification->getMessage();
	}
}

//Print out the entire webpage
echo $template->drawPage($content);
?>