<?php
/* register.php
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
require_once("class/location.php");
require_once("class/date.php");
require_once("class/notification.php");

//Check if the user is logged in
if(User::checkLoginStatus()) {
	
	//Redirect the user back to the home page
	$notification = new Notification('You have already logged in', 'success');
	$notification->redirectWithMsg('index.php');
}


//Template of the website
if(User::checkLoginStatus()) {
	$website = new WebsiteData(User::checkLoginStatus(), $_SESSION['fullName']);
} else {
	$website = new WebsiteData();
}

$pageHeader = new Header(WebsiteData::$websiteTitle, "Register", "Register an Account", WebsiteData::$navItems);
$pageFooter = new Footer(WebsiteData::$websiteTitle, date('Y'));
$template = new Template($pageHeader, $pageFooter);


//Empty sting to concatenate the HTML output
$content = "";

if(isset($_POST['submit'])) {
	
	//Check if all the fields are filled. If not, redirect the user back to this page with the error message passed
	if(empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirmPassword']) || empty($_POST['fullName']) || empty($_POST['gender']) || empty($_POST['day']) || empty($_POST['month']) || empty($_POST['year'])) {
		$notification = new Notification('Please ensure that all the fields are filled before registering', 'danger');
		$notification->redirectWithMsg($_SERVER['PHP_SELF']);
	} else {
	
		//If all the fields are filled, assign the values from the "POST" array to the variables accordingly
		$email 			 = htmlentities($_POST['email']);
		$password 		 = htmlentities($_POST['password']);
		$confirmPassword = htmlentities($_POST['confirmPassword']);
		$fullName 		 = htmlentities($_POST['fullName']);
		$gender 		 = htmlentities($_POST['gender']);
		$day 			 = htmlentities($_POST['day']);
		$month 			 = htmlentities($_POST['month']);
		$year 			 = htmlentities($_POST['year']);
		$birthday 		 = $year."-".$month."-".$day;
		$country 		 = htmlentities($_POST['country']);
		$state 			 = htmlentities($_POST['state']);
		$city 			 = htmlentities($_POST['city']);
		
		//Variables involved in error handling
		$errorMessage = "";
		$error = false;
		
		//Validate if the email is valid
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error = true;
			$errorMessage .= "Please ensure that the email is valid<br/>";
		}
		
		//Validate if the password is less than 6 characters
		if(strlen($password) < 6) {
			$error = true;
			$errorMessage .= "Please ensure that the password is more than 6 characters<br/>";
		}
		
		//Validate if the password is the same as the confirm password
		if($password != $confirmPassword) {
			$error = true;
			$errorMessage .= "The password and confirm password does not match, please try again<br/>";
		}
		
		//Validate if the full name is less than 6 characters
		if(strlen($fullName) < 6) {
			$error = true;
			$errorMessage .= "Please ensure that the full name is more than 6 characters<br/>";
		}
		
		//Validate if the gender is valid
		if($gender != "1" && $gender != "2") {
			$error = true;
			$errorMessage .= "Please ensure that the gender is either Male or Female<br/>";
		}
		
		//Validate if the date is valid by calling the "validateDate" function from "function/util.php"
		if(!Date::validateDate($birthday)) {
			$error = true;
			$errorMessage .= "Please ensure that the birthday is valid<br/>";
		}
		
		//Validate if the country is less than 6 characters
		if(strlen($country) < 6) {
			$error = true;
			$errorMessage .= "Please ensure that the country is more than 6 characters<br/>";
		}
		
		//Validate if the state is less than 6 characters
		if(strlen($state) < 6) {
			$error = true;
			$errorMessage .= "Please ensure that the state is more than 6 characters<br/>";
		}
		
		//Validate if the city is less than 6 characters
		if(strlen($city) < 6) {
			$error = true;
			$errorMessage .= "Please ensure that the city is more than 6 characters<br/>";
		}
		
		//Check if there is an error
		if($error) {
			
			//Prompt error message
			$notification = new Notification($errorMessage, 'danger');
			$notification->redirectWithMsg($_SERVER['PHP_SELF']);
			
		} else {
			
			//Create Location and User objects
			$userLocation = new Location($country, $state, $city);
			$userDetails = new User($email, $password, $fullName, $gender, $birthday, $userLocation);
			
			//Check if the user is registered successfully
			if($userDetails->register()) {
				
				//Redirect the user to the login page
				$notification = new Notification('Successfully registered', 'success');
				$notification->redirectWithMsg('login.php');
				
			} else {
				
				//Prompt the error message where the email address already exist
				$notification = new Notification('The email exists, please try again with another email', 'danger');
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
						$content .= '<h1>Register</h1>';
						$content .= '<hr/>';
					$content .= '</div>';
					$content .= '<form action="'. $_SERVER['PHP_SELF'] .'" method="POST" onsubmit="return validate();" novalidate="novalidate">';	
						
						$content .= '<br/><h2>Login Credentials</h2>';
						
						//Email input
						$content .= '<div class="form-group">';
							$content .= '<label for="email">Email Address:</label>';
							$content .= '<input class="form-control" type="email" name="email" id="email" placeholder="Email Address" required="required" onchange="validate();"/>';
							$content .= '<small id="emailError" class="hide red"></small>';
							$content .= '<small id="emailHelpBlock" class="form-text text-muted">';
  								$content .= 'Your email used to login';
							$content .= '</small>';
						$content .= '</div>';
						
						//Password input
						$content .= '<div class="form-group">';
							$content .= '<label for="password">Password:</label>';
							$content .= '<input class="form-control" type="password" name="password" id="password" placeholder="Password" required="required" onchange="validate();"/>';
							$content .= '<small id="passwordError" class="hide red"></small>';
							$content .= '<small id="passwordHelpBlock" class="form-text text-muted">';
  								$content .= 'Password must be at least 6 characters';
							$content .= '</small>';
						$content .= '</div>';
						
						//Confirm assword input
						$content .= '<div class="form-group">';
							$content .= '<label for="confirmPassword">Confirm Password:</label>';
							$content .= '<input class="form-control" type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required="required" onchange="validate();"/>';
							$content .= '<small id="confirmPasswordError" class="hide red"></small>';
							$content .= '<small id="confirmPasswordHelpBlock" class="form-text text-muted">';
  								$content .= 'Reconfirm your password';
							$content .= '</small>';
						$content .= '</div>';
						
						$content .= '<br/><h2>Personal Details</h2>';
						
						//Full name input
						$content .= '<div class="form-group">';
							$content .= '<label for="fullName">Full Name:</label>';
							$content .= '<input class="form-control" type="text" name="fullName" id="fullName" placeholder="Full Name" required="required" onchange="validate();"/>';
							$content .= '<small id="fullNameError" class="hide red"></small>';
							$content .= '<small id="fullNameHelpBlock" class="form-text text-muted">';
  								$content .= 'Your full name that will appear on the posts';
							$content .= '</small>';
						$content .= '</div>';
						
						$content .= '<div class="row">';
							$content .= '<div class="col-lg-3">';
								//Gender input
								$content .= '<div class="form-group">';
									$content .= '<label for="gender">Gender:</label>';
									$content .= '<select name="gender" class="form-control" id="gender" required="required" onchange="validate();">';
										$content .= '<option value="" selected="selected" disabled="disabled">Gender</option>';
										$content .= '<option value="1">Male</option>';
										$content .= '<option value="2">Female</option>';
									$content .= '</select>';
									$content .= '<small id="genderError" class="hide red"></small>';
									$content .= '<small id="genderHelpBlock" class="form-text text-muted">';
										$content .= 'Gender';
									$content .= '</small>';
								$content .= '</div>';
							$content .= '</div>';
							$content .= '<div class="col-lg-9">';	
								$content .= '<div class="form-group">';
									$content .= '<label>Birthday:</label>';
									$content .= '<div class="row">';
										
										//Day input
										$content .= '<div class="col">';
											$content .= '<select name="day" class="form-control" id="day" required="required" onchange="validate();">';
												$content .= '<option value="" selected="selected" disabled="disabled">Day</option>';
												for($day = 1; $day <= 31; $day++) {
													$content .= '<option value="'. $day .'">'. $day .'</option>'; 
												}
											$content .= '</select>';
											$content .= '<small id="dayError" class="hide red"></small>';
											$content .= '<small id="dayHelpBlock" class="form-text text-muted">';
												$content .= 'Day of your Birthday';
											$content .= '</small>';
										$content .= '</div>';
										
										//Month input
										$content .= '<div class="col">';
											$content .= '<select name="month" class="form-control" id="month" required="required" onchange="validate();">';
												$content .= '<option value="" selected="selected" disabled="disabled">Month</option>';
												for($month = 1; $month <= 12; $month++) {
													$content .= '<option value="'. $month .'">'. Date::getMonth($month) .'</option>'; 
												}
											$content .= '</select>';
											$content .= '<small id="monthError" class="hide red"></small>';
											$content .= '<small id="monthHelpBlock" class="form-text text-muted">';
												$content .= 'Month of your Birthday';
											$content .= '</small>';
										$content .= '</div>';
										
										//Year input
										$content .= '<div class="col">';
											$content .= '<select name="year" class="form-control" id="year" required="required" onchange="validate();">';
												$content .= '<option value="" selected="selected" disabled="disabled">Year</option>';
												for($year = date("Y", time()); $year >= 1900; $year--) {
													$content .= '<option value="'. $year .'">'. $year .'</option>'; 
												}
											$content .= '</select>';
											$content .= '<small id="yearError" class="hide red"></small>';
											$content .= '<small id="yearHelpBlock" class="form-text text-muted">';
												$content .= 'Year of your Birthday';
											$content .= '</small>';
										$content .= '</div>';
									$content .= '</div>';
								$content .= '</div>';
							$content .= '</div>';
						$content .= '</div>';
						
						$content .= '<br/><h2>Location Information</h2>';
						
						//Country
						$content .= '<div class="form-group">';
							$content .= '<label for="country">Country:</label>';
							$content .= '<input class="form-control" type="text" name="country" id="country" placeholder="Country" required="required" onchange="validate();"/>';
							$content .= '<small id="countryError" class="hide red"></small>';
							$content .= '<small id="countryHelpBlock" class="form-text text-muted">';
								$content .= 'Your residential country';
							$content .= '</small>';
						$content .= '</div>';
						
						//State
						$content .= '<div class="form-group">';
							$content .= '<label for="state">State:</label>';
							$content .= '<input class="form-control" type="text" name="state" id="state" placeholder="State" required="required" onchange="validate();"/>';
							$content .= '<small id="stateError" class="hide red"></small>';
							$content .= '<small id="stateHelpBlock" class="form-text text-muted">';
								$content .= 'Your residential state';
							$content .= '</small>';
						$content .= '</div>';
						
						//City
						$content .= '<div class="form-group">';
							$content .= '<label for="city">City:</label>';
							$content .= '<input class="form-control" type="text" name="city" id="city" placeholder="City" required="required" onchange="validate();"/>';
							$content .= '<small id="cityError" class="hide red"></small>';
							$content .= '<small id="cityHelpBlock" class="form-text text-muted">';
								$content .= 'Your residential city';
							$content .= '</small>';
						$content .= '</div>';
						
						//Submit button
						$content .= '<div class="form-group">';
							$content .= '<input class="btn btn-primary" type="submit" name="submit" value="Register"/>';
						$content .= '</div>';
					$content .= '</form>';			
				$content .= '</div>';
			$content .= '</div>';	
		$content .= '</div>';
	$content .= '</section>';
	
	//Message section
	if(isset($_GET['msg']) && isset($_GET['status'])) {
		$notification = new Notification($_GET['msg'], $_GET['status']);
		$content .= $notification->getMessage($notification);
	}
}

//Print out the entire webpage
echo $template->drawPage($content);
?>

<script type="text/javascript">
//Perform validation on the form fields based on the PHP validation in the client side
function validate() {	
	
	var valid = true;
	
	//Email validation
	var email = "#email";

	if($(email).val() == null || $(email).val() == "" || $(email).val().length < 6 || !$(email)[0].checkValidity()) {
		changeBorder(email, "red");
		showErrorText(email, "Invalid Email Address");
		valid = false;
	} else {
		changeBorder(email, "green");
		hideErrorText(email);
	}
	
	//Password validation
	var password = "#password";

	if($(password).val() == null || $(password).val() == "" || $(password).val().length < 6 || !$(password)[0].checkValidity()) {
		changeBorder(password, "red");
		showErrorText(password, "Password Should Be At Least 6 Characters");
		valid = false;
	} else {
		changeBorder(password, "green");
		hideErrorText(password);
	}
	
	//Confirm password validation
	var confirmPassword = "#confirmPassword";

	if($(confirmPassword).val() == null || $(confirmPassword).val() == "" || $(confirmPassword).val().length < 6 || !$(confirmPassword)[0].checkValidity() || $(confirmPassword).val() != $(password).val()) {
		changeBorder(confirmPassword, "red");
		showErrorText(confirmPassword, "Confirm Password and Password Does Not Match");
		valid = false;
	} else {
		changeBorder(confirmPassword, "green");
		hideErrorText(confirmPassword);
	}
	
	//Full name validation
	var fullName = "#fullName";

	if($(fullName).val() == null || $(fullName).val() == "" || $(fullName).val().length < 6 || !$(fullName)[0].checkValidity()) {
		changeBorder(fullName, "red");
		showErrorText(fullName, "Full Name Should Be At Least 6 Characters");
		valid = false;
	} else {
		changeBorder(fullName, "green");
		hideErrorText(fullName);
	}
	
	//Gender validation
	var gender = "#gender";

	if($(gender).val() == null || $(gender).val() == "" || !$(gender)[0].checkValidity()) {
		changeBorder(gender, "red");
		showErrorText(gender, "Please Select Your Gender");
		valid = false;
	} else {
		changeBorder(gender, "green");
		hideErrorText(gender);
	}
	
	//Day validation
	var day = "#day";
	
	if($(day).val() == null || $(day).val() == "" || !$(day)[0].checkValidity()) {
		changeBorder(day, "red");
		showErrorText(day, "Please Select The Day of Your Birthday");
		valid = false;
	} else {
		changeBorder(day, "green");
		hideErrorText(day);
	}
	
	//Month validation
	var month = "#month";
	
	if($(month).val() == null || $(month).val() == "" || !$(month)[0].checkValidity()) {
		changeBorder(month, "red");
		showErrorText(month, "Please Select The Month of Your Birthday");
		valid = false;
	} else {
		changeBorder(month, "green");
		hideErrorText(month);
	}
	
	//Year validation
	var year = "#year";
	
	if($(year).val() == null || $(year).val() == "" || !$(year)[0].checkValidity()) {
		changeBorder(year, "red");
		showErrorText(year, "Please Select The Year of Your Birthday");
		valid = false;
	} else {
		changeBorder(year, "green");
		hideErrorText(year);
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
	
	//Return the validity of the form fields entered by the user
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