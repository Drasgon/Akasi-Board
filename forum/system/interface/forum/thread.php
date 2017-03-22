<?php
/*
Copyright (C) 2015  Alexander Bretzke

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

	$main->useFile('./system/interface/forum/thread_buttons.php');

$subMsgImage = '';
$subMsgTitle = '';
$pageErrorMsg = 'Sie versuchen auf eine ungültige Seite zuzugreifen.';

echo '<div class="threadCon">';

setlocale(LC_ALL, null);
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

if ((isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) && isset($_SESSION['ID'])) {
      $checkIconSettings = $db->query("SELECT emoticons FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
      if ($iconSettings = mysqli_fetch_object($checkIconSettings)) {
            $emoticons = $iconSettings->emoticons;
      }
      if ($emoticons == '1') {
            $main->useFile('./system/controller/processors/icon_parser_processor.php');
      }
}

$_SESSION['ID'] = session_id();
$currentThread   = mysqli_real_escape_string($GLOBALS['connection'], $_GET['threadID']);


/*######
#		|- Page system START -|
*/ ######

	$pageNo = (!isset($_GET['pageNo'])) ? '1' : $_GET['pageNo'];

	// Entries per page
	$perPage = $main->serverConfig('thread_entries_per_page');
	$page    = (isset($pageNo)) ? '' . $pageNo . '' : '1';

	// Calculate the column, which will be displayed first
	$start = $page * $perPage - $perPage;


	$prevPage = $pageNo - 1;
	$nextPage = $pageNo + 1;

/*######
#		|- Page system END -|
*/ ######

if ((!isset($_GET['form']) || !$_GET['form'] == 'postAdd' || $_GET['form'] == 'postEdit') && (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'form=postEdit') != '1') && strpos($_SERVER['HTTP_REFERER'], 'form=postAdd') != '1') {
	if(!isset($_SESSION['lastVisitedThread'][$currentThread]) || (isset($_SESSION['lastVisitedThread'][$currentThread]) && (time() - $_SESSION['lastVisitedThread'][$currentThread][0] >= 120)))
	{
      $db->query("UPDATE $db->table_thread SET views=views+1 WHERE id=('" . $currentThread . "')");
	  $_SESSION['lastVisitedThread'][$currentThread][0] = time();
	}
}
if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
      $checkforrequest = $db->query("SELECT request_id FROM $db->table_msg_request WHERE user_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')) AND thread_id=('" . $currentThread . "')");
      
      if (mysqli_num_rows($checkforrequest) == 0) {
            $db->query("INSERT INTO $db->table_msg_request (user_id,thread_id) VALUES ((SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')),('" . $currentThread . "'))");
      }
}

$get_threadData   = "SELECT id, icon_id, main_forum_id, title, rating, rating_votes, closed FROM $db->table_thread WHERE id=('" . $currentThread . "')";
$threadDataResult = $db->query($get_threadData);

