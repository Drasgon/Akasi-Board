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

// IMPORTANT STUFF //
/*error_reporting (E_ALL);*/


// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

$boardID = (isset($_GET['boardview'])) ? mysqli_real_escape_string($GLOBALS['connection'], $_GET['boardview']) : "";
//////////////////////////////////////////////////////////////////////////////////////////////////////////


// VARIABLES //

$threads_id                 = "";
$threads_main_id            = "";
$threads_sub_icon_id        = "";
$threads_author_id          = "";
$threads_sub_closed         = "";
$threads_sub_title          = "";
$threads_sub_description    = "";
$threads_sub_views          = "";
$threads_sub_posts          = "";
$threads_sub_dateCreated_uF = "";
$threads_sub_dateCreated_F  = "";
$threads_sub_last_author_id = "";
$threads_sub_last_reply_uF  = "";
$threads_sub_last_reply_F   = "";
$threads_participant_last   = "";
$threads_author             = "";




// PAGE SYSTEM 

$pageNo = (!isset($_GET['pageNo'])) ? '1' : $_GET['pageNo'];

//	Entries per page
$perPage = 20;
$page    = (isset($pageNo)) ? '' . $pageNo . '' : '1';

//	Calculating the column which is shown first
$start = $page * $perPage - $perPage;


$prevPage = $pageNo - 1;
$nextPage = $pageNo + 1;


// PAGE SYSTEM END //

// Define icon legend

$icon_legend = '
<div class="icon_legend">
	<h3>
		Legende
	</h3>
	<ul>
		<li class="threadicons" id="thread_def">
			<p>Keine neuen Beiträge</p>
		</li>
		<li class="threadicons" id="thread_new">
			<p>Mindestens ein neuer Beitrag</p>
		</li>
		<li class="threadicons" id="thread_closed">
			<p>Thema geschlossen</p>
		</li>
	</ul>
</div>';

// Get active column //
$sortField = (isset($_GET['sortField'])) ? mysqli_real_escape_string($GLOBALS['connection'], $_GET['sortField']) : '';
$direction = (isset($_GET['direction'])) ? mysqli_real_escape_string($GLOBALS['connection'], $_GET['direction']) : 'DESC';
(isset($_GET['direction']) && $direction != "ASC" && $direction != "DESC") ? throwError("Die aktuelle URL enthält unzulässige Werte, bitte überprüfen Sie die Schreibweise und versuchen Sie es erneut.") : '';

if (isset($_GET['sortField']) && !empty($_GET['direction'])) {
    if (isset($_GET['sortField']) && $_GET['direction'] == 'DESC')
        $linkDirection = 'ASC';
    if (isset($_GET['sortField']) && $_GET['direction'] == 'ASC')
        $linkDirection = 'DESC';
}

if (!isset($_GET['sortField']) || empty($_GET['direction'])) {
    $linkDirection = 'ASC';
}

$topicClass     = (isset($_GET['sortField']) && $_GET['sortField'] == 'topic') ? 'columnTopic active' : 'columnTopic';
$ratingClass    = (isset($_GET['sortField']) && $_GET['sortField'] == 'rating') ? 'columnRating active' : 'columnRating';
$repliesClass   = (isset($_GET['sortField']) && $_GET['sortField'] == 'replies') ? 'columnReplies active' : 'columnReplies';
$viewsClass     = (isset($_GET['sortField']) && $_GET['sortField'] == 'views') ? 'columnViews active' : 'columnViews';
$lastReplyClass = (isset($_GET['sortField']) && $_GET['sortField'] == 'lastReply') ? 'columnLastPost active' : 'columnLastPost';


//////////////////////////////////////////////////////////////////////////////////////////////////////////

// QUERIES //
$get_threads = "SELECT id, main_forum_id, icon_id, title, description, author_id, closed, views, posts, date_created, rating, rating_votes, last_post_author_id, last_replyTime FROM $db->table_thread WHERE main_forum_id= ('" . $boardID . "') ";


