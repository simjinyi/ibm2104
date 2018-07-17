<?php
/* index.php
 * Prints the news feed with the control panel
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */
 
date_default_timezone_set('Asia/Kuala_Lumpur');

//Check if there exists session establishment, establish the session if not
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Check if there exists the cookies that store the user sorting preference
if(!isset($_COOKIE['sortBy']) && !isset($_COOKIE['sortOrder'])) {
	
	//Create them if not wth the default value
	setcookie('sortBy', 'date');
	setcookie('sortOrder', 'desc');
	
	$cookieSortBy = 'date';
	$cookieSortOrder = 'desc';
	
} else {
	
	$cookieSortBy = $_COOKIE['sortBy'];
	$cookieSortOrder = $_COOKIE['sortOrder'];	
	
}

//Check if there exists the cookies that store the user sorting preference
if(!isset($_COOKIE['postsPerPage'])) {
	
	//Create one if not with the default value
	setcookie('postsPerPage', 5);
	
	$cookiePostsPerPage = 5;
	
} else {
	
	$cookiePostsPerPage = $_COOKIE['postsPerPage'];
	
}

//Scripts required
require_once("class/data.php");
require_once("class/template.php");
require_once("class/user.php");
require_once("class/notification.php");


//Template of the website
if(User::checkLoginStatus()) {
	$website = new WebsiteData(User::checkLoginStatus(), $_SESSION['fullName']);
} else {
	$website = new WebsiteData();
}

$pageHeader = new Header(WebsiteData::$websiteTitle, "Home", "Welcome to Safety First", WebsiteData::$navItems);
$pageFooter = new Footer(WebsiteData::$websiteTitle, date('Y'));
$template = new Template($pageHeader, $pageFooter);


//Empty sting to concatenate the HTML output
$content = "";

//Message section
if(isset($_GET['msg']) && isset($_GET['status'])) {
	$notification = new Notification($_GET['msg'], $_GET['status']);
	$content .= $notification->getMessage($notification);
}

//Content of the page
$content .= '<section class="mt-5 mb-5" id="content">';
	$content .= '<div class="container">';
		$content .= '<div class="page-header">';
			$content .= '<h1>Timeline</h1>';
		$content .= '</div>';
		$content .= '<hr/>';
		
		//Collapsible control panel
		$content .= '<div class="collapse" id="collapseSettings">';
		
			//Sorting methods selections
			$content .= '<h3>Sorting:</h3>';
			$content .= '<form onsubmit="return false">';
				$content .= '<div class="form-group">';
					$content .= '<label for="sort">Sort by:</label>';
					$content .= '<select class="form-control" id="sort" name="sort" onchange="changeSort()">';
						foreach(WebsiteData::$newsFeedSorting as $sorting) {
							if(($sorting['sortBy'] == $cookieSortBy) && ($sorting['sortOrder'] == $cookieSortOrder)) {
								$content .= '<option value="'. $sorting['sortBy'] .','. $sorting['sortOrder'] .'" selected="selected">'. $sorting['value'] .'</option>';
							} else {
								$content .= '<option value="'. $sorting['sortBy'] .','. $sorting['sortOrder'] .'">'. $sorting['value'] .'</option>';
							}
						}
					$content .= '</select>';
				$content .= '</div>';
			$content .= '</form>';
			$content .= '<br/>';
			
			//Posts per page selections
			$content .= '<h3>Posts Per Page:</h3>';
			$content .= '<form onsubmit="return false">';
				$content .= '<div class="form-group">';
					$content .= '<label for="postsPerPage">Posts Per Page:</label>';
					$content .= '<select class="form-control" id="postsPerPage" name="postsPerPage" onchange="changePostsPerPage()">';
						for($i = 5; $i <= 100; $i += 5) {
							if($cookiePostsPerPage == $i) {
								$content .= '<option value="'. $i .'" selected="selected">'. $i .'</option>';
							} else {
								$content .= '<option value="'. $i .'">'. $i .'</option>';
							}
						}
					$content .= '</select>';
				$content .= '</div>';
			$content .= '</form>';
			$content .= '<br/>';
			
			//Filter location selections
			$content .= '<h3>Filter Location:</h3>';
			$content .= '<form onsubmit="return false">';
				$content .= '<div class="row">';
					$content .= '<div class="col-lg-3">';
					
						//Country input
						$content .= '<div class="form-group">';
							$content .= '<label for="country">Country:</label>';
							$content .= '<input class="form-control" type="text" name="country" id="country" placeholder="Country" onchange="updateLocationFilter()" value="'. (User::checkLoginStatus() ? $_SESSION['country'] : "") .'"/>';
						$content .= '</div>';
					$content .= '</div>';
					$content .= '<div class="col-lg-3">';
					
						//State input
						$content .= '<div class="form-group">';
							$content .= '<label for="state">State:</label>';
							$content .= '<input class="form-control" type="text" name="state" id="state" placeholder="State" onchange="updateLocationFilter()" value="'. (User::checkLoginStatus() ? $_SESSION['state'] : "") .'"/>';
						$content .= '</div>';
					$content .= '</div>';
					$content .= '<div class="col-lg-3">';
					
						//City input
						$content .= '<div class="form-group">';
							$content .= '<label for="city">City:</label>';
							$content .= '<input class="form-control" type="text" name="city" id="city" placeholder="City" onchange="updateLocationFilter()" value="'. (User::checkLoginStatus() ? $_SESSION['city'] : "") .'"/>';
						$content .= '</div>';
					$content .= '</div>';
					$content .= '<div class="col-lg-3">';
					
						//Filter location enable or disable
						$content .= '<div class="form-group">';
							$content .= '<label for="searchLocation">Filter:</label><br/>';
							$content .= '<div id="locationButton">';
								$content .= '<button class="btn btn-danger" id="searchLocation" value="false" onclick="locationFilter();">Disabled, Showing All Posts</button>';
							$content .= '</div>';
						$content .= '</div>';
					$content .= '</div>';
				$content .= '</div>';
			$content .= '</form>';	
			
			//Check if the user is logged in
			if(User::checkLoginStatus()) {
				
				$content .= '<br/>';
				
				//View only the posts by the user selections
				$content .= '<h3>Filter Posts:</h3>';
				$content .= '<form onsubmit="return false">';
				
					//Filter only user's post enable or disable
					$content .= '<div class="form-group">';
						$content .= '<label for="showMyPosts">Show Only My Posts:</label><br/>';
						$content .= '<div id="showMyPostsButton">';
							$content .= '<button class="btn btn-danger" id="showMyPosts" value="false" onclick="showMyPostsFilter();">Disabled, Showing All Posts</button>';
						$content .= '</div>';
					$content .= '</div>';
				$content .= '</form>';	
			}
		$content .= '</div>';
		
		//Collapse button
		$content .= '<div id="collapseSettingsButton">';
			$content .= '<button onclick="collapseSettings(true);" class="btn btn-outline-primary" type="button" data-toggle="collapse" data-target="#collapseSettings" aria-expanded="false" aria-controls="collapseSettings">View Settings</button>';
		$content .= '</div>';
  		$content .= '<hr/>';
		
		//AJAX returned value fill in the div below
		$content .= '<div id="newsFeed">';
		$content .= '</div>';
		$content .= '<hr/>';
		$content .= '<p>Content refreshing in: <span id="refresh">60</span>s';
	$content .= '</div>';
