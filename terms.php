<?php
/* contacts.php
 * Emergency contacts page
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
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

$pageHeader = new Header(WebsiteData::$websiteTitle, "Terms of Use", "Please Read the Terms of Use Before Using this Website", WebsiteData::$navItems);
$pageFooter = new Footer(WebsiteData::$websiteTitle, date('Y'));
$template = new Template($pageHeader, $pageFooter);


//Empty sting to concatenate the HTML output
$content = "";

//Content of the page
$content .= '<section class="mt-5 mb-5" id="content">';
	$content .= '<div class="container">';
		$content .= '<div class="row">';
			$content .= '<div class="col-lg-12">';
				$content .= '<h1>Terms of Service ("Terms")</h1>';
				$content .= '<p>Last updated: July 16, 2018</p>';
				$content .= '<p>Please read these Terms of Service ("Terms", "Terms of Service") carefully before using the ibm2104.jysim.net website (the "Service") operated by Safety First ("us", "we", or "our").</p>';
				$content .= '<p>Your access to and use of the Service is conditioned on your acceptance of and compliance with these Terms. These Terms apply to all visitors, users and others who access or use the Service.</p>';
				$content .= '<p>By accessing or using the Service you agree to be bound by these Terms. If you disagree with any part of the terms then you may not access the Service.</p>';
				$content .= '<hr/>';
				
				$content .= '<h2>Accounts</h2>';
				$content .= '<p>When you create an account with us, you must provide us information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>';
				$content .= '<p>You are responsible for safeguarding the password that you use to access the Service and for any activities or actions under your password, whether your password is with our Service or a third-party service.</p>';
				$content .= '<p>You agree not to disclose your password to any third party. You must notify us immediately upon becoming aware of any breach of security or unauthorized use of your account.</p>';
				$content .= '<hr/>';

				$content .= '<h2>Links To Other Web Sites</h2>';
				$content .= '<p>Our Service may contain links to third-party web sites or services that are not owned or controlled by Safety First.</p>';
				$content .= '<p>Safety First has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party web sites or services. You further acknowledge and agree that Safety First shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with use of or reliance on any such content, goods or services available on or through any such web sites or services.</p>';
				$content .= '<p>We strongly advise you to read the terms and conditions and privacy policies of any third-party web sites or services that you visit.</p>';
				$content .= '<hr/>';

				$content .= '<h2>Governing Law</h2>';
				$content .= '<p>These Terms shall be governed and construed in accordance with the laws of Malaysia, without regard to its conflict of law provisions.</p>';
				$content .= '<p>Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these Terms will remain in effect. These Terms constitute the entire agreement between us regarding our Service, and supersede and replace any prior agreements we might have between us regarding the Service.</p>';
				$content .= '<hr/>';

				$content .= '<h2>Changes</h2>';
				$content .= '<p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will try to provide at least 15 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>';
				$content .= '<p>By continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, please stop using the Service.</p>';
			$content .= '</div>';
		$content .= '</div>';
	$content .= '</div>';
$content .= '</section>';

//Print out the entire webpage
echo $template->drawPage($content);
?>