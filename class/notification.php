<?php
/* notifications.php
 * Contains the Notification class that handles all the notifications and redirections
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */
 
date_default_timezone_set('Asia/Kuala_Lumpur');

class Notification {
	
	//Attributes to store the notification data
	private $msg;
	private $status;
	
	//Constructor to intialize all the attributes 
	public function __construct($msg, $status) {
		$this->msg 	  = $msg;
		$this->status = $status;
	}
	
	//Draw the notification section
	public function getMessage() {
		
		//Empty sting to concatenate the HTML output
		$content = "";
		
		//Message section
		$content .= '<section id="message">';
			$content .= '<div class="container">';
				$content .= '<div class="row">';
					$content .= '<div class="col-lg-12">';
						$content .= '<div class="alert alert-'. $this->status .' alert-dismissible mt-5 mb-5">';
							$content .= '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
							$content .= $this->msg;
						$content .= '</div>';
					$content .= '</div>';
				$content .= '</div>';
			$content .= '</div>';
		$content .= '</section>';
		
		//Return the HTML notification body to the caller function
		return $content;
	}
	
	//Redirect the user to another page with the message passed
	public function redirectWithMsg($destination) {
		
		header('location: '. $destination .'?msg='. $this->msg .'&status='. $this->status .'');
		exit();
	}
}
?>