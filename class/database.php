<?php
/* database.php
 * Contains the Database class that stores the data and methods related to the database operations
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */
 
date_default_timezone_set('Asia/Kuala_Lumpur');

class Database {
	
	//Attributes to store the database information
	private $server;
	private $username;
	private $password;
	private $database;
	
	//Attribute to store the connection after successful establishment
	private $conn;
	
	//Constructor to initialize all the attributes
	public function __construct($server, $username, $password, $database) {
		$this->server 	= $server;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;
	}
	
	//To establish the connection to the database
	public function connectDB() {
		
		//Check if there is an error in the connection
		try {
			$this->conn = new mysqli($this->server, $this->username, $this->password);
				
			if(!($this->conn->connect_error)) {
				if(!$this->conn->select_db($this->database)) {
					throw new Exception("Unable to Select Database");
				}
			} else {
				throw new Exception("Unable to Establish Connection");
			}
		} catch(Exception $e) {
			exit($e);
		}
	}
	
	//Perform the query and return the result of the query
	public function queryDB($query) {
		
		//Check if there is an error in the query
		try {
			if($result = $this->conn->query($query)) {
				return $result;
			} else {
				throw new Exception("Unable to Perform Query: ". $this->conn->error);
			}
		} catch(Exception $e) {
			exit($e);
		}
	}
	
	//Close the connection to the database
	public function disconnectDB() {
		return $this->conn->close();
	}
	
	/* Getter Methods */
	//Get the connection object
	public function getConn() {
		return $this->conn;
	}
}
?>