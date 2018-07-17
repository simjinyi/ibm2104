<?php
/* location.php
 * Contains the Location class to store the data of the locations for the User and Post classes
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */

date_default_timezone_set('Asia/Kuala_Lumpur');

class Location {
	
	//Attributes to store the location information
	private $country;
	private $state;
	private $city;
	
	//Constructor initialize the values of the attributes
	public function __construct($country, $state, $city) {
		$this->country 	= $country;
		$this->state 	= $state;
		$this->city 	= $city;
	}
	
	/* Getter Methods */
	//Return the respective value for the attributes
	public function getCountry() {
		return $this->country;
	}
	
	public function getState() {
		return $this->state;
	}
	
	public function getCity() {
		return $this->city;
	}
}
?>