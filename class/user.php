<?php
/* user.php
 * Contains the User class that stores the data and methods related to the user operations
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */

date_default_timezone_set('Asia/Kuala_Lumpur');

//Scripts required
require_once("data.php");
require_once("database.php");

//Check if there exists session establishment, establish the session if not
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

class User {
	
	//Attributes to store the user information
	private $email;
	private $password;
	private $fullName;
	private $gender;
	private $birthday;
	private $location;
	
	//Constructor to initialize all the attributes
	public function __construct($email, $password, $fullName, $gender, $birthday, Location $location) {
		$this->email 	= $email;
		$this->password = $password;
		$this->fullName = $fullName;
		$this->gender 	= $gender;
		$this->birthday = $birthday;
		$this->location = $location;
	}
	
	/* Getter Methods */
	//Return the value stored in the attributes accordingly
	private function getEmail() 	{ return $this->email; }
	private function getPassword() 	{ return $this->password; }
	private function getFullName() 	{ return $this->fullName; }
	private function getGender() 	{ return $this->gender; }
	private function getBirthday() 	{ return $this->birthday; }
	private function getLocation() 	{ return $this->location; }
	
	//Perform the login operation
	public static function login($email, $password) {
		
		try {
			
			//Open a connection to the database
			$db = new Database(WebsiteData::$databaseServer, WebsiteData::$databaseUsername, WebsiteData::$databasePassword, WebsiteData::$databaseName);
			$db->connectDB();
			
			//Get the connection to use the prepared statement to prevent SQL injection in the login process
			$conn = $db->getConn();
			
			//Prepared statement
			if(!$stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=? LIMIT 1")) {
				throw new Exception("Failed to Perform Login Query: ". $conn->error);
			}
			
			$stmt->bind_param('ss', $email, md5($password));
			
			//Get the email and password from the user
			$email 	  = mysqli_real_escape_string($conn, $email);
			$password = mysqli_real_escape_string($conn, $password);
			
			//Execute the SQL command
			$stmt->execute();
			
			//Bind the result to the variable
			$userResult = $stmt->get_result();
			
			//Email and password are correct
			if($userResult->num_rows == 1) {
						
				$userRow = $userResult->fetch_assoc();
				
				//Set session variables
				$_SESSION['id'] 		= $userRow['id'];
				$_SESSION['email'] 		= $userRow['email']; 
				$_SESSION['fullName'] 	= $userRow['full_name'];
				$_SESSION['gender'] 	= ($userRow['gender'] ? 'Male' : 'Female');
				$_SESSION['birthday'] 	= $userRow['birthday'];
				
				//Query the user location
				$locationResult = $db->queryDB("SELECT * FROM user_locations WHERE user_id='". $userRow['id'] ."' LIMIT 1");
				$locationRow = $locationResult->fetch_assoc();
				
				//Set session variables
				$_SESSION['country'] 	= $locationRow['country'];
				$_SESSION['state'] 		= $locationRow['state'];
				$_SESSION['city'] 		= $locationRow['city'];
				
				//Update user last login time
				$db->queryDB("UPDATE users SET last_login=NOW()");
				
				//Terminate the connections
				$stmt->close();
				$db->disconnectDB();
				
				return true;
				
			} else {	
			
				//Invalid email or password		
				return false;			
			}
		
		} catch(Exception $e) {
			exit($e);
		}
	}
	
	//Log the user out
	public static function logout() {	
		
		//Unset the session variables and terminate the session
		session_unset();
		session_destroy();
		
		//Return the login status, if not logged in, means logged out successfully
		return !self::checkLoginStatus();
	}
	
	//Check if the user is logged in by checking on the status of all the session variables
	public static function checkLoginStatus() {		
		return (isset($_SESSION['id']) && isset($_SESSION['email']) && isset($_SESSION['fullName']) && isset($_SESSION['gender']) && isset($_SESSION['birthday']));
	}
	
	//Perform the regtister operation
	public function register() {
		
		//Open a connection to the database
		$db = new Database(WebsiteData::$databaseServer, WebsiteData::$databaseUsername, WebsiteData::$databasePassword, WebsiteData::$databaseName);
		$db->connectDB();
		
		//Get the register information from the user
		$email 		= $db->getConn()->real_escape_string($this->getEmail());
		$password 	= $db->getConn()->real_escape_string($this->getPassword());
		$fullName 	= $db->getConn()->real_escape_string($this->getFullName());
		$gender 	= $db->getConn()->real_escape_string($this->getGender());
		$birthday 	= $db->getConn()->real_escape_string($this->getBirthday());
		$country 	= ucwords($db->getConn()->real_escape_string($this->getLocation()->getCountry()));
		$state 		= ucwords($db->getConn()->real_escape_string($this->getLocation()->getState()));
		$city 		= ucwords($db->getConn()->real_escape_string($this->getLocation()->getCity()));
		
		//Check if email exists
		$emailExistResult = $db->queryDB("SELECT * FROM users WHERE email='". $email ."' LIMIT 1");
		
		if($emailExistResult->num_rows != 1) {
				
			//Insert the user information to the database if the email does not exist
			$insertUser = $db->queryDB("INSERT INTO users (email, password, full_name, gender, birthday, register, last_login) VALUES ('". $email ."', '". md5($password) ."', '". $fullName ."', '". $gender ."', '". $birthday ."', NOW(), NULL)");
			
			//Insert the user location
			$insertLocation = $db->queryDB("INSERT INTO user_locations (country, state, city, user_id) VALUES ('". $country ."', '". $state ."', '". $city ."', LAST_INSERT_ID())");
			
			//Close the database connection
			$db->disconnectDB();
			
			//Return the status back to the caller function
			return ($insertUser && $insertLocation);
			
		} else {
			
			//Close the database connection
			$db->disconnectDB();
			
			//Return false since email already exists
			return false;
		}
	}
}
?>