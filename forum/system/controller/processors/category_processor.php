<?php
/*
CATEGORY HANDLER
*/

if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'catLoad' && isset($_POST['loadCategory']) && !empty($_POST['loadCategory'])) {

	include('../../classes/akb_mysqli.class.php');
	include('../../classes/akb_main.class.php');

	if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);
    
    $_SESSION['ID'] = mysqli_real_escape_string($connection, $_COOKIE['PHPSESSID']);
    
    $getUserData = $db->query("SELECT account_id FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))") or die(mysql_error());
    
    while ($userData = mysqli_fetch_object($getUserData)) {
        $userID = $userData->account_id;
    }
    
    $categoryContent = mysqli_real_escape_string($connection, $_POST['loadCategory']);
    
    $getLastID = $db->query("SELECT id, icon_id, title, description, closed FROM $db->table_boards WHERE category=('" . $categoryContent . "') ORDER BY id DESC");
    
    while ($lastID = mysqli_fetch_object($getLastID)) {
        $rowid           = $lastID->id;
        $row_icon_id     = $lastID->icon_id;
        $rowtitle        = $lastID->title;
        $row_description = $lastID->description;
        $rowclosed       = $lastID->closed;
    }
    
    $db->query("UPDATE $db->table_hiddenboards SET state='1' WHERE user_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')) AND cat_id=('" . $categoryContent . "')");
    
    $board_table = '';
	
    $get_forums  = "SELECT id, icon, icon_id, title, description, closed FROM $db->table_boards WHERE category=('" . $categoryContent . "')";
    $forums_result = $db->query($get_forums) or die(mysql_error());
    while ($forums = mysqli_fetch_object($forums_result)) {
        unset($lastThreadID);
        unset($lastThreadTitle);
        unset($lastThreadAuthor);
        unset($lastThreadReply);
        unset($lastThreadAuthorName);
        unset($lastActivity_id);
        unset($fetchLastThread_id);
        
        $board_id      = $forums->id;
        $board_icon    = $forums->icon;
        $board_icon_id = $forums->icon_id;
        $board_closed  = $forums->closed;
        
        
        if ($board_closed == 1) {
            $board_icon = "./images/icons/closed.png";
        }
        if ($board_closed == 0) {
            $statusQuery_threadCount = $db->query("SELECT COUNT(*) AS count_threads FROM $db->table_thread WHERE main_forum_id=('" . $board_id . "')");
            
            $totalRows_threads = mysqli_fetch_row($statusQuery_threadCount);
            
            $statusQuery_ID = $db->query("SELECT COUNT(*) AS count_unreads FROM $db->table_forum_read WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid='" . $_SESSION['ID'] . "') AND board_id=('" . $board_id . "')");
            
            $totalRows_unreads = mysqli_fetch_row($statusQuery_ID);
            
            if ($totalRows_threads == $totalRows_unreads || (!isset($_SESSION['STATUS']) || $_SESSION['STATUS'] == false)) {
                $boardUnread_status = false;
            } else {
                $boardUnread_status = true;
            }
            
            if ($boardUnread_status == false) {
                $board_icon       = './images/icons/default.png';
                $board_titleClass = '';
            } else {
                $board_icon       = './images/icons/new_post.png';
                $board_titleClass = 'class="newPost-Board"';
            }
        }
        $board_title        = $forums->title;
        $board_description  = $forums->description;
        $failMsg            = '';
        $msg                = 'Von ';
        $fetchLastThread_id = $db->query("SELECT thread_id FROM $db->table_thread_posts WHERE thread_id=(SELECT id FROM $db->table_thread WHERE main_forum_id=('" . $board_id . "') ORDER BY last_replyTime DESC LIMIT 1) ORDER BY date_posted DESC LIMIT 1");
        while ($lastThreadActive_idNewest = mysqli_fetch_object($fetchLastThread_id)) {
            $lastActivity_id = $lastThreadActive_idNewest->thread_id;
        }
        $defineLastThread = "SELECT id,title,last_post_author_id,last_replyTime FROM $db->table_thread WHERE main_forum_id=('" . $board_id . "') AND id=('" . $lastActivity_id . "') ORDER BY last_replyTime ASC LIMIT 0,1";
        $lastThreadResult = $db->query($defineLastThread) or die(mysql_error());
        $num_LastThread = mysqli_num_rows($lastThreadResult);
        if ($num_LastThread <= 0) {
            $lastThreadResult = '';
            $failMsg          = 'In dieser Kategorie sind noch keine Themen vorhanden';
            $msg              = '';
        } else {
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
            
            $defineLastAuthor = "SELECT username FROM $db->table_accdata WHERE account_id=('" . $lastThreadAuthor . "')";
            $lastAuthorResult = $db->query($defineLastAuthor) or die(mysql_error());
            while ($lastAuthorActive = mysqli_fetch_object($lastAuthorResult)) {
                $lastThreadAuthorName = $lastAuthorActive->username;
            }
            mysqli_free_result($lastAuthorResult);
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
							' . $board_title . '
							</a>
							</h4>
							<p class="desc specialP">
							' . $board_description . '
							</p>
						</div>
					</div>
					<div class="boardThreadData">
						<h4 class="boardTitle" ' . $board_titleClass . '>';
								if (!$num_LastThread <= 0) {
									$board_table .= '
									<a href="?page=Index&threadID=' . $lastThreadID . '" title="Zum Thread ' . $lastThreadTitle . ' springen.">
									' . $lastThreadTitle . '
									</a>';
								}
								$board_table .= '
						</h4>
						<p class="desc specialP">
							' . $msg . ' ' . $failMsg . ' 
							<a href="?page=Profile&amp;User=' . $lastThreadAuthor . '">
							' . $lastThreadAuthorName . '</a>
							<span class="lastPostDateCon">  ' . $lastThreadReply . '  </span>
						</p>
					</div>
				</div>
			</li>';
			
			
		if($main->boardConfig($board_id, 'member_exclusive') == 1)
		{
			if(isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == TRUE)
			{
				echo $board_table;
			}
		}
		else if($main->boardConfig($board_id, 'member_exclusive') == 0)
		{
			echo $board_table;
		}
    }
    mysqli_free_result($forums_result);
    
} else {
    throw new HttpException(500, "Database Error");
}
?>