if (mysqli_num_rows($threadDataResult) < 1) {
      $deprecatedLink = "Sie haben einen ungültigen oder nicht mehr gültigen Link aufgerufen.";
      $main->throwError($deprecatedLink);
      $threadExists = false;
} else {
      $threadExists = true;
      
      
      /* SUBSCRIPTION SYSTEM START */
      
      $subMsgTitle           = '';
      $checkSubStatusbyQuery = $db->query("SELECT sub_id FROM $db->table_subdata WHERE user_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')) AND thread_id=('" . $currentThread . "')");
      if (mysqli_num_rows($checkSubStatusbyQuery) == 0) {
            $subMsgTitle = 'Abonnieren';
            $subMsgImage = './images/icons/thread_sub.png';
            if (isset($_GET['action']) && $_GET['action'] == 'threadSub') {
                  $executeSub = $db->query("INSERT INTO $db->table_subdata (user_id,thread_id) VALUES ((SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')),('" . $currentThread . "'))") or die(mysqli_error($connection));
                  if ($executeSub == true) {
                        $subChangeStatus = true;
                        $subscrString    = 'Das Thema wurde erfolgreich Abonniert!<br>Sie erhalten ab jetzt Echtzeit Benachrichtigungen, sobald es einen neuen Beitrag in diesem gibt.';
                  }
            }
      } else {
            $subMsgTitle = 'Deabonnieren';
            $subMsgImage = './images/icons/thread_unsub.png';
            if (isset($_GET['action']) && $_GET['action'] == 'threadSub') {
                  $executeUnsub = $db->query("DELETE FROM $db->table_subdata WHERE user_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')) AND thread_id=('" . $currentThread . "')") or die(mysqli_error($connection));
                  if ($executeUnsub == true) {
                        $subChangeStatus = true;
                        $subscrString    = 'Das Thema wurde erfolgreich Deabonniert!';
                  }
            }
      }
      
      /* SUBSCRIPTION SYSTEM END */
      
      if (isset($_GET['action']) && $_GET['action'] == 'threadSub') {
            if (isset($subChangeStatus) && $subChangeStatus == true) {
                  $main->useFile('./system/interface/successpage.php');
                  throwSuccess($subscrString);
            }
      } else {
            
            
            while ($threadData = mysqli_fetch_object($threadDataResult)) {
                  $threadID          = $threadData->id;
                  $threadIcID        = $threadData->icon_id;
                  $threadTitle       = $threadData->title;
                  $threadRating      = $threadData->rating;
                  $threadRatingVotes = $threadData->rating_votes;
                  $threadClosed      = $threadData->closed;
				  $boardID			 = $threadData->main_forum_id;
                  
                  $thread_rating_ = '';
                  $voting         = '0';
				  
				  
				if($main->checkBoardPermission($boardID, 0, 'Sie haben leider nicht die notwendigen Zugriffsrechte, um diese Seite zu besuchen.', '?page=Index') == false)
				{
					return;
				}
				
                  
                  if ($threadRatingVotes != '0' && $threadRating != '0') {
                        $threads_rating_calc   = $threadRating / $threadRatingVotes;
                        $threads_rating_calced = round($threads_rating_calc, 0, PHP_ROUND_HALF_DOWN);
                        
                        
                        for ($i = 0; $i <= 4; $i++) {
                              if ($i < $threads_rating_calced) {
                                    
                                    $thread_rating_ .= '<div class="icons_small" id="rating"></div>';
                                    $voting++;
                              } else {
                                    
                                    $thread_rating_ .= '<div class="icons_small" id="rating_0"></div>';
                              }
                        }
                        
                  } else {
                        
                        for ($i = 0; $i <= 4; $i++) {
                              $thread_rating_ .= '<div class="icons_small" id="rating_0"></div>';
                        }
                        
                  }
                  
                  
                  if ($threadClosed == 1) {
                        $threadIcon = "./images/icons/thread_closed.png";
                  }
                  
                    if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
						
							$_SESSION['ID'] = session_id();
                              $getUser        = $db->query("SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')");
                              while ($userResult = mysqli_fetch_object($getUser)) {
                                    $userID = $userResult->id;
                              }
                              
                              $secureThreadID = $threadID;
                              $secureUserID   = mysqli_real_escape_string($GLOBALS['connection'], $userID);
                              $statusQuery    = $db->query("SELECT account_id, thread_id, board_id FROM $db->table_forum_read WHERE account_id=('" . $secureUserID . "') AND thread_id=('" . $secureThreadID . "')");
                              
                              if (mysqli_num_rows($statusQuery) == 1) {
                                    $unread_status = false;
                              } else {
                                    $db->query("INSERT INTO $db->table_forum_read (account_id,thread_id,board_id) VALUES ('" . $secureUserID . "', '" . $secureThreadID . "', (SELECT main_forum_id FROM $db->table_thread WHERE id=('" . $secureThreadID . "')))");
                                    $unread_status = true;
                              }
						
						if ($threadClosed == 0) {
                              
                              if ($unread_status == false) {
                                    $threadIcon = './images/icons/thread_def.png';
                              } else {
                                    $threadIcon = './images/icons/thread_new.png';
                              }
                        } else {
                              $threadIcon = './images/icons/thread_def.png';
                        }
                  }
                  
                  $headLineContent = '';
                  $headLineContent .= '
			
					<div class="mainHeadline">
						<div class="threadTitleContainer">
							<div class="headlineContainer">
								<h2>
									<span class="prefix">
										<strong>
										</strong>
									</span>
									<div class="icons" id="threadicon"></div>
									<a href="?page=Index&amp;threadID=' . $currentThread . '">
										' . $threadTitle . '
									</a>
								</h2>
								<p>
									<div title="' . $voting . '/5 Sterne(n) bei ' . $threadRatingVotes . ' Bewertungen" class="threadRating">' . $thread_rating_ . '</div>
								</p>
							</div>
						</div>

					<div class="contentHeader">';
                  
				  
                  // Calculate function
                  if (isset($_GET['threadID'])) {
                        $get_max_pages = $db->query("SELECT COUNT(*) FROM $db->table_thread_posts WHERE thread_id=('" . $currentThread . "')");
                        
                        $total_rows     = mysqli_fetch_row($get_max_pages);
                        $total_rows     = $total_rows[0];
                        $max_pages_calc = $total_rows / $perPage;
                        $page_value_UR  = ceil($max_pages_calc);
                        
                        $new_page_min = $pageNo - 3;
                        $new_page_max = $pageNo + 3;
                        
                        if ($new_page_max > $page_value_UR)
                              $new_page_max = $page_value_UR;
                        
                        
                        // Prevent page lower than 1
                        if ($new_page_min < 1) {
                              $new_page_min = 1;
                        }
                        
						if(!isset($_GET['form']) || !(isset($_GET['form']) && $_GET['form'] == 'postAdd'))
						{
						  
							/*######
							#		|- Page system display START -|
							*/ ######
							
							if($pageNo > $page_value_UR)
								$main->throwError($pageErrorMsg);
							else {
							
								if ($page_value_UR > 1) {
									  $headLineContent .= '
										<div class="pageNavigation">
										<ul>';
									  if ($pageNo > 1) {
											$headLineContent .= '
												<a href="?page=Index&amp;threadID=' . $currentThread . '&amp;pageNo=' . $prevPage . '" title="vorherige Seite">
													<li class="skip skipActive">
														<img src="./images/3Dart/prevPage_2.png" alt="">
													</li>
												</a>';
									  } else {
											$headLineContent .= '
												<li class="skip">
													<img src="./images/3Dart/prevPageDis_2.png" alt="">
												</li>';
									  }
									  
									  // Generate page buttons
									  
									  if($pageNo != 1 && $pageNo - 4 >= 1)
									  {
										parse_str($main->getURI(), $vals);
										$vals['pageNo'] = 1;
									  
										$headLineContent .= '
											<a href="?page=Index&amp;threadID=' . $currentThread . '&amp;pageNo=' . $i . '">
												<li class="navigationActive">
													1
												</li>
											</a>
											<li class="disabled">
												<span>
													...
												</span>
											</li>';
									  }
											
									  for ($i = $new_page_min; $i <= $new_page_max; $i++) {
											if ($i == $pageNo) {
												  $headLineContent .= '
													<li class="active">
														<span>
															' . $i . '
														</span>
													</li>';
											} else {
												  parse_str($main->getURI(), $vals);
												  $vals['pageNo'] = $i;
													$headLineContent .= '
														<a href="?page=Index&amp;threadID=' . $currentThread . '&amp;pageNo=' . $i . '">
															<li class="navigationActive">
																' . $i . '
															</li>
														</a>';
											}
									  }
									  
									  if($pageNo != $page_value_UR && $pageNo + 4 <= $page_value_UR)
									  {
										parse_str($main->getURI(), $vals);
										$vals['pageNo'] = $page_value_UR;
										$fixed_query    = http_build_query($vals);
										$newURL         = str_replace("%2F%3F", "?", $fixed_query);
									  
										$headLineContent .= '
											<li class="disabled">
												<span>
													...
												</span>
											</li>
											<a href="'.$newURL.'">
												<li class="navigationActive">
													'.$page_value_UR.'
												</li>
											</a>';
									  }
									  
									  if ($pageNo < $page_value_UR) {
										$headLineContent .= '	
											<a href="?page=Index&amp;threadID=' . $currentThread . '&amp;pageNo=' . $nextPage . '" title="nächste Seite">
												<li class="skip skipActive">
													<img src="./images/3Dart/nextPage_2.png" alt="">
												</li>
											</a>';
									  } else {
										$headLineContent .= '
											<li class="skip">
												<img src="./images/3Dart/nextPageDis_2.png" alt="">
											</li>';
									  }
										$headLineContent .= '	
												</ul>
											</div>';
								}
							}
							/*######
							#		|- Page system display END -|
							*/ ######
                        }
                  }

				  if($page_value_UR - $pageNo >= 0)
				  {
					if((!isset($_GET['form']) || !(isset($_GET['form']) && $_GET['form'] == 'postAdd')) && $threadClosed == 0)
					{
                  
						$headLineContent .= generateButtons($currentThread, $subMsgTitle, $subMsgImage, $threadExists);
					}
				  
                  $headLineContent .= '
							</div>
						</div>
					<div class="threadMain">';
				}
				
            }
            
            if($page_value_UR - $pageNo >= 0)
			{
            
            echo $headLineContent;
            if (!isset($_GET['form']) || (isset($_GET['action']) && $_GET['action'] == 'lastPost')) {
                  $virtual_postID   = '0';
                  $get_threadData   = "SELECT id, author_id, date_posted, date_edited, text FROM $db->table_thread_posts WHERE thread_id='" . $currentThread . "'  LIMIT $start, $perPage";
                  $threadDataResult = $db->query($get_threadData);
                  while ($threadData = mysqli_fetch_object($threadDataResult)) {
							$postID     = $threadData->id;
							$authorID   = $threadData->author_id;
							$datePosted = $main->convertTime($threadData->date_posted);
							$dateEdited = $main->convertTime($threadData->date_edited);
							
							
							$postText = $threadData->text;
                        if (isset($emoticons) && $emoticons == '1') {
                              $postTextParsed = emoticons($postText);
                        } else {
                              $postTextParsed = $postText;
                        }
                        
                        $virtual_postID++;
						
						if($authorID != 0)
						{
							$guestState = FALSE;
							
							$authorInfo       = $main->getUserdata($authorID, "account_id");
						}
						else
							$guestState = TRUE;
						
						if(!$guestState && $authorInfo['accepted'] == 1)
						{
							$author           = $authorInfo['name'];
							$authorGender     = $authorInfo['gender'];
							$authorAvatar     = $authorInfo['avatar'];
							$author_postCount = $authorInfo['posts'];
							$author_title     = $authorInfo['title'];
							$author_signature = $authorInfo['signature'];
							$memberAvatar_border = $authorInfo['avatar_border'];
							
							$authorLink = ARRAY('<a href="?page=Profile&amp;User=' . $authorID . '" title="Profil von ' . $author . ' aufrufen.">', '</a>', 'title="Profil von ' . $author . ' aufrufen"');
							$authorPostsLink = ARRAY('<a href="?page=Profile&amp;User=' . $authorID . '&amp;tab=posts">', '</a>');

							$get_authorData = "SELECT location, about FROM $db->table_profile WHERE id='" . $authorID . "'";
							$authorData     = $db->query($get_authorData);
							if ($fetch_authorData = mysqli_fetch_object($authorData))
								  $location = $fetch_authorData->location;
							
						}
						else
						{
							$author           = $authorInfo['name'];
							$authorGender     = $authorInfo['gender'];
							$authorAvatar     = $authorInfo['avatar'];
							$author_postCount = $authorInfo['posts'];
							$author_title     = $authorInfo['title'];
							$author_signature = $authorInfo['signature'];
							$memberAvatar_border = $authorInfo['avatar_border'];
							$authorLink = ARRAY('', '', '');
							$authorPostsLink = ARRAY('', '');
						}
					
						unset($authorLocation);
						unset($location);
                        
                        if (isset($location) && !empty($location))
                            $authorLocation = 'Wohnort: ' . $location;
						else
							$authorLocation = '';
                        
                        if (!$main->checkImage($authorAvatar) || empty($authorAvatar)) {
                              $authorAvatar = $main->getDefaultAvatar();
                        }
                        
                        $image_info = getimagesize($authorAvatar);
                        $image_type = $image_info[2];
                        
                        if ($image_type == IMAGETYPE_PNG) {
                              $pngClass = 'avatar_png';
                        } else {
                              $pngClass = '';
                        }
                        
                        $getOnline = $db->query("SELECT online FROM $db->table_sessions WHERE id=('" . $authorID . "')");
							$online = mysqli_fetch_object($getOnline);
                              $author_status = $online->online;
                        
                        
                        switch ($authorGender) {
                              
                              case 1:
                                    $authorGenderImg = './images/icons/undefinedGender.png';
                                    $authorGenderMsg = 'hat kein Geschlecht angegeben';
                                    break;
                              case 2:
                                    $authorGenderImg = './images/icons/female.png';
                                    $authorGenderMsg = 'ist weiblich';
                                    break;
                              case 3:
                                    $authorGenderImg = './images/icons/male.png';
                                    $authorGenderMsg = 'ist männlich';
                                    break;
                        }
                        
						if(!$guestState)
						{
							if ($author_status == 0) {
								  $authorStatusMsg = 'offline';
								  $authorStatusMsgIcon = 'red';
							} else {
								  $authorStatusMsg = 'online';
								  $authorStatusMsgIcon = 'green';
							}

							$onlineStatus = '<div class="'.$authorStatusMsgIcon.'_circle_small" title="'.$author.' ist grade '.$authorStatusMsg.'."></div>';
						}
						else
						{
							$authorStatusMsg = '';
							$onlineStatus = '';
						}
							
                        
                        if (!empty($author_signature)) {
                              $signature = '
								<div class="signature">
									<div>
										' . $author_signature . '
									</div>
								</div>';
					
                        } else {
                              $signature = '';
                        }
						
						if (!empty($dateEdited) && $dateEdited != NULL) {
                              $postEdited = '
								<div class="msgEdited">
									<div>
										Zuletzt bearbeitet: ' . $dateEdited . '
									</div>
								</div>';
					
                        } else {
                              $postEdited = '';
                        }
						
						$rank = $main->calculateRank(0, 0, $authorID);
						
                        
                        $postRowData       = '

	<div class="postRow clearfix" id="postRow' . $postID . '">
		<div class="UserSidebar">
			<div class="msgAuthor">
				<p class="usernameMsg">
					'.$onlineStatus.'
					'.$authorLink[0].'
						<span>
							' . $author . '
						</span>
					'.$authorLink[1].'
				</p>
				<p class="userTitle">
					' . $author_title . '
				</p>
				<p class="userRank" title="'.$rank[2].', Rang '.$rank[0].'">
					'.$rank[1].'
				</p>
			</div>
			<div class="userAvatar">
				<div class="UserAvatarMsg">
					'.$authorLink[0].'<img src="' . $authorAvatar . '" '.$authorLink[2].' class="' . $pngClass . ' img-zoom" style="border:5px solid rgba('.$memberAvatar_border.')">'.$authorLink[1].'
				</div>
			</div>
			<div class="userIcons">
				<img src="' . $authorGenderImg . '" title="' . $author . ' ' . $authorGenderMsg . '.">
			</div>
			<div class="userCredits">
				<p>
					'.$authorPostsLink[0].'
						Beiträge: ' . $author_postCount . '
					'.$authorPostsLink[1].'
				</p>
				<p>
				' . $authorLocation . '
				</p>
			</div>
			<div class="userMessenger">
			</div>
		</div>
		<div class="contentMsg">
			<div class="msgHeader">
				<div class="msgIcon">
					<img src="./images/icons/threadAdd-Msg.png" width="22" height="22">
				</div>
				<div class="msgDate">
					<span class="msgContentInner">
						' . $datePosted . '
					</span>
				</div>
				<div class="msgIDdisplay">
					<a href="?page=Index&threadID=' . $currentThread . '&postID=' . $virtual_postID . '" class="msgIDlink">' . $virtual_postID . '</a>
				</div>
			</div>
			<div class="MsgMainCon">
				' . $postTextParsed . '
			</div>
			' . $postEdited . '
			' . $signature . '
		<div class="Thread-postOptions">
			<ul>';
                        $checkUserIdentity = $db->query("SELECT author_id FROM $db->table_thread_posts WHERE author_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')) AND id=('" . $postID . "')");
                        if (isset($_SESSION['MODACCESS']) && $_SESSION['MODACCESS'] == true || mysqli_num_rows($checkUserIdentity) == '1') {
                              $postRowData .= '
			<a href="?page=Index&threadID=' . $currentThread . '&postID=' . $postID . '&form=postEdit">
			  <li>
				Beitrag bearbeiten
			  </li>
			  </a>';
                        }
                        
                        if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
                              
                              $postRowData .= '
							
			<form method=POST action="?page=Index&threadID=' . $_GET["threadID"] . '&form=postAdd&type=direct_quote" id="quote_form" class="quote_form">
				<input type="hidden" name="quoteID" value="' . $postID . '">
				<input type=submit value="Direkt zitieren">
				<input type=submit value="** option **" disabled="disabled">
				<input type=submit value="** option **" disabled="disabled">
			</form>
			
			<li id="quote_btn" class="quote_btn">
				Zitieren
			</li>
							
						';
                              
                        }
                        
                        $postRowData .= '
			  <a href="?page=Index&threadID=' . $currentThread . '&postID=' . $postID . '&form=postReport">
			  <li>
				Melden
			  </li>
			  </a>
			</ul>
		  </div>
		</div>
	</div>';
                        
                        echo $postRowData;
                  }
            } else {
                        
				if (isset($_GET['form']) && isset($_GET['threadID']) && isset($_GET['postID']) && $_GET['form'] == 'postEdit') {
					  
					  $main->useFile('./system/interface/forum/thread_reply.php');
					  
				}
            }
            
            
       
if(!isset($_GET['form']) || !(isset($_GET['form']) && $_GET['form'] == 'postAdd'))
{
	/*
	 *
	 *      PAGE SYSTEM DISPLAY START
	 *
	 */
	
	$threadPageFooter = '<div class="threadFooter">';
	
	if (isset($page_value_UR) && $page_value_UR > 1) {
		  $threadPageFooter .= '
			<div class="pageNavigation pageNavigationFooter">
				<ul>';
				
		  if ($pageNo > 1) {
				$threadPageFooter .= '
					<a href="?page=Index&amp;threadID=' . $currentThread . '&amp;pageNo=' . $prevPage . '" title="vorherige Seite">
						<li class="skip skipActive">
							<img src="./images/3Dart/prevPage_2.png" alt="">
						</li>
					</a>';
					
		  } else {
				$threadPageFooter .= '
					<li class="skip">
						<img src="./images/3Dart/prevPageDis_2.png" alt="">
					</li>';
		  }
		  
		  for ($i = $new_page_min; $i <= $new_page_max; $i++) {
				if ($i == $pageNo) {
					  $threadPageFooter .= '
						<li class="active">
							<span>
								' . $i . '
							</span>
						</li>';
				} else {
					  parse_str($main->getURI(), $vals);
					  $vals['pageNo'] = $i;

					  $threadPageFooter .= '
						<a href="?page=Index&amp;threadID=' . $currentThread . '&amp;pageNo=' . $i . '">
							<li class="navigationActive">
								' . $i . '
							</li>
						</a>';
				}
		  }
		  
		  if ($pageNo < $page_value_UR) {
				$threadPageFooter .= '	
					<a href="?page=Index&amp;threadID=' . $currentThread . '&amp;pageNo=' . $nextPage . '" title="nächste Seite">
						<li class="skip skipActive">
							<img src="./images/3Dart/nextPage_2.png" alt="">
						</li>
					</a>';
					
		  } else {
				$threadPageFooter .= '
					<li class="skip">
						<img src="./images/3Dart/nextPageDis_2.png" alt="">
					</li>';
					
		  }
		  $threadPageFooter .= '	
				</ul>
			</div>
			';
	}
	
	if((!isset($_GET['form']) || !(isset($_GET['form']) && $_GET['form'] == 'postAdd')) && $threadClosed == 0)
	{
		$threadPageFooter .= generateButtons($currentThread, $subMsgTitle, $subMsgImage, $threadExists).'</div>';
	}
	
	/*
	 *
	 *      PAGE SYSTEM DISPLAY END
	 *
	 */
    
    

    
	echo $threadPageFooter;
}


	if((isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true || $main->boardConfig($main->getThreadBoardID($currentThread), "guest_posts")))
		{
			if((isset($_GET['form']) && $_GET['form'] != 'postEdit') || !isset($_GET['form']))
			{
				$content = '';
				
				if (isset($_GET['action']) && $_GET['action'] == 'postAdd')
				{
							$main->useFile('./system/controller/board_controller/board_add_reply.php');
							
							$content = addReply();
				}

					if (isset($_GET['form']) && $_GET['form'] == 'postAdd')
						$class = 'post-addContainer-full';
					else
						$class = 'post-addContainer-fast';
					  
					  $threadContainer = '
						  <a name="replyAdd"></a>
						  <div class="'.$class.'">
							<h1 class="fancy_font reply_add_header">
							  Einen neuen Beitrag erstellen
						  </h1>
						  <fieldset class="PostAddLegend">
							<legend>
							  Beitrag
							</legend>
							<form method="POST" action="?page=Index&threadID=' . $_GET["threadID"] . '&form=postAdd&action=postAdd" class="postAddForm" id="postAddForm">';
					  
					  
					  if (isset($_GET['token']) && !empty($_GET['token'])) {
							$token = mysqli_real_escape_string($GLOBALS['connection'], $_GET['token']);
							$query = $db->query("SELECT content FROM $db->table_post_saves WHERE token = ('" . $token . "') AND user_id = (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
							
							$results = mysqli_fetch_object($query);
								  $content = $results->content;
					  }
					  if (!isset($_GET['token']) && !isset($_GET['action']))
							$content = '';
					  
					  $threadContainer .= '
						<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
						  <textarea type="hidden" id="postAddArea" name="postAddArea"> 
							' . $content . '
						  </textarea>
							<script>tinymce.init({ 
								skin_url: "css/tinymce",
								skin: "charcoal",
								language_url : "lang/tinymce/de.js",
								language: "de",
								selector:"#postAddArea",
								plugins: [
								"autoresize advlist autolink lists link image charmap print preview hr anchor pagebreak",
								"searchreplace wordcount visualblocks visualchars code fullscreen",
								"insertdatetime media nonbreaking save table contextmenu directionality",
								"emoticons template paste textcolor colorpicker textpattern imagetools codesample"
							  ],
								toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
								  toolbar2: "print preview media | forecolor backcolor fontsizeselect emoticons | codesample",
								  image_advtab: true,
								autoresize_min_height: 350,
								autoresize_max_height: 550
								
							});</script>
							<script src="./javascript/post_save.js"></script>
						  <div class="submitPost" style="margin:30px 0;">
							<input type="submit" value="Absenden" id="postAddSubmitBtn">
							<input type="reset" value="Zurücksetzen" id="postAddResetBtn">
						  </div>
							 </form>';
					  
					  

					  $threadContainer .= '
						 </span>
						 <span id="PostAddResponse_Success" class="responseSuccess">
						 </span>
							 
						 <div class="changeInformation">	
							<p>
							Folgendes ist bei der Erstellung eines neuen Beitrags zu beachten:
							</p>
							<ul>
								<li>
								Es sind jegliche Zeichen erlaubt.
								</li>
								<li>
								Sie müssen das Urherberrecht für eingefügte Bilder besitzen. Die Administration übernimmt keine Haftung für enstandene Schäden durch Zuwiderhandlung.
								</li>
								<li>
								Anstößige sowie gewaltverherrlichende oder verspottende Texte sind <b>strengstens Verboten</b>. Bei Verstoß ist mit Strafen in Form von Strafpunkten bis hin zu einer temporären oder permanenten Accountsperre zu rechnen.
								</li>
							</ul>
						</div>
							 
						  </fieldset>
						  </div>';
					  
							echo $threadContainer;

			}
		}
		else
		{
			if((isset($_GET['form']) && $_GET['form'] != 'postEdit'))
				$main->throwError("Sie haben nicht die erforderlichen Zugriffsrechte für diese Seite.", "?page=Index");
		}
            
            echo '
	</div>
	</div>';
      }
	  }
}
?>