$content .= '</section>';

//Print out the entire webpage
echo $template->drawPage($content);
?>

<script type="text/javascript">

//Page number for the pagination
var page = 1;

//Post filter variables
var filterByLocation = false;
var filterMyPosts = false;

//Location to be searched
var filterLocation = [];

//When the document is loaded, update the news feed content
$(document).ready(function() {
	
	updateNewsFeedContents($('#sort').val(), $('#postsPerPage').val());
	
	//Content refresh counter
	var counter = 60;
	
	//Update the countdown for the content refresh counter in the home page every second
	//Refresh the news feed content every 60 seconds
	setInterval(function() { 
		
		$('#refresh').html(--counter);
		
		//Reset the counter to 60 every refresh cycle
		if(counter <= 0) {
			
			updateNewsFeedContents($('#sort').val(), $('#postsPerPage').val())
			counter = 60;
		}
		 
	}, 1000);
});

//Update the news feed contents
function updateNewsFeedContents(sortBy, itemsPerPage) {
	
	//Sorting method information from the select field in the page
	var sortData = sortBy.split(',');
	var sortBy = sortData[0];
	var sortOrder = sortData[1];
	
	//Check if the user wants to filter the post based on the location
	if(filterByLocation) {
		
		//Make the AJAX request with the values posted
		$.ajax({
			url: 'ajaxHandler.php',
			type: 'POST',
			data: { 
				pageNo: page,
				postsPerPage: itemsPerPage,
				getNewsFeed:true,
				locationFilter: filterLocation,
				postFilter: filterMyPosts,
				sorting: {
					sortBy: sortBy,
					sortOrder: sortOrder
				}
			},
			cache: false,
			success: function(result) {		
				
				//Update the news feed into the HTML page		
				$('#newsFeed').html(result);
			}
		});
	} else {
		
		//Make the AJAX request with the values posted without location filter
		$.ajax({
			url: 'ajaxHandler.php',
			type: 'POST',
			data: { 
				pageNo: page,
				postsPerPage: itemsPerPage,
				getNewsFeed:true,
				postFilter: filterMyPosts,
				sorting: {
					sortBy: sortBy,
					sortOrder: sortOrder
				}
			},
			cache: false,
			success: function(result) {	
				
				//Update the news feed into the HTML page			
				$('#newsFeed').html(result);
			}
		});
	}
}

