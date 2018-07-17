<?php
/* post.php
 * Contains the Post class that handles all the post operations
 * Sim Jin Yi, Shaun Lee Sun Her, Hoo Weng Shang, Ang Chee Keat
 */
 
date_default_timezone_set('Asia/Kuala_Lumpur');

//Scripts required
require_once("date.php");
require_once("data.php");
require_once("database.php");

class Post {
	
	//Attributes to store the post information
	private $title;
	private $content;
	private $severity;
	private $location;
	private $image;
	
	//Constructor to initialize the attributes
	public function __construct($title, $content, $severity, Location $postLocation, $image) {
		$this->title 	= $title;
		$this->content 	= $content;
		$this->severity = $severity;
		$this->location = $postLocation;
		$this->image = $image;
	}
	
	//Add and save the post into the database based on the user ID
	public function addPost($userID) {
		
		//Open a connection to the database
		$db = new Database(WebsiteData::$databaseServer, WebsiteData::$databaseUsername, WebsiteData::$databasePassword, WebsiteData::$databaseName);
		$db->connectDB();
		
		//Get the post information
		$postTitle 		= $db->getConn()->real_escape_string($this->title);
		$postContent 	= $db->getConn()->real_escape_string($this->content);
		$postSeverity 	= $db->getConn()->real_escape_string($this->severity);
		$postCountry 	= $db->getConn()->real_escape_string($this->location->getCountry());
		$postState 		= $db->getConn()->real_escape_string($this->location->getState());
		$postCity 		= $db->getConn()->real_escape_string($this->location->getCity());
		
		//Image is optional, hence check for its availability
		if(!empty($this->image['name'])) {
			
			//User posted an image
			$uploadImage = addslashes(file_get_contents($this->image['tmp_name']));
			
			//Save the post information with the image to the database
			$db->queryDB("INSERT INTO posts (title, content, severity, date, image, user_id) VALUES ('". $postTitle ."', '". $postContent ."', '". $postSeverity ."', NOW(), '". $uploadImage ."', '". $userID ."')"); 
			
		} else {
			
			//Save the post information without the image to the database
			$db->queryDB("INSERT INTO posts (title, content, severity, date, image, user_id) VALUES ('". $postTitle ."', '". $postContent ."', '". $postSeverity ."', NOW(), NULL, '". $userID ."')"); 
		}
		
		//Save the post location into the database
		$db->queryDB("INSERT INTO post_locations (country, state, city, post_id) VALUES ('". $postCountry ."', '". $postState ."', '". $postCity ."', LAST_INSERT_ID())"); 
		
		//Close the database connection
		$db->disconnectDB();
		
		return true;
	}
	
	//Add and save the reaction to the database
	public static function reactPost($reaction, $postID, $userID) {
		
		//Open a connection to the database
		$db = new Database(WebsiteData::$databaseServer, WebsiteData::$databaseUsername, WebsiteData::$databasePassword, WebsiteData::$databaseName);
		$db->connectDB();
		
		//Check and assign the react code based on the reaction
		if($reaction == "like") {
			$reactCode = 1;
		} else {
			$reactCode = 2;
		}
		
		//Check if the user has reacted to the post
		$reactionResult = $db->queryDB("SELECT * FROM post_likes WHERE post_id='". $postID ."' AND user_id='". $userID ."' LIMIT 1");
		
		//User has reacted
		if($reactionResult->num_rows == 1) {
			
			$row = $reactionResult->fetch_assoc();
			
			//Update the reaction
			if($row['post_like'] != $reactCode) {
				$db->queryDB("UPDATE post_likes SET post_like='". $reactCode ."' WHERE user_id='". $userID ."' AND post_id='". $postID ."'");
			} else {
				$db->queryDB("DELETE FROM post_likes WHERE user_id='". $userID ."' AND post_id='". $postID ."'");
			}
		} else {
			
			//User not reacted
			$db->queryDB("INSERT INTO post_likes (post_like, post_id, user_id) VALUES ('". $reactCode ."', '". $postID ."', '". $userID ."')");
		}
		
		$db->disconnectDB();
		
		return true;
	}
	
