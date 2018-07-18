<?php
/* contacts.php
 * Emergency contacts page
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */
 
date_default_timezone_set('Asia/Kuala_Lumpur');

//Scripts required
require_once("class/data.php");
require_once("class/template.php");
require_once("class/user.php");


//Template of the website
if(User::checkLoginStatus()) {
	$website = new WebsiteData(User::checkLoginStatus(), $_SESSION['fullName']);
} else {
	$website = new WebsiteData();
}

$pageHeader = new Header(WebsiteData::$websiteTitle, "About Us", "About Safety First", WebsiteData::$navItems);
$pageFooter = new Footer(WebsiteData::$websiteTitle, date('Y'));
$template = new Template($pageHeader, $pageFooter);


//Empty sting to concatenate the HTML output
$content = "";

//Content of the page
$content .= '<section class="mt-5 mb-5" id="content">';
	$content .= '<div class="container">';
		$content .= '<div class="row">';
			$content .= '<div class="col-lg-12">';
				$content .= '<h1>About Us</h1>';
				$content .= '<hr/>';
				$content .= '<p>We provide a platform where users can register themselves an account to share or post some important news or images that happened near them into the forum.</p>';  
				$content .= '<p>The feature allow the users to include the rate of severity from 1 to 10 to raise awareness of the citizens as well as to react on other people post. Most importantly, users can sort the posts in the timeline based on the date or the severity according to their preferences.</p>';
				$content .= '<p>The contents are entirely provided by the users and are not filtered. Therefore, please ensure the credibility of the information before spreading it.</p>';
			$content .= '</div>';
		$content .= '</div>';
	$content .= '</div>';
$content .= '</section>';

//Print out the entire webpage
echo $template->drawPage($content);
?>