switch ($sortField) {
    case "topic":
        $get_threads .= " ORDER BY title $direction ";
        break;
    
    case "rating":
        $get_threads .= " ORDER BY rating $direction ";
        break;
    
    case "replies":
        $get_threads .= " ORDER BY posts $direction ";
        break;
    
    case "views":
        $get_threads .= " ORDER BY views $direction";
        break;
    
    case "lastReply":
        $get_threads .= " ORDER BY last_replyTime $direction ";
        break;
    default: {
        $lastReplyClass = 'columnLastPost active';
        $get_threads .= " ORDER BY last_replyTime $direction ";
    }
        break;
}

$forum_description = $main->serverConfig("forum_description");

$get_threads .= " LIMIT $start, $perPage";
$sub_forums_result = $db->query($get_threads) or die(mysql_error());

$get_main_sel = "SELECT title, description, closed, icon_id, icon FROM $db->table_boards WHERE id=('" . $boardID . "')";

//////////////////////////////////////////////////////////////////////////////////////////////////////////

// BOARD //
$incrementalCatID = '0';
// CATERGORIES //
$board_table      = '
<div class="mainHeadline">
    <div class="icons" id="forumiconMain"></div>
    <div class="headlineContainer">
      <h1>
        Forum
      </h1>
    </div>
	<p>'.$forum_description.'</p>
</div>
<div class="board_structure">
<ul class="boardsCat">
';
$defineCat        = "SELECT id,name FROM $db->table_categories ORDER BY id ASC";
$catResult = $db->query($defineCat) or die(mysql_error());
while ($cat = mysqli_fetch_object($catResult)) {
    
    $boardCatId = $cat->id;
    if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true) {
        
        $getCatState = $db->query("SELECT state FROM $db->table_hiddenboards WHERE user_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')) AND cat_id=('" . $boardCatId . "')");
        
        if (mysqli_num_rows($getCatState) <= '0') {
            $db->query("INSERT INTO $db->table_hiddenboards (user_id, cat_id, state) VALUES ((SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')), '" . $boardCatId . "', 1)");
            $catState = '1';
        }
        
        while ($catStateProcess = mysqli_fetch_object($getCatState)) {
            $catState = $catStateProcess->state;
        }
    } else {
        $catState = '1';
    }
    
    $boardCatName = $cat->name;
    $incrementalCatID++;
    $board_table .= '
<li class="boardCat_row" id="' . $incrementalCatID . '">
<div class="catHeaderOuter" title="Kategorie ' . $boardCatName . ' schließen/öffnen.">
	<div class="catHeader">
		<div class="CatName">
			<span>
				' . $boardCatName . '
			</span>
		</div>
		<div class="lastpost_design">
				<p>Letzter Beitrag</p>
			</div>
	</div>
