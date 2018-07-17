<?php
/* data.php
 * Contains the WebsiteData class that stores all the website data
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */
 
date_default_timezone_set('Asia/Kuala_Lumpur');

class WebsiteData {
	
	//Attribute required in the website
	public static $websiteTitle 	= "Safety First";
	public static $navItems 		= array();
	public static $databaseServer 	= "localhost";
	public static $databaseUsername = "root";
	public static $databasePassword = "";
	public static $databaseName 	= "safety_first";
	public static $newsFeedSorting = array(
		array(
			'sortBy' => 'date',
			'sortOrder' => 'desc',
			'value' => 'Date (Descending)'
		),
		array(
			'sortBy' => 'severity',
			'sortOrder' => 'desc',
			'value' => 'Severity (Descending)'
		),
		array(
			'sortBy' => 'date',
			'sortOrder' => 'asc',
			'value' => 'Date (Ascending)'
		),
		array(
			'sortBy' => 'severity',
			'sortOrder' => 'asc',
			'value' => 'Severity (Ascending)'
		)
	);
	
	//Constructor to constructor the nav items based on the login status
	public function __construct($login = false, $user = false) {
		if($login && $user) {
			self::$navItems = array(
				array(
					'href' => '#',
					'value' => 'Logged in as '. $user .''
				),
				array(
					'href' 	=> 'index.php',
					'value' => 'Home'
				),
				array(
					'href' => 'addPost.php',
					'value' => 'Add Post'
				),
				array(
					'href' => 'logout.php',
					'value' => 'Logout'
				)
			);
		} else {
			self::$navItems = array(
				array(
					'href' 	=> 'index.php',
					'value' => 'Home'
				),
				array(
					'href' 	=> 'login.php',
					'value' => 'Login'
				),
				array(
					'href' => 'register.php',
					'value' => 'Register'
				)
			);
		}
	}
}
?>