<?php
/* date.php
 * Contains the Date class that stores the static methods related to date operations
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */

date_default_timezone_set('Asia/Kuala_Lumpur');

class Date {
	
	//Get the month in the form of integer and return the string form
	public static function getMonth($month) {
		switch($month) {
			case 1:
				return "January";
				
			case 2:
				return "February";
				
			case 3:
				return "March";
				
			case 4:
				return "April";
				
			case 5:
				return "May";
				
			case 6:
				return "June";
				
			case 7:
				return "July";
				
			case 8:
				return "August";
				
			case 9:
				return "September";
				
			case 10:
				return "October";
				
			case 11:
				return "November";
				
			case 12:
				return "December";
				
			default:
				return "Error";
		}
	}
	
	//Get the date and return if the date is valid
	public static function validateDate($date) {
		
		//Split the date into the form of day-month-year
		$dateTemp = explode('-', $date);
		
		//Assign the values from the array to the variables accordingly
		$year = $dateTemp[0];
		$month = $dateTemp[1];
		$day = $dateTemp[2];
		
		if($day < 0 || $day > 31) {
			return false;
		} else {
			
			//Check if the month is February
			if($month == 2) {
				
				//Check for leap year
				if($year % 4 == 0) {
					if($day <= 28) {
						return true;
					} else {
						return false;
					}
				} else {
					if($day <= 29) {
						return true;
					} else {
						return false;
					}
				}
			} else {
				
				//Check for months with 31 days
				if($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12) {
					if($day <= 31) {
						return true;
					} else {
						return false;
					}
				} else {
					
					//Check for months with 30 days
					if($day <= 30) {
						return true;
					} else {
						return false;
					}
				}
			}
		}
		
		//If every conditions fails, the date must be invalid
		return false;
	}
	
	//Get the second in the form of integer and convert it to string form
	public static function secondsToWord($s) {
		
		//Perform the calculations on the second
		$days 	 = $s/(3600 * 24);
		$hours 	 = $s/2600 % 24;
		$minutes = $s/60 % 60;
		$seconds = $s % 60;
		
		//Return the string form of the second
		return (floor($days) > 0 ? $days."d" : "") ." ". (floor($hours) > 0 ? $hours."h" : "") ." ". (floor($minutes) > 0 ? $minutes."m" : "") ." ". (floor($seconds) > 0 ? $seconds."s" : "0s");
	}
}
?>