//Function to change the collapse button when clicked
function collapseSettings(collapsed) {
	if(collapsed) {
		$('#collapseSettingsButton').html('<button onclick="collapseSettings(false);" class="btn btn-outline-primary" type="button" data-toggle="collapse" data-target="#collapseSettings" aria-expanded="false" aria-controls="collapseSettings">Collapse Settings</button>');
	} else {
		$('#collapseSettingsButton').html('<button onclick="collapseSettings(true);" class="btn btn-outline-primary" type="button" data-toggle="collapse" data-target="#collapseSettings" aria-expanded="false" aria-controls="collapseSettings">View Settings</button>');
	}
}

//Function to filter only the user's post
function showMyPostsFilter() {
	
	if($('#showMyPosts').val() == "true") {
		
		//Change the button text
		$('#showMyPostsButton').html('<button class="btn btn-danger" id="showMyPosts" value="false" onclick="showMyPostsFilter();">Disabled, Showing All Posts</button>');
		filterMyPosts = false;
				
	} else {
		
		//Change the button text
		$('#showMyPostsButton').html('<button class="btn btn-success" id="showMyPosts" value="true" onclick="showMyPostsFilter();">Enabled, Showing My Posts</button>');
		filterMyPosts = true;
	}
	
	//Update the news feed contents
	updateNewsFeedContents($('#sort').val(), $('#postsPerPage').val());
}

//Filter the location of the posts
function locationFilter() {
	
	if($('#searchLocation').val() == "true") {
		
		//Change the button text
		$('#locationButton').html('<button class="btn btn-danger" id="searchLocation" value="false" onclick="locationFilter();">Disabled, Showing All Posts</button>');
		filterByLocation = false;
		
		filterLocation = [];	
				
	} else {
		
		//Change the button text
		$('#locationButton').html('<button class="btn btn-success" id="searchLocation" value="true" onclick="locationFilter();">Enabled, Showing Filtered Post</button>');
		filterByLocation = true;
		
		//Populate the array based on the search values provided by the user
		var cityField = $('#city').val();
		var stateField = $('#state').val();
		var countryField = $('#country').val();
		
		filterLocation = [countryField, stateField, cityField];	
	}
	
	//Update the news feed contents
	updateNewsFeedContents($('#sort').val(), $('#postsPerPage').val());
}

//Update the value of the location to be searched dynamically when the user enters into the city, state or country fields
function updateLocationFilter() {
	
	//Get the values from the text box
	var cityField = $('#city').val();
	var stateField = $('#state').val();
	var countryField = $('#country').val();
	
	//Check if the location filter is enabled
	if(filterByLocation) {
		filterLocation = [countryField, stateField, cityField];	
	} else {
		filterLocation = [];
	}
	
	//Update the news feed contents
	updateNewsFeedContents($('#sort').val(), $('#postsPerPage').val());
}

//Change the posts per page
function changePostsPerPage() {
	
	//Update the news feed contents
	updateNewsFeedContents($('#sort').val(), $('#postsPerPage').val());
}

//React to the posts
function reactPost(postID, postReaction) {
	
	//Make the AJAX request to react to the posts
	$.ajax({
		url: 'ajaxHandler.php',
		type: 'POST',
		data: { 
			reaction: postReaction,
			post_id: postID
		},
		cache: false,
		success: function() {
			
			//Update the news feed content				
			updateNewsFeedContents($('#sort').val(), $('#postsPerPage').val());
		}
	});
}

//Change the sorting method
function changeSort() {
	
	//Update the news feed content
	updateNewsFeedContents($('#sort').val(), $('#postsPerPage').val());
}

//Provide confirmation before deleting the post
function deletePostConfirmation(postID) {
	
	//Change the button to confirm if the user wants to delete the post
	$('#deletePost_' + postID + '').html('<button class="btn btn-danger" title="Click Again to Delete" onclick="deletePost(' + postID + ');" id="delete_' + postID + '">Delete?</button>');
	
	//If the user does not react in 5 seconds, change the button back to the original button
	setTimeout(function() {
		$('#deletePost_' + postID + '').html('<button class="btn btn-outline-danger" title="Click Twice to Delete" onclick="deletePostConfirmation(' + postID + ');" id="delete_confirmation_' + postID + '">Delete</button>');
	}, 5000);
}

//Delete the post
function deletePost(id) {
	
	//Make AJAX request with the required parameter posted to delete the post
	$.ajax({
		url: 'ajaxHandler.php',
		type: 'POST',
		data: { 
			deletePost:true,
			postID: id
		},
		cache: false,
		success: function(result) {	
		
			//Update the news feed contents			
			updateNewsFeedContents($('#sort').val(), $('#postsPerPage').val());
		}
	});
}

//Function to navigate to the next page
function nextPage() {
	
	//Increment the page number by 1
	page++;
	
	//Update the news feed contents
	updateNewsFeedContents($('#sort').val(), $('#postsPerPage').val());
}

function previousPage() {
	
	//Decrement the page number by 1
	page--;
	
	//Update the news feed contents
	updateNewsFeedContents($('#sort').val(), $('#postsPerPage').val());
}
</script>