</div>
<ul class="board_body">
';
    if ($catState == '1') {
        
        
        $incementalCounter = '0';
        $get_forums        = "SELECT id, icon, icon_id, title, description, closed FROM $db->table_boards WHERE category=('" . $boardCatId . "')";
        $forums_result = $db->query($get_forums) or die(mysql_error());
        while ($forums = mysqli_fetch_object($forums_result)) {
		
            $lastThreadID			=	'';
            $lastThreadTitle		=	'';
            $lastThreadAuthor		=	'';
            $lastThreadReply		=	'';
            $lastThreadAuthorName	=	'';
            $lastActivity_id		=	'';
            $fetchLastThread_id		=	'';
			$num_LastThread			=	'';
			$msg					=	'';
			
			$boardUnread_status		=	false;
			
			$totalRows_threads		=	'';
			$totalRows_unreads		=	'';
			$totalUnreads			=	'';
            
            $board_id      = $forums->id;
            $board_icon    = $forums->icon;
            $board_icon_id = $forums->icon_id;
            $board_closed  = $forums->closed;
			
            
            
            if ($board_closed == 1) {
                $board_icon = "./images/icons/closed.png";
            }
            if ($board_closed == 0) {
			
                $boardUnread_status = $main->detectUnreadThreadsInBoard($board_id);
				
                if ($boardUnread_status == false) {
				
                    $board_icon       = './images/icons/default.png';
                    $board_titleClass = '';
                } else {
				
                    $board_icon       = './images/icons/new_post.png';
                    $board_titleClass = 'newPost-Board';
					$totalUnreads	  = ("$boardUnread_status");
                }
            }
			
            $board_title        = $forums->title;
            $board_description  = $forums->description;
            
			
            $fetchLastThread_id = $db->query("SELECT id FROM $db->table_thread WHERE main_forum_id=('" . $board_id . "') ORDER BY last_replyTime DESC LIMIT 1");
            while ($lastThreadActive_idNewest = mysqli_fetch_object($fetchLastThread_id)) {
                $lastActivity_id = $lastThreadActive_idNewest->id;
				
				$msg                = 'Von ';		
				
				$defineLastThread = "SELECT id,title,last_post_author_id,last_replyTime FROM $db->table_thread WHERE main_forum_id=('" . $board_id . "') AND id=('" . $lastActivity_id . "') ORDER BY last_replyTime ASC LIMIT 0,1";
            
			
            $lastThreadResult = $db->query($defineLastThread) or die(mysql_error());
            $num_LastThread = mysqli_num_rows($lastThreadResult);
			
            if ($num_LastThread >= 1) {
			
            while ($lastThreadActive = mysqli_fetch_object($lastThreadResult)) {
				
                    $lastThreadID      = $lastThreadActive->id;
                    $lastThreadTitle   = $lastThreadActive->title;
                    $lastThreadAuthor  = $lastThreadActive->last_post_author_id;
                    $lastThreadReplyuF = $lastThreadActive->last_replyTime;
                    
                    if (date('Y-m-d', $lastThreadReplyuF) == date('Y-m-d')) {
                        $lastThreadReplyF = strftime('<span class="timeRange">Heute</span>, %H:%M', $lastThreadReplyuF);
                    } elseif (date('Y-m-d', $lastThreadReplyuF) == date('Y-m-d', strtotime("Yesterday"))) {
                        $lastThreadReplyF = strftime('<span class="timeRange">Gestern</span>, %H:%M', $lastThreadReplyuF);
                    } elseif (date('Y-m-d', $lastThreadReplyuF) < date('Y-m-d', strtotime("Yesterday"))) {
                        $lastThreadReplyF = strftime("%A, %d %B %Y %H:%M", $lastThreadReplyuF);
                    }
                    $lastThreadReply = "($lastThreadReplyF)";
					
                }
                
                
                mysqli_free_result($lastThreadResult);
				
				if($lastThreadAuthor != 0) // If last author was NOT a guest.
                {
					$defineLastAuthor = "SELECT username FROM $db->table_accdata WHERE account_id=('" . $lastThreadAuthor . "')";
					$lastAuthorResult = $db->query($defineLastAuthor) or die(mysql_error());
					$lastAuthorActive = mysqli_fetch_object($lastAuthorResult);
						$lastThreadAuthorName = $lastAuthorActive->username;

					mysqli_free_result($lastAuthorResult);
				}
				else
					$lastThreadAuthorName = 'Gast';
            }
			
		}
		
		if (!$num_LastThread) {
			
                $lastThreadResult = '';
                $msg          = 'In dieser Kategorie sind noch keine Themen vorhanden';
				
        }
            
            $board_table .= '


<li>
<div class="boardList">
	<div class="innerListTitle">
		<div class="BoardListIcon">
			<div class="icons" id="forumicon"></div>
		</div>
		<div class="BoardListTitleContent">
			<h4 class="boardTitle ' . $board_titleClass . '">
				<a href="?page=Index&boardview=' . $board_id . '" title="Zum Board ' . $board_title . ' springen.">
					' . $board_title . $totalUnreads . '
				</a>
			</h4>
			<p class="desc specialP">
				' . $board_description . '
			</p>
		</div>
	</div>
	<div class="boardThreadData">
		<h4 class="boardTitle ' . $board_titleClass . '">';
            if (!$num_LastThread <= 0) {
                $board_table .= '
<a href="?page=Index&threadID=' . $lastThreadID . '" title="Zum Thread ' . $lastThreadTitle . ' springen.">
' . $lastThreadTitle . '
</a>';
            }
            $board_table .= '
</h4>
<p class="desc specialP">
' . $msg . '
<a href="?page=Profile&amp;User=' . $lastThreadAuthor . '">
' . $lastThreadAuthorName . '</a>
<span class="lastPostDateCon">  ' . $lastThreadReply . '  </span>
</p>
</div>
</div>
</li>
';
        }
    }
    $board_table .= '
