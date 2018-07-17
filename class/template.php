<?php
/* template.php
 * Contains the classes in drawing the bare HTML pages
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */
 
date_default_timezone_set('Asia/Kuala_Lumpur');

class Template {
	
	//Attributes to store the header, content and footer sections
	private $pageHeader;
	private $pageContent;
	private $pageFooter;
	
	//Constructor to initialize the header and footer of the page
	public function __construct(Header $pageHeader, Footer $pageFooter) {
		$this->pageHeader = $pageHeader;
		$this->pageFooter = $pageFooter;
	}
	
	//Draw the bare HTML page with the contents of the page filled
	public function drawPage($pageContent) {
		
		//Set the page content
		$this->pageContent = $pageContent;
		
		//Empty sting to concatenate the HTML output
		$content = "";
		
		$content .= '<!DOCTYPE html>';
		$content .= '<html lang="en">';

			//Get the contents under the head tag
			$content .= $this->pageHeader->getHead();
			
			$content .= '<body id="page-top">';
			
				//Print out the navbar
				$content .= $this->pageHeader->getNav();
			
				//Print out the header section
				$content .= $this->pageHeader->getHeader();
				
				$content .= $this->pageContent;
				
				//Print out the footer section
				$content .= $this->pageFooter->getFooter();
		
			$content .= '</body>';
		$content .= '</html>';
		
		//Return the formed HTML page to the caller function
		return $content;
	}
}

class Header {
	
	//Attributes to store all the details needed in the header section
	private $websiteTitle;
	private $pageTitle;
	private $headerText;
	private $navItems = array();
	
	//Constructor to initialize all the attributes
	public function __construct($websiteTitle = "", $pageTitle = "", $headerText = "", $navItems = array()) {
		$this->websiteTitle = $websiteTitle;
		$this->pageTitle 	= $pageTitle;
		$this->headerText 	= $headerText;
		$this->navItems 	= $navItems;
	}
	
	/* Getter Methods */
	//Return the HTML contents of the head section of the page
	public function getHead() {
		
		//Empty sting to concatenate the HTML output
		$content = "";
		
		//Head
		$content .= '<head>';
		
			//Meta contents
			$content .= '<meta charset="utf-8">';
			$content .= '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
			
			//Webpage title
			$content .= '<title>'. $this->websiteTitle .' - '. $this->pageTitle .'</title>';
			
			/* CSS Files */
			//Bootstrap core CSS
			$content .= '<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">';
			
			//Custom CSS
			$content .= '<link href="css/safetyFirst.css" rel="stylesheet">';
			
		$content .= '</head>';
		
		//Return the head contents to the caller function
		return $content;
	}
	
	//Return the HTML contents of the navbar section of the page
	public function getNav() {
		
		//Empty sting to concatenate the HTML output
		$content = "";
		
		//Navigation
		$content .= '<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">';
			$content .= '<div class="container">';
				$content .= '<a class="navbar-brand js-scroll-trigger" href="#page-top">'. $this->websiteTitle .'</a>';
				$content .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">';
				$content .= '<span class="navbar-toggler-icon"></span>';
				$content .= '</button>';
				$content .= '<div class="collapse navbar-collapse" id="navbarResponsive">';
					$content .= '<ul class="navbar-nav ml-auto">';
						
						//Loop through items in navbar and print them out
						foreach($this->navItems as $item) {
							
							//Print the item as active if the user is currently on that page
							if($this->pageTitle == $item['value']) {
								$content .= '<li class="nav-item active">';
									$content .= '<a class="nav-link js-scroll-trigger" href="'. $item['href'] .'">'. $item['value'] .'</a>';
								$content .= '</li>';
							} else {
								$content .= '<li class="nav-item">';
									$content .= '<a class="nav-link js-scroll-trigger" href="'. $item['href'] .'">'. $item['value'] .'</a>';
								$content .= '</li>';
							}
						}
						
					$content .= '</ul>';
				$content .= '</div>';
			$content .= '</div>';
		$content .= '</nav>';
		
		//Return the navbar contents to the caller function
		return $content;
	}
	
	//Return the HTML contents of the header section of the page
	public function getHeader() {
		
		//Empty sting to concatenate the HTML output
		$content = "";
		
		//Header
		$content .= '<header class="text-white">';
			$content .= '<div class="container text-center">';
				$content .= '<h1>'. $this->websiteTitle .'</h1>';
				$content .= '<h2>'. $this->pageTitle .'</h2>';
				$content .= '<p class="lead">'. $this->headerText .'</p>';
			$content .= '</div>';
		$content .= '</header>';
		
		//Return the header contents to the caller function
		return $content;
	}
}

class Footer {
	
	//Attributes to store all the details needed in the footer section
	private $websiteTitle;
	private $copyrightYear;
	
	//Constructor to initalize all the attributes
	public function __construct($websiteTitle = "", $copyrightYear = 2018) {
		$this->websiteTitle  = $websiteTitle;
		$this->copyrightYear = $copyrightYear;
	}
	
	/* Getter Methods */
	//Return the HTML contents of the footer section of the page
	public function getFooter() {
		
		//Empty sting to concatenate the HTML output
		$content = "";
		
		//Footer
		$content .= '<footer class="py-5 bg-dark">';
			$content .= '<div class="container">';
				$content .= '<p class="m-0 text-center text-white">Copyright &copy; '. $this->websiteTitle .' '. $this->copyrightYear .'</p>';
				$content .= '<p class="mt-3 text-center"><a class="text-white" href="contacts.php">Emergency Contacts</a> &nbsp; <a class="text-white" href="aboutUs.php">About Us</a> &nbsp; <a class="text-white" href="terms.php">Terms of Use</a></p>';
			$content .= '</div>';
		$content .= '</footer>';
		
		/* JavaScript Files */
		//Bootstrap core JavaScript
		$content .= '<script type="text/javascript" src="vendor/jquery/jquery.min.js"></script>';
		$content .= '<script type="text/javascript" src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>';
		
		//Plugin JavaScript
		$content .= '<script type="text/javascript" src="vendor/jquery-easing/jquery.easing.min.js"></script>';
		
		//Custom JavaScript
		$content .= '<script type="text/javascript" src="js/safetyFirst.js"></script>';
		
		//Return the header contents to the caller function
		return $content;
	}
}
?>