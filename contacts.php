<?php
/* contacts.php
 * Emergency contacts page
 * Shaun Lee Sun Her, Sim Jin Yi, Hoo Weng Shang, Ang Chee Keat
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

$pageHeader = new Header(WebsiteData::$websiteTitle, "Emergency Contacts", "Get the Latest Emergency Contacts Information Here", WebsiteData::$navItems);
$pageFooter = new Footer(WebsiteData::$websiteTitle, date('Y'));
$template = new Template($pageHeader, $pageFooter);


//Empty sting to concatenate the HTML output
$content = "";

$content .= '
	<style>
		table { 
			width: 950px;
        	border:2px solid red;
		}
		
		th { 
			height: 60px;
     		background-color: #990000;
		 	color: white;
		 	font-size: 30px;
		 	text-align: left;
			font-family: Kristen ITC;
		}
		
		td { 
			height: 60px;
     		font-size: 20px;
	 		font-family: Arial;
		}
		
		.column {
  			float: left;
  			width: 40%;
  			padding: 5px;
		}
		
		.row::after {
		  	content: "";
  			clear: both;
  			display: table;
		}
	</style>
';

//Content of the page
$content .= '<section class="mt-5 mb-5" id="content">';
	$content .= '<div class="container">';
		$content .= '<div class="row">';
			$content .= '<div class="col-lg-12">';
				$content .= '<div class="table">';
					$content .= '<img src="images/emer_banner.gif" style="height:220px; width:100%;">';
 					$content .= '<img src="images/heart_line.gif" style="height:50px; width:100%;">';
					$content .= '<table class="table" border="2px">';
        				$content .= '<tr>';
            				$content .= '<th>';
								$content .= '<center><b>Emergency Contact Numbers</b></center>';
							$content .= '</th>';
        				$content .= '</tr>';
						$content .= '<tr>';
            				$content .= '<td>';
								$content .= '<img src="images/phone.png" style="padding-left:270px; float:left; height:50px; width:80px;">';
								$content .= '<img src="images/telephone.jpg"  style="float:right; height:50px; width:300px;">';
								$content .= '<p style="color:red; padding-left:330px; line-height: 280%;"><b>General Emergency Hotline</b></p>';
            				$content .= '</td>';
        				$content .= '</tr>';
        				$content .= '<tr>';
            				$content .= '<td>';
								$content .= '<div class="row">';
            						$content .= '<div class="column">';
            							$content .= '<img src="images/999.jpg" style=" padding-left: 10px;  height:250px; width:300px;">';
									$content .= '</div>';
									$content .= '<div class="column">';
										$content .= '<img src="images/PLUS.png" style="  height:50px; width:100px;">';
										$content .= '<p><u><b>Plus Highway Contact</b></u></p>';
										$content .= '<p>AAM Toll Free Hotline: 1800 88 0808 </p>';
										$content .= '<p>PLUSLINE             : 1800 88 0000 </p>'; 
										$content .= '<p>Touch \'N Go          : 03-7628 5115 </p>';
									$content .= '</div>';
								$content .= '</div>';
            				$content .= '</td>';
        				$content .= '</tr>';
        				$content .= '<tr>';
            				$content .= '<td>';
								$content .= '<img src="images/penang.jpg"  style=" padding-left: 280px; float:left; height:60px; width:50px;">';
								$content .= '<img src="images/telephone.jpg"  style=" float:right; height:50px; width:300px;">';
								$content .= '<p style="color:red; padding-left:350px; line-height: 280%;"><b>State Emergency Hotline</b></p>';
            				$content .= '</td>';
        				$content .= '</tr>';
						$content .= '<tr>';
							$content .= '<td>';
								$content .= '<p><b>&emsp; <u>Emergency Contacts</u></b></p>';
								$content .= '<p>&emsp; Traffic Light Failure (JKR): 04-283 1522</p>';
								$content .= '<p>&emsp; Street Light & Electrical Power Failure (TNB): 04-227 2021/15454</p>';
								$content .= '<p>&emsp; Garbage/Drain/Pot Holes (MPPP): 04-263 3000 </p>';
								$content .= '<p>&emsp; Public Water Pipes Failure (JKA): 04-262 5321 </p>';
							$content .= '</td>';
        				$content .= '</tr>';
    				$content .= '</table>';
				$content .= '</div>';
			$content .= '</div>';
		$content .= '</div>';
	$content .= '</div>';
$content .= '</section>';

//Print out the entire webpage
echo $template->drawPage($content);
?>