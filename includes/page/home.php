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

	
	$fp = fsockopen("udp://baneofthelegion.de","9987"); 
if ($fp) { 
$ts_status = "Online";
} else { 
$ts_status = "Offline";
} 
	
	$html .= '
	</div>
	
	<div class="home_column">
		<img src="./img/gfx/gfx_img/header_ts.png">
			<div class="teamspeak_status clearfix">
				<p>'.$ts_status.'</p>
			</div>
	</div>
	
	<div class="home_column">
		<img src="./img/gfx/gfx_img/header_donate.png">
		<div class="pp_donate">
			<p>
				Auch wir haben Laufzeitgebühren für unseren Member-Service (HP und TeamSpeak) zu zahlen. Daher freuen wir uns über jegliche Spenden per PayPal!
			</p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_donations">
				<input type="hidden" name="business" value="Alexander_Bretzke@gmx.de">
				<input type="hidden" name="lc" value="DE">
				<input type="hidden" name="item_name" value="Bane of the Legion">
				<input type="hidden" name="no_note" value="0">
				<input type="hidden" name="currency_code" value="EUR">
				<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
				<input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
				<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
	</div>';
	
	echo $html;
?>