</ul>
</li>
';
}
$board_table .= '
</div>
</ul>

'.$icon_legend.'
';

//////////////////////////////////////////////////////////////////////////////////////////////////////////


// THREADS //
$checkResults = "SELECT id FROM $db->table_boards WHERE id=('" . $boardID . "')";
$resultChecked = $db->query($checkResults) or die(mysql_error());
if ($boardID) {
    if (mysqli_num_rows($resultChecked) < 1) {
        $deprecatedLink = "Sie haben einen ungültigen oder nicht mehr gültigen Link aufgerufen.";
        throwError($deprecatedLink);
    } else {
        // Pre-data //
        
        $sel_main_result = $db->query($get_main_sel) or die(mysql_error());
        while ($main_board_selected = mysqli_fetch_object($sel_main_result)) {
            $board_sel_title   = $main_board_selected->title;
            $board_sel_desc    = $main_board_selected->description;
            $board_sel_icon    = $main_board_selected->icon;
            $board_sel_icon_id = $main_board_selected->icon_id;
            $board_sel_closed  = $main_board_selected->closed;
            
            if ($board_sel_closed == 1) {
                $board_sel_icon = "./images/icons/closed.png";
            } else {
                
                if ($board_sel_icon_id == 1) {
                    $board_sel_icon = "./images/icons/default.png";
                }
                if ($board_sel_icon_id == 2) {
                    $board_sel_icon = "../images/icons/new_post.png";
                }
            }
        }
        
        $sub_table = '
<div class="mainHeadline">
	<div class="headlineContainer">
		<h2>
			<span class="prefix">
				<strong>
				</strong>
			</span>
			<div class="icons" id="forumicon"></div>
			<a href="?page=Index&amp;boardview=' . $boardID . '">
				' . $board_sel_title . '
			</a>
		</h2>
		<p>
			' . $board_sel_desc . '
		</p>
	</div>
</div>
<div class="contentHeader">
';
        
        // Calculate function
        if (isset($_GET['boardview'])) {
            $get_max_pages = $db->query("SELECT COUNT(*) FROM $db->table_thread WHERE main_forum_id=('" . $boardID . "')");
            
            $total_rows     = mysqli_fetch_row($get_max_pages);
            $total_rows     = $total_rows[0];
            $max_pages_calc = $total_rows / $perPage;
            $page_value_UR  = ceil($max_pages_calc);
            
            
            if ($page_value_UR > 1) {
                $sub_table .= '
<div class="pageNavigation">
<ul>
<li class="skip">
';
                if ($pageNo > 1) {
                    $sub_table .= '	
<a href="?page=Board&amp;boardview=' . $boardID . '&amp;pageNo=' . $prevPage . '" title="vorherige Seite">
<img src="./images/3Dart/prevPage.png" alt="">
</li>
';
                } else {
                    $sub_table .= '	
<img src="./images/3Dart/prevPageDis.png" alt="">
</li>
';
                }
                
                
                for ($i = 1; $i <= $page_value_UR; $i++) {
                    if ($i == $pageNo) {
                        $sub_table .= '
<li class="active">
<span>
' . $i . '
</span>
</li>
';
                    } else {
                        parse_str($main->getURI(), $vals);
                        $vals['pageNo'] = $i;
                        $fixed_query    = http_build_query($vals);
                        $newURL         = str_replace("%2F%3F", "?", $fixed_query);
                        $sub_table .= '
<li>
<a href="' . $newURL . '">
' . $i . '
</a>
</li>
';
                    }
                }
                $sub_table .= '		
<li class="skip">
';
                if ($pageNo < $page_value_UR) {
                    $sub_table .= '	
<a href="?page=Board&amp;boardview=' . $boardID . '&amp;pageNo=' . $nextPage . '" title="nächste Seite">
<img src="./images/3Dart/nextPage.png" alt="">
</a>
</li>
';
                } else {
                    $sub_table .= '	
<img src="./images/3Dart/nextPageDis.png" alt="">
</a>
</li>
';
                }
                $sub_table .= '	
</ul>
</div>
';
            }
        }
        $sub_table .= '

<div class="largeButtons">
<ul>
<li>
<a href="?page=Index&boardview=' . $_GET['boardview'] . '&form=threadAdd" title="Thema erstellen">
<div class="icons" id="new_thread_icon"></div>

<span>
Thema erstellen
</span>
</a>
</li>
</ul>
</div>
</div>';

if($total_rows >= 1)
{

$sub_table .= '
<table class="tableList">
<thead>
<tr class="tableHead">
<th colspan="2" class="' . $topicClass . '">
<div>
<a href="?page=Index&boardview=' . $boardID . '&sortField=topic&direction=' . $linkDirection . '">
Thema		  	  
<div class="lP_descSort">
</div>

</a>
</div>
</th>
<th class="' . $ratingClass . '">
<div>
<a href="?page=Index&boardview=' . $boardID . '&sortField=rating&direction=' . $linkDirection . '">
Nutzerbewertung	  
<div class="lP_descSort">
</div>

</a>
</div>
</th>
<th class="' . $repliesClass . '">
<div>
<a href="?page=Index&boardview=' . $boardID . '&sortField=replies&direction=' . $linkDirection . '">
Antworten	  
<div class="lP_descSort">
</div>

</a>
</div>
</th>
<th class="' . $viewsClass . '">
<div>
<a href="?page=Index&boardview=' . $boardID . '&sortField=views&direction=' . $linkDirection . '">
Ansichten
<div class="lP_descSort">
</div>

</a>
</div>
</th>
<th class="' . $lastReplyClass . '">
<div>
<a href="?page=Index&boardview=' . $boardID . '&sortField=lastReply&direction=' . $linkDirection . '">
Letzte Antwort 
<div class="lP_descSort">
</div>

</a>
</div>
</th>
</tr>
</thead>
<tbody>
';
        
        
        while ($sub_forums = mysqli_fetch_object($sub_forums_result)) {
            $threads_id                 = $sub_forums->id;
            $threads_main_id            = $sub_forums->main_forum_id;
            $threads_sub_icon_id        = $sub_forums->icon_id;
            $threads_author_id          = $sub_forums->author_id;
            $threads_sub_closed         = $sub_forums->closed;
            $threads_sub_title          = $sub_forums->title;
            $threads_sub_description    = $sub_forums->description;
            $threads_sub_views          = $sub_forums->views;
            $threads_sub_posts          = $sub_forums->posts;
            $threads_sub_dateCreated_uF = $sub_forums->date_created;
            
            $actualTime = time();
            if (date('Y-m-d', $threads_sub_dateCreated_uF) == date('Y-m-d')) {
                $lastThreadReplyF = strftime('<span class="timeRange">Heute</span>, %H:%M', $threads_sub_dateCreated_uF);
            } elseif (date('Y-m-d', $threads_sub_dateCreated_uF) == date('Y-m-d', strtotime("Yesterday"))) {
                $lastThreadReplyF = strftime('<span class="timeRange">Gestern</span>, %H:%M', $threads_sub_dateCreated_uF);
            } elseif (date('Y-m-d', $threads_sub_dateCreated_uF) <= date('Y-m-d', strtotime("Yesterday"))) {
                $lastThreadReplyF = strftime("%A, %d %B %Y %H:%M", $threads_sub_dateCreated_uF);
            }
            
            $threads_sub_last_author_id = $sub_forums->last_post_author_id;
            $threads_sub_last_reply_uF  = $sub_forums->last_replyTime;
            $actualTime                 = time();
            if (date('Y-m-d', $threads_sub_last_reply_uF) == date('Y-m-d')) {
                $threads_sub_last_reply_F = strftime('<span class="timeRange">Heute</span>, %H:%M', $threads_sub_last_reply_uF);
            } elseif (date('Y-m-d', $threads_sub_last_reply_uF) == date('Y-m-d', strtotime("Yesterday"))) {
                $threads_sub_last_reply_F = strftime('<span class="timeRange">Gestern</span>, %H:%M', $threads_sub_last_reply_uF);
            } elseif (date('Y-m-d', $threads_sub_last_reply_uF) <= date('Y-m-d', strtotime("Yesterday"))) {
                $threads_sub_last_reply_F = strftime("%A, %d %B %Y %H:%M", $threads_sub_last_reply_uF);
            }
            
            $threads_rating       = $sub_forums->rating;
            $threads_rating_votes = $sub_forums->rating_votes;
            
			$thread_rating_ = '';
			
            if ($threads_rating_votes != '0' && $threads_rating != '0') {
            $threads_rating_calc   = $threads_rating / $threads_rating_votes;
            $threads_rating_calced = round($threads_rating_calc, 0, PHP_ROUND_HALF_DOWN);
			
			for($i = 0; $i <= 4; $i++)
			{
				if($i < $threads_rating_calced) {
				
				$thread_rating_ .= '<div class="icons_small" id="rating"></div>';
				} else {
				
				$thread_rating_ .= '<div class="icons_small" id="rating_0"></div>';
				}
			}
			
			} else {
		
				for($i = 0; $i <= 4; $i++)
				{
					$thread_rating_ .= '<div class="icons_small" id="rating_0"></div>';
				}
			
			}
            
			
            $get_author    = "SELECT username FROM $db->table_accdata WHERE account_id='" . $threads_author_id . "'";
            $author_result = $db->query($get_author);
            $threads_author_fetch = mysqli_fetch_object($author_result);
                $threads_author = $threads_author_fetch->username;

            
			if($threads_sub_last_author_id != 0)
			{
				$get_last_author    = "SELECT username FROM $db->table_accdata WHERE account_id='" . $threads_sub_last_author_id . "'";
				$last_author_result = $db->query($get_last_author);
				$last_author_fetch = mysqli_fetch_object($last_author_result);
					$threads_participant_last = $last_author_fetch->username;
			}
            else
				$threads_participant_last = 'Gast';
            
            if ($threads_sub_closed == 1) {
                $threads_sub_icon      = ' class="threadicons" id="thread_closed"';
                $threads_sub_title_msg = '<font color="red">[CLOSED]</font>';
            }
            if ($threads_sub_closed == 0) {
                $threads_sub_title_msg = '';
                
                if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true) {
                    
                    $_SESSION['ID'] = session_id();
                    $threadID       = $threads_id;
                    $getUser        = $db->query("SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')");
                    while ($userResult = mysqli_fetch_object($getUser)) {
                        $userID = $userResult->id;
                    }
                    
                    $secureThreadID = mysqli_real_escape_string($GLOBALS['connection'], $threadID);
                    $secureUserID   = mysqli_real_escape_string($GLOBALS['connection'], $userID);
                    
                    $statusQuery = $db->query("SELECT account_id, thread_id, board_id FROM $db->table_forum_read WHERE account_id=('" . $secureUserID . "') AND thread_id=('" . $secureThreadID . "')");
                    if (mysqli_num_rows($statusQuery) == 1) {
                        $unread_status = false;
                    } else {
                        $unread_status = true;
                    }
                    
                    if ($unread_status == false) {
                        $threads_sub_icon = ' class="threadicons" id="thread_def"';
                    } else {
                        $threads_sub_icon = ' class="threadicons" id="thread_new"';
                    }
                } else {
                    $threads_sub_icon = ' class="threadicons" id="thread_def"';
                }
            }
            
            $sub_table .= '
				<tr class="container-1 smoothTransitionFast">
					<td class="columnIcon">
						<div ' . $threads_sub_icon . '></div>
					</td>
					<td class="columnTopic">
						<div class="topic">
							<p>
								<span class="Title">
									<strong>
										' . $threads_sub_title_msg . '
									</strong>
								</span>
								<a href="?page=Index&amp;threadID=' . $threads_id . '">
									' . $threads_sub_title . '
								</a>
							</p>
						</div>
						<div class="statusDisplay">
							<div class="statusDisplayIcons">
							</div>
						</div>
						<p class="firstPost light">
							Von
							<a href="?page=Profile&amp;User=' . $threads_author_id . '">
							' . $threads_author . '
							</a>
							(' . $lastThreadReplyF . ')
						</p>
					</td>
					<td class="columnRating" align="center">
						'. $thread_rating_ . '
					</td>
					<td class="columnReplies">
						' . $threads_sub_posts . '
					</td>
					<td class="columnViews hot">
						' . $threads_sub_views . '
					</td>
					<td class="columnLastPost">
						<!--<div class="containerIconSmall">
							<a href="?page=Index&amp;threadID=' . $threads_id . '&amp;action=lastPost">
								<img src="./images/icons/goToLastPostS.png" alt="" title="Zum letzten Beitrag dieses Themas springen" width="16" height="16">
							</a>
						</div>-->
						<div class="containerContentSmall">
							<p>
								Von 
								<a href="?page=Profile&amp;User=' . $threads_sub_last_author_id . '">
									' . $threads_participant_last . '
								</a>
							</p>
							<p class="lastPost_time">
								(' . $threads_sub_last_reply_F . ')
							</p>
						</div>
					</td>
				</tr>
				';
        }
        $sub_table .= "
</tbody>
</table>
";

} else {
	$sub_table .= '
	<div class="empty_board">
	<div class="icons_big" id="arrow">
	</div>
	<div>
		<p>
			In diesem Board gibt es noch keine Themen
		</p>
	</div>
	</div>
	';
}
        
        
        $url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        
        function writeThread()
        {
            
            if ((empty($_POST["thread_title"]))) {
                echo '
<br />
<center>
<span style="color:#FF0000;">
Sie haben nicht alle benötigten Informationen eingegeben!
<br />
</span>
</center>
';
            } else {
                
                $post_new_title = ($_POST["thread_title"]);
                $post_new_text  = ($_POST["area_add_new_thread"]);
                
                
                $control = 0;
                
                // UPDATE USER
                $user_session = session_id();
                $db->query("UPDATE $db->table_accdata SET post_counter=post_counter+1 WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid= ('" . mysqli_real_escape_string($GLOBALS['connection'], $user_session) . "'))");
                
                // ADD Thread
                if ($control == 0) {
                    $db->query("INSERT INTO $db->table_thread (main_forum_id, author_id, title, date_created) VALUES ('" . mysqli_real_escape_string($GLOBALS['connection'], $_GET["boardview"]) . "', (SELECT id FROM $db->table_accounts WHERE sid= ('" . mysqli_real_escape_string($GLOBALS['connection'], $user_session) . "')), '" . mysqli_real_escape_string($GLOBALS['connection'], $post_new_title) . "', NOW())") or die(mysql_error());
                    $control = 1;
                }
                if ($control == 1) {
                    // ADD Thread First Post
                    $db->query("INSERT INTO $db->table_thread_posts (thread_id, author_id, date_posted, text) VALUES ((SELECT MAX(id) FROM $db->table_thread), (SELECT id FROM $db->table_accounts WHERE sid= ('" . mysqli_real_escape_string($GLOBALS['connection'], $user_session) . "')), NOW(), ('" . mysqli_real_escape_string($GLOBALS['connection'], $post_new_text) . "'))") or die(mysql_error());
                    header("refresh:1;url=/index.php?page=Index");
                }
            }
        }
    }
}


