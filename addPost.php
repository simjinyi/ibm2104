<?php
/* addPost.php
 * Let the user register to the system
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */
 
date_default_timezone_set('Asia/Kuala_Lumpur');

//Check if there exists session establishment, establish the session if not
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Scripts required
require_once("class/data.php");
require_once("class/template.php");
require_once("class/user.php");
require_once("class/post.php");
require_once("class/location.php");
require_once("class/notification.php");

//Check if the user is logged in
if(!User::checkLoginStatus()) {	

	//Redirect the user back to the home page if he/she is not logged in
	$notification = new Notification('Please login to add post', 'danger');
	$notification->redirectWithMsg('login.php');
}

//Template of the website
if(User::checkLoginStatus()) {
	$website = new WebsiteData(User::checkLoginStatus(), $_SESSION['fullName']);
} else {
	$website = new WebsiteData();
}

$pageHeader = new Header(WebsiteData::$websiteTitle, "Add Post", "Write Post", WebsiteData::$navItems);
$pageFooter = new Footer(WebsiteData::$websiteTitle, date('Y'));
$template = new Template($pageHeader, $pageFooter);

//Empty sting to concatenate the HTML output
$content = "";

if(isset($_POST['submit'])) {
	
	//Check if all the fields are filled. If not, redirect the user back to this page with the error message passed
	if(empty($_POST['title']) || empty($_POST['content']) || empty($_POST['severity']) || empty($_POST['country']) || empty($_POST['state']) || empty($_POST['city'])) {
		$notification = new Notification('Please ensure that all the fields are filled before adding the post', 'danger');
		$notification->redirectWithMsg($_SERVER['PHP_SELF']);
	} else {
		
		//If all the fields are filled, assign the values from the "POST" and "FILES" array to the variables accordingly
		$title 	  = ucwords($_POST['title']);
		$content  = nl2br($_POST['content']);
		$severity = $_POST['severity'];
		$country  = $_POST['country'];
		$state 	  = $_POST['state'];
		$city 	  = $_POST['city'];
		$image 	  = $_FILES['image']; //Optional field
		
		//Variables involved in error handling
		$errorMessage = "";
		$error = false;
		
		//Validate if the title is less than 6 characters
		if(strlen($title) < 6) {
			$error = true;
			$errorMessage .= "Please ensure that the title is more than 6 characters<br/>";
		}
		
		//Validate if the content is less than 6 characters
		if(strlen($content) < 6) {
			$error = true;
			$errorMessage .= "Please ensure that the content is more than 6 characters<br/>";
		}
		
		//Validate if the country is less than 6 characters
		if(strlen($content) < 6) {
			$error = true;
			$errorMessage .= "Please ensure that the country is more than 6 characters<br/>";
		}
		
		//Validate if the state is less than 6 characters
		if(strlen($content) < 6) {
			$error = true;
			$errorMessage .= "Please ensure that the state is more than 6 characters<br/>";
		}
		
		//Validate if the city is less than 6 characters
		if(strlen($content) < 6) {
			$error = true;
			$errorMessage .= "Please ensure that the city is more than 6 characters<br/>";
		}
		
		//Check if the user uploads an image
		if(!empty($image['name'])) {
			
			$allowedExtensions = array('jpg', 'png', 'gif');
			$ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
			
			//Check for the extension to ensure that the file is an image
			if(!in_array($ext, $allowedExtensions)) {
				
				//Prompt the error message if invalid image extension encountered
				$errorMessage .= "Only jpg, png or gif is allowed for the image";
			}
			
				
			//Check if the file size exceeds the limit
			if($image['size'] > 1000000) {
			
				//Prompt the error message if file size exceeds the limit
				$errorMessage .= "Maximum allowed file size is 1MB";
			}
		}
		
		if($error) {
			
			//Prompt error message
			$notification = new Notification($errorMessage, 'danger');
			$notification->redirectWithMsg($_SERVER['PHP_SELF']);
			
		} else {
		
			//Create Location and Post objects
			$postLocation = new Location($country, $state, $city);
			$postData = new Post($title, $content, $severity, $postLocation, $image);
			
			//Check if the post is added successfully
			if($postData->addPost($_SESSION['id'])) {
				
				//Redirect the user back to the home page
				$notification = new Notification('Post successfully added', 'success');
				$notification->redirectWithMsg('index.php');
			} else {			
				
				//Prompt error message
				$notification = new Notification('Failed to add the post', 'danger');
				$notification->redirectWithMsg($_SERVER['PHP_SELF']);
			}
		}
	}
	
} else {
	
	//Content of the page
	$content .= '<section class="mt-5 mb-5" id="content">';
		$content .= '<div class="container">';
			$content .= '<div class="row">';
				$content .= '<div class="col-lg-12">';
					$content .= '<div class="page-header">';
						$content .= '<h1>Add Post</h1>';
						$content .= '<hr/>';
					$content .= '</div>';
					$content .= '<form action="'. $_SERVER['PHP_SELF'] .'" method="POST" onsubmit="return validate();" enctype="multipart/form-data" novalidate="novalidate">';	
					
						//Title input
						$content .= '<div class="form-group">';
							$content .= '<label for="title">Title:</label>';
							$content .= '<input class="form-control" type="text" name="title" id="title" placeholder="Title" required="required" onchange="validate();"/>';
							$content .= '<small id="titleError" class="hide red"></small>';
							$content .= '<small id="titleHelpBlock" class="form-text text-muted">';
  								$content .= 'The title of your post, please make it as descriptive as possible to the incident';
							$content .= '</small>';
						$content .= '</div>';
						
						//Content input
						$content .= '<div class="form-group">';
							$content .= '<label for="content">Content:</label>';
							$content .= '<textarea class="form-control" rows="5" name="content" id="content" placeholder="Content" required="required" onchange="validate();"></textarea>';
							$content .= '<small id="contentError" class="hide red"></small>';
							$content .= '<small id="contentHelpBlock" class="form-text text-muted">';
  								$content .= 'The content of your post, please describe the incident happened';
							$content .= '</small>';
						$content .= '</div>';
						
						$content .= '<div class="row">';
							$content .= '<div class="col-lg-3">';
							
								//Gender input
								$content .= '<div class="form-group">';
									$content .= '<label for="severity">Severity:</label>';
									$content .= '<select name="severity" class="form-control" id="severity" required="required" onchange="validate();"/>';
										$content .= '<option value="" selected="selected" disabled="disabled">Severity</option>';
										for($i = 1; $i <= 10; $i++) {
											$content .= '<option value="'. $i .'">'. $i .'</option>';
										}
									$content .= '</select>';
									$content .= '<small id="severityError" class="hide red"></small>';
									$content .= '<small id="severityHelpBlock" class="form-text text-muted">';
										$content .= 'Severity of the incident, 1 being the lowest, 10 being the highest';
									$content .= '</small>';
								$content .= '</div>';
							$content .= '</div>';
							$content .= '<div class="col-lg-3">';
							
								//Country input
								$content .= '<div class="form-group">';
									$content .= '<label for="country">Country:</label>';
									$content .= '<input class="form-control" type="text" name="country" id="country" placeholder="Country" value="'. $_SESSION['country'] .'" required="required" onchange="validate();"/>';
									$content .= '<small id="countryError" class="hide red"></small>';
									$content .= '<small id="countryHelpBlock" class="form-text text-muted">';
										$content .= 'Country of the incident';
									$content .= '</small>';
								$content .= '</div>';
							$content .= '</div>';
							$content .= '<div class="col-lg-3">';
							
								//State input
								$content .= '<div class="form-group">';
									$content .= '<label for="state">State:</label>';
									$content .= '<input class="form-control" type="text" name="state" id="state" placeholder="State" value="'. $_SESSION['state'] .'" required="required" onchange="validate();"/>';
									$content .= '<small id="stateError" class="hide red"></small>';
									$content .= '<small id="stateHelpBlock" class="form-text text-muted">';
										$content .= 'State of the incident';
									$content .= '</small>';
								$content .= '</div>';
							$content .= '</div>';
							$content .= '<div class="col-lg-3">';
							
								//City input
								$content .= '<div class="form-group">';
									$content .= '<label for="city">City:</label>';
									$content .= '<input class="form-control" type="text" name="city" id="city" placeholder="City" value="'. $_SESSION['city'] .'" required="required" onchange="validate();"/>';
									$content .= '<small id="cityError" class="hide red"></small>';
									$content .= '<small id="cityHelpBlock" class="form-text text-muted">';
										$content .= 'City of the incident';
									$content .= '</small>';
								$content .= '</div>';
							$content .= '</div>';							
						$content .= '</div>';
						
						//Image input (optional)
						$content .= '<div class="form-group">';
							$content .= '<label for="image">Image:</label>';
							$content .= '<input class="form-control" type="file" name="image" id="image" onchange="validate();"/>';
							$content .= '<small id="imageError" class="hide red"></small>';
							$content .= '<small id="imageHelpBlock" class="form-text text-muted">';
								$content .= 'Image of the incident (Max: 1MB)';
							$content .= '</small>';
						$content .= '</div>';
						
						//Submit button
						$content .= '<div class="form-group">';
							$content .= '<input class="btn btn-primary" type="submit" name="submit" value="Add"/>';
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

<script type="text/javascript">
//Perform validation on the form fields based on the PHP validation in the client side
function validate() {	
	
	var valid = true;
	
	//Title validation
	var title = "#title";

	if($(title).val() == null || $(title).val() == "" || $(title).val().length < 6 || !$(title)[0].checkValidity()) {
		changeBorder(title, "red");
		showErrorText(title, "Title Should Be At Least 6 Characters");
		valid = false;
	} else {
		changeBorder(title, "green");
		hideErrorText(title);
	}
	
	//Content validation
	var content = "#content";

	if($("textarea" + content).val() == null || $("textarea" + content).val() == "" || $("textarea" + content).val().length < 6) {
		changeBorder("textarea" + content, "red");
		showErrorText(content, "Content Should Be At Least 6 Characters");
		valid = false;
	} else {
		changeBorder("textarea" + content, "green");
		hideErrorText(content);
	}
	
	//Severity validation
	var severity = "#severity";
	
	if($(severity).val() == null || $(severity).val() == "" || !$(severity)[0].checkValidity()) {
		changeBorder(severity, "red");
		showErrorText(severity, "Please Select The Severity of the Incident");
		valid = false;
	} else {
		changeBorder(severity, "green");
		hideErrorText(severity);
	}
	
	//Country validation
	var country = "#country";
	
	if($(country).val() == null || $(country).val() == "" || $(country).val().length < 6 || !$(country)[0].checkValidity()) {
		changeBorder(country, "red");
		showErrorText(country, "Please Ensure That The Country is At Least 6 Characters");
		valid = false;
	} else {
		changeBorder(country, "green");
		hideErrorText(country);
	}
	
	//State validation
	var state = "#state";
	
	if($(state).val() == null || $(state).val() == "" || $(state).val().length < 6 || !$(state)[0].checkValidity()) {
		changeBorder(state, "red");
		showErrorText(state, "Please Ensure That The State is At Least 6 Characters");
		valid = false;
	} else {
		changeBorder(state, "green");
		hideErrorText(state);
	}
	
	//City validation
	var city = "#city";
	
	if($(city).val() == null || $(city).val() == "" || $(city).val().length < 6 || !$(city)[0].checkValidity()) {
		changeBorder(city, "red");
		showErrorText(city, "Please Ensure That The City is At Least 6 Characters");
		valid = false;
	} else {
		changeBorder(city, "green");
		hideErrorText(city);
	}
	
	//Image validation
	var image = "#image";
	
	//Check if an image is selected and perform the validation if an image is selected
	if($(image).val().length != 0) {	
	
		//Ensure that the extension of the image is "jpg", "png" or "gif" and the size is less that 1MB	
		if($(image)[0].files[0].size > 1000000 || ($(image)[0].files[0].name.split('.').pop().toLowerCase() != "jpg" && $(image)[0].files[0].name.split('.').pop().toLowerCase() != "png" && $(image)[0].files[0].name.split('.').pop().toLowerCase() != "gif")) {
			changeBorder(image, "red");
			showErrorText(image, "Please Ensure That The File Size is Less Than 1MB and the File is of jpg, png or gif Type");
			valid = false;
		} else {
			changeBorder(image, "green");
			hideErrorText(image);
		}
	}
	
	//Return the validity of the form
	return valid;
}

//Show error text below the invalid input fields
function showErrorText(element, errorText) {
	$(element + "Error").html(errorText);
	$(element + "Error").removeClass('hide');
}

//Hide error text below the input fields
function hideErrorText(element) {
	$(element + "Error").addClass('hide');
}

//Change the border color of the input fields
function changeBorder(element, color) {
	$(element).css("border-top-color", color);
	$(element).css("border-left-color", color);
	$(element).css("border-bottom-color", color);
	$(element).css("border-right-color", color);
}
</script>