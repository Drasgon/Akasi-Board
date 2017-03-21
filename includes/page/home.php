<?php
	
	// Use the board database
	if(!isset($db))
	{
		include('./forum/system/classes/akb_mysqli.class.php');
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	
	if (!isset($main) || $main == NULL)
	{
		include('./forum/system/classes/akb_main.class.php');
		$main = new Board($db, $connection);
	}
	
	$html = '';
	
	/*if($db && $board_link)
		echo 'EVERYTHING\'s okay!';*/
		
		
	$get_news_id = $db->query("SELECT news_id FROM ".$db->table_portal_news." LIMIT 1");
		$news_id = mysqli_fetch_object($get_news_id);
			$id = $news_id->news_id;
	
	$html .= '<div class="home_column">
				<img src="./img/gfx/gfx_img/header_news.png">';
	
	if(isset($id))
	{
		$getNewsContent = $db->query("SELECT title, date_created, author_id FROM $db->table_thread WHERE id=('".$id."') ORDER BY id DESC LIMIT 1");
			while($newsContent = mysqli_fetch_object($getNewsContent)) {
				$title = $newsContent->title;
				$date_created = $newsContent->date_created;
				$author_id = $newsContent->author_id;
			}
			
			if(isset($date_created))
				$date_created = $main->convertTime($date_created);
		
			if(isset($author_id))
			{
				$getAuthor = $db->query("SELECT username FROM $db->table_accdata WHERE account_id=('".$author_id."') LIMIT 1");
					while($author = mysqli_fetch_object($getAuthor)) {
						$username = $author->username;
					}
					
				$getContent = $db->query("SELECT text FROM $db->table_thread_posts WHERE thread_id=('".$id."') ORDER BY id ASC LIMIT 1");
					while($content = mysqli_fetch_object($getContent)) {
						$text = $content->text;
					}
				 $target = strlen($text);
					if($target>1200) {
					if(($newtarget = strpos($text, ' ', 1200)) !== false ) {
					$target = $newtarget;
					} else {
					$target = 1200;
					 }
					}
					$text = substr($text, 0,$target);
					$text = $main->closetags($text);
				$text .= '... <a href="forum/?page=Index&threadID='.$id.'">[Weiterlesen]</a>';


		
		
			$html .= '
			<div class="news_header">
				<div class="news_header_right">
					<h3><div class="icons" id="newsicon"></div>'.$title.'</h3>
					<p>'.$date_created.' von <a href="forum/?page=Profile&User='.$author_id.'">'.$username.'</a></p>
				</div>
			</div>';
			$html .='
			<div class="news_main">
				'.$text.'
			</div>
			<div class="news_footer">
			</div>';
			
		}
		else
		{
			$html .= '<p class="news_empty">Es sind keine Neuigkeiten verfügbar! <img src="forum/images/emoticons/Smiley12.png"></p>';
		}
	} else $html .= '<p class="news_empty">Es sind keine Neuigkeiten verfügbar! <img src="forum/images/emoticons/Smiley12.png"></p>';

	$html .= '
	</div>
	
	<div class="home_column">
		<img src="./img/gfx/gfx_img/header_ts.png">
			<div class="teamspeak_status clearfix">
				<span id="its559481"><a href="http://www.teamspeak3.com/">teamspeak</a> Hosting by TeamSpeak3.com</span><script type="text/javascript" src="http://view.light-speed.com/teamspeak3.php?IP=ts56.nitrado.net&PORT=12700&QUERY=10011&UID=559481&display=block&font=11px&background=transparent&server_info_background=transparent&server_info_text=%230c6666&server_name_background=transparent&server_name_text=%2300ccff&info_background=transparent&channel_background=transparent&channel_text=%23d99329&username_background=transparent&username_text=%23e6e612"></script>
			</div>
	</div>';
	
	echo $html;
?>