if (!$boardID) {
    echo $board_table;
}

if ($boardID) {
    if ((!isset($_GET['form']) || !$_GET['form'] == 'threadAdd') && isset($sub_table)) {
        echo $sub_table;
    }
    if (isset($_GET['form']) && $_GET['form'] == 'threadAdd') {
        
        if (isset($_GET['action']) && $_GET['action'] == 'threadAdd') {
            if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet'] = true) {
                $main->useFile('./system/controller/board_controller/board_add_thread.php');
                $add_thread_status = addThread();
            }
        }
        if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true) {
            if (!isset($add_thread_status) || (isset($add_thread_status['threadAddStatus']) && $add_thread_status['threadAddStatus'] == false)) {
			
			$string = '';
			
$threadContainer = '
			
<div class="thread-addContainer">
  <p>
    Ein neues Thema erstellen
  </p>
  <form method="POST" action="';

                $threadContainer .= $main->getURI();;
                
                
                if (isset($_GET['token']) && !empty($_GET['token'])) {
                    $token = mysqli_real_escape_string($GLOBALS['connection'], $_GET['token']);
                    $query = $db->query("SELECT title, content FROM $db->table_thread_saves WHERE token = ('" . $token . "') AND user_id = (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
                    
                    while ($results = mysqli_fetch_object($query)) {
                        $title   = $results->title;
                        $content = $results->content;
                        
                        $string = 'value="' . $title . '"';
                        
                    }
                }
                if (!isset($_GET['token']))
                    $content = '';

$threadContainer .= '&form=threadAdd&action=threadAdd" class="threadAddForm" id="threadAddForm">


  <fieldset class="threadAddLegend">
    <legend>
      Themen Titel
    </legend>
	<select>
		<option>
		</option>
		<option>
			RP
		</option>
		<option>
			Wichtig
		</option>
		<option>
			Off-Topic
		</option>
	</select>
    <input type="text" class="threadTitleInput" id="threadTitleInput" name="threadTitleInput" style="border: 2px solid rgba(255, 0, 0, 0); width: 300px;" '.$string.'>
  </legend>
  </fieldset>
  <fieldset class="PostAddLegend">
    <legend>
      Beitrag
    </legend>
    <textarea type="hidden" id="threadAddArea" name="threadAddArea">

                '.$content.'

	</textarea>
<script type="text/javascript">
CKEDITOR.replace("threadAddArea", { 
language: "de", 
enterMode : CKEDITOR.ENTER_BR
});
</script>
	  <script src="./javascript/thread_save.js"></script>
    <div class="submitPost" style="margin:30px 0;">
      <input type="submit" value="Absenden" id="threadAddSubmitBtn">
      <input type="reset" value="Zurücksetzen" id="threadAddResetBtn">
    </div>
    <span id="ThreadAddResponse_failed" class="responseFailed">
    </span>
    <span id="ThreadAddResponse_Success" class="responseSuccess">
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
</form>
</div>';

echo $threadContainer;


            } else {
                $main->useFile('./system/interface/successpage_threadcreate.php');
                throwSuccess_thread("Ihr Thema wurde erfolgreich erstellt!", "?page=Index&threadID=" . $add_thread_status['newThreadID']);
                echo '
					<meta http-equiv="refresh" content="3;url=?page=Index&threadID=' . $add_thread_status['newThreadID'] . '">
					';
            }
        } else {
            $pageRightsBoard = "Sie haben leider nicht die notwendigen Zugriffsrechte um diese Seite zu besuchen.";
            throwError($pageRightsBoard);
        }
    }
}
?>