	//Return the news feed to the caller function
	public static function getNewsFeed($login, $sortData, $pageNo, $postsPerPage, $locationFilter, $postFilter, $userID = 0) {
		
		//Open a connection to the database
		$db = new Database(WebsiteData::$databaseServer, WebsiteData::$databaseUsername, WebsiteData::$databasePassword, WebsiteData::$databaseName);
		$db->connectDB();
		
		//Check for the page number to ensure that the page number is not out of range		
		if($pageNo < 1) {
			$pageNo = 1;
		}
		
		//Location filter variables
		$filterCountry = "";
		$filterState = "";
		$filterCity = "";
		
		//Store the result set after querying for all the posts in the database
		$totalResult = "";
		
		//Check if the user wants to filter the post based on the location
		if($locationFilter != NULL) {
			
			$filterCountry = $locationFilter[0];
			$filterState = $locationFilter[1];
			$filterCity = $locationFilter[2];
			
			//Total Posts Found
			//Check if the user wants to filter and view only his/her posts with ternary operator
			$totalResult = $db->queryDB("SELECT * FROM posts WHERE id IN (SELECT post_id FROM post_locations WHERE country LIKE '". $filterCountry ."' AND state LIKE '". $filterState ."' AND city LIKE '". $filterCity ."')". ($postFilter == "true" ? " AND user_id='". $userID ."'" : "") ." ORDER BY ". $sortData['sortBy'] ." ". strtoupper($sortData['sortOrder']) ."");
			
		} else {
			
			//User does not want to filter the post based on the location
			//Total Posts Found
			//Check if the user wants to filter and view only his/her posts with ternary operator
			$totalResult = $db->queryDB("SELECT * FROM posts". ($postFilter == "true" ? " WHERE user_id='". $userID ."'" : "") ." ORDER BY ". $sortData['sortBy'] ." ". strtoupper($sortData['sortOrder']) ."");
		}
		
		//Get the total number of the posts after filter (if applied)
		$totalPostsNum = $totalResult->num_rows;
		
		//Calculate the offset to be included in the query to allow pagination
		$offset = ($pageNo - 1) * $postsPerPage;
		
		//Store the result set after querying for all the posts in the database with settings applied
		$postResult = "";
		
		//Check if the user wants to filter the post based on the location
		if($locationFilter != NULL) {
			
			//Get the post information
			$postResult = $db->queryDB(("SELECT * FROM posts WHERE id IN (SELECT post_id FROM post_locations WHERE country LIKE '". $filterCountry ."' AND state LIKE '". $filterState ."' AND city LIKE '". $filterCity ."') ORDER BY ". $sortData['sortBy'] ." ". strtoupper($sortData['sortOrder']) ."". ($postFilter == "true" ? " AND user_id='". $userID ."'" : "") ." LIMIT ". $offset .", ". $postsPerPage .""));
			
		} else {
			
			//Get the post information
			$postResult = $db->queryDB(("SELECT * FROM posts". ($postFilter == "true" ? " WHERE user_id='". $userID ."'" : "") ." ORDER BY ". $sortData['sortBy'] ." ". strtoupper($sortData['sortOrder']) ." LIMIT ". $offset .", ". $postsPerPage .""));
		}
		
		//Empty sting to concatenate the HTML output
		$content = "";
		
		//Check if the exists at least one post
		if($totalPostsNum != 0) {
			
			//Pagination control panel
			$content .= '<p>Pagination (Showing '. $postsPerPage .' Posts Per Page):</p>';
			$content .= '<p>Total Posts: '. $totalPostsNum .'</p>';
			$content .= '<ul class="pagination">';
				if($pageNo > 1) {
					$content .= '<li class="page-item"><a class="page-link" onclick="previousPage()">Previous</a></li>';
				} else {
					$content .= '<li class="page-item disabled"><a class="page-link" onclick="previousPage()">Previous</a></li>';
				}
				
				$content .= '<li class="page-item disabled"><a class="page-link">Page '. $pageNo .'</a></li>';
				
				if($postsPerPage * $pageNo < $totalPostsNum) {
					$content .= '<li class="page-item"><a class="page-link" onclick="nextPage()">Next</a></li>';
				} else {
					$content .= '<li class="page-item disabled"><a class="page-link" onclick="nextPage()">Next</a></li>';
				}
			$content .= '</ul>';
			$content .= '<hr/>';
			
			//Timeline 
			$content .= '<ul class="timeline">';
				
				//Loop through the posts
				for($i = 0; $postRow = $postResult->fetch_assoc(); $i++) {
					
					//Variables to store the post information
					$postUserID 	= $postRow['user_id'];
					$postID 		= $postRow['id'];
					$postTitle 		= $postRow['title'];
					$postContent 	= $postRow['content'];
					$postSeverity 	= $postRow['severity'];
					$postDate 		= strtotime($postRow['date']);
					$currentDate 	= time();
					$postImage = NULL;
					
					//Check if the post contains image
					if($postRow['image'] != NULL) {
						$postImage = $postRow['image'];
					}
					
					//Get the duration of the post since it is first posted
					$duration = $currentDate - $postDate;
					
					//Get the post location
					$postLocationResult = $db->queryDB("SELECT * FROM post_locations WHERE post_id='". $postID ."' LIMIT 1");
					$postLocationRow = $postLocationResult->fetch_assoc();
					
					//Variables to store the post location
					$postCountry = $postLocationRow['country'];
					$postState 	 = $postLocationRow['state'];
					$postCity 	 = $postLocationRow['city'];
					
					//Get the post user information
					$postUserResult = $db->queryDB("SELECT full_name FROM users WHERE id='". $postUserID ."' LIMIT 1");
					$postUserRow = $postUserResult->fetch_assoc();
					
					//Get the full name of the user who posted the post
					$postUserFullName = $postUserRow['full_name'];
					
					//Get the post reaction information
					$numLikesResult = $db->queryDB("SELECT post_like FROM post_likes WHERE post_id='". $postID ."' AND post_like='1'");
					$numDislikesResult = $db->queryDB("SELECT post_like FROM post_likes WHERE post_id='". $postID ."' AND post_like='2'");
					
					//Calculate the number of likes and dislikes
					$numLikes = $numLikesResult->num_rows;
					$numDislikes = $numDislikesResult->num_rows;
					
					//Set the post background based on the number of likes to dislikes ratio
					if($numLikes > $numDislikes) {
						$color = '#F2FFF0';
					} elseif($numLikes < $numDislikes) {
						$color = '#FFF0F2';
					} else {
						$color = '#FFFFFF';
					}
					
					//Concatenate the post body
					if($i % 2 == 0) {
						$content .= '<li>';
					} else {
						$content .= '<li class="timeline-inverted">';
					}
						if($postSeverity <= 3) {
							$content .= '<div class="timeline-badge info">';
						} elseif($postSeverity <= 6) {
							$content .= '<div class="timeline-badge warning">';
						} else {
							$content .= '<div class="timeline-badge danger">';
						}
							$content .= '<p>'. $postSeverity .'</p>';
						$content .= '</div>';
						$content .= '<div class="timeline-panel" style="background-color: '. $color .';">';
							
							if($login && $userID == $postUserID) {
								$content .= '<div class="float-right" id="deletePost_'. $postID .'">';
									$content .= '<button class="btn btn-outline-danger" title="Click Twice to Delete" onclick="deletePostConfirmation('. $postID .')" id="delete_confirmation_'. $postID .'">Delete</button>';
								$content .= '</div>';
							}
							
							$content .= '<div class="timeline-heading">';
								$content .= '<h4 class="timeline-title">'. $postTitle .'</h4>';
								$content .= '<p><small class="text-muted">By: <b>'. $postUserFullName .'</b></small></p>';
								$content .= '<p><small class="text-muted">At: <b>'. $postCity .', '. $postState .', '. $postCountry .'</b></small></p>';
								$content .= '<p><small class="text-muted">Posted: <b>'. Date::secondsToWord(intval($duration)) .'</b> ago</small></p>';
							$content .= '</div>';
							$content .= '<div class="timeline-body">';
								$content .= '<hr/>';
								$content .= '<p>'. $postContent .'</p>';
								if($postImage != NULL) {
									$content .= '<br/>';
									$content .= '<img class="img-thumbnail postImage" src="data:image/jpeg;base64,'.base64_encode($postImage).'"/>';
								}
								$content .= '<hr/>';
								
								$content .= '<p><small class="text-muted"><span class="text-success">'. $numLikes .' Likes</span>, <span class="text-danger">'. $numDislikes .' Dislikes</small></p>';
								$content .= '<p><small class="text-muted">Accuracy: <b>'. number_format(($numDislikes != 0 ? ($numLikes/($numLikes + $numDislikes) * 100) : 0), 2, '.','') .'%</b></small></p><br/>';
								
								if($login) {
																	
									$reactedResult = $db->queryDB("SELECT post_like FROM post_likes WHERE post_id='". $postID ."' AND user_id='". $userID ."'");
																	
									if($reactedResult->num_rows == 1) {
										$reactedRow = $reactedResult->fetch_assoc();
										
										if($reactedRow['post_like'] == 1) {
											$content .= '<button class="btn btn-primary" onclick="reactPost('. $postID .', \'like\')" id="like_'. $postID .'">Liked</button>&nbsp;';
											$content .= '<button class="btn btn-danger" onclick="reactPost('. $postID .', \'dislike\')" id="dislike_'. $postID .'">Dislike</button>';
										} else {
											$content .= '<button class="btn btn-primary" onclick="reactPost('. $postID .', \'like\')" id="like_'. $postID .'">Like</button>&nbsp;';
											$content .= '<button class="btn btn-danger" onclick="reactPost('. $postID .', \'dislike\')" id="dislike_'. $postID .'">Disliked</button>';
										}
									} else {
										$content .= '<button class="btn btn-primary" onclick="reactPost('. $postID .', \'like\')" id="like_'. $postID .'">Like</button>&nbsp;';
										$content .= '<button class="btn btn-danger" onclick="reactPost('. $postID .', \'dislike\')" id="dislike_'. $postID .'">Dislike</button>';
									}
									
								} else {
									$content .= '<p><small class="text-muted">Please login to react to the posts</small></p>';
								}
								
							$content .= '</div>';
						$content .= '</div>';
					$content .= '</li>';
				}
				
			$content .= '</ul>';
			
			$content .= '<hr/>';
			
			//Pagination control panel
			$content .= '<p>Pagination (Showing '. $postsPerPage .' Posts Per Page):</p>';
			$content .= '<p>Total Posts: '. $totalPostsNum .'</p>';
			$content .= '<ul class="pagination">';
				if($pageNo > 1) {
					$content .= '<li class="page-item"><a class="page-link" onclick="previousPage()">Previous</a></li>';
				} else {
					$content .= '<li class="page-item disabled"><a class="page-link" onclick="previousPage()">Previous</a></li>';
				}
				
				$content .= '<li class="page-item disabled"><a class="page-link">Page '. $pageNo .'</a></li>';
				
				if($postsPerPage * $pageNo < $totalPostsNum) {
					$content .= '<li class="page-item"><a class="page-link" onclick="nextPage()">Next</a></li>';
				} else {
					$content .= '<li class="page-item disabled"><a class="page-link" onclick="nextPage()">Next</a></li>';
				}
			$content .= '</ul>';
			
		} else {
			
			//Check is the result set empty due to filter enabled
			if($locationFilter != NULL || $postFilter == "true") {
				
				//Shows that the result set is empty
				$content .= '<ul class="timeline">';
					$content .= '<li>';
						$content .= '<div class="timeline-badge"></div>';
						$content .= '<div class="timeline-panel">';
							$content .= '<div class="timeline-heading">';
								$content .= '<h4 class="timeline-title">No Post Found</h4>';
								$content .= '<p><small class="text-muted"></small></p>';
							$content .= '</div>';
							$content .= '<div class="timeline-body">';
								if($locationFilter != NULL) {
									$content .= '<p>Filter By Location <b>Enabled</b></p>';
								} else {
									$content .= '<p>Filter By Location <b>Disabled</b></p>';
								}
								if($postFilter == "true") {
									$content .= '<p>Show Only My Posts <b>Enabled</b></p>';
								} else {
									$content .= '<p>Show Only My Posts <b>Disabled</b></p>';
								}
								$content .= '<hr/>';
								$content .= '<p>Please Change the Settings to View More Posts</p>';
							$content .= '</div>';
						$content .= '</div>';
					$content .= '</li>';
				$content .= '</ul>';
				
			} else {
				
				//There is no post at all in the database
				$content .= '<ul class="timeline">';
					$content .= '<li>';
						$content .= '<div class="timeline-badge"></div>';
						$content .= '<div class="timeline-panel" style="background-color: #FFF;">';
							$content .= '<div class="timeline-heading">';
								$content .= '<h4 class="timeline-title">No Post Available</h4>';
								$content .= '<p><small class="text-muted"></small></p>';
							$content .= '</div>';
							$content .= '<div class="timeline-body">';
								$content .= '<p>Add Some Posts!</p>';
								$content .= '<hr/>';
								$content .= '<a class="btn btn-primary" href="addPost.php">Add Post</a>';
							$content .= '</div>';
						$content .= '</div>';
					$content .= '</li>';
				$content .= '</ul>';
			}
		}
		
		$db->disconnectDB();
		
		//Return the HTML post contents to the caller function
		return $content;
	}
	
	public static function deletePost($postID, $userID) {
		
		//Open a connection to the database
		$db = new Database(WebsiteData::$databaseServer, WebsiteData::$databaseUsername, WebsiteData::$databasePassword, WebsiteData::$databaseName);
		$db->connectDB();
		
		$deletePostReaction = $db->queryDB("DELETE FROM post_likes WHERE post_id='". $postID ."' AND user_id='". $userID ."'");
		$deletePostLocation = $db->queryDB("DELETE FROM post_locations WHERE post_id='". $postID ."'");
		$deletePost = $db->queryDB("DELETE FROM posts WHERE id='". $postID ."' AND user_id='". $userID ."'");
		
		$db->disconnectDB();
		
		return ($deletePostReaction && $deletePostLocation && $deletePost);
	}
}
?>