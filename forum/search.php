<?php
global $langGlobal;

ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(-1);

// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

$search_key = $_GET['q'];


//Sonderzeichen entfernen nur Zahlen und Buchstaben zulassen
$search_key = preg_replace('/[^a-zA-Z 0-9]/', '', $search_key);
 
//Zahlen von Buchstaben trennen um Suche zu verbessern (Iphone 3GS --> Iphone 3 GS)
$search_key = preg_replace('/[a-z]+|[0-9]+/', '\0 ', $search_key);
 
//Überflüssiger Leerzeichen entfernen
$search_key = preg_replace('/\s\s+/', ' ', $search_key);
 
//Leerzeichen am Anfang und Ende entfernen
$search_key_last = trim($search_key);
 
//String in Wörter zerlegen
$search_key = explode(" ",$search_key_last);
 
$such_query = "SELECT thread_id, author_id, date_posted, text
                            FROM ".$db->table_thread_posts."
                            WHERE ";
                            for($i=0; $i<count($search_key); $i++)
                                {
                                    $such_query .= "text LIKE CONCAT('%".mysqli_real_escape_string($connection, $search_key[$i])."%')";
 
                                    if($i<count($search_key)-1)
                                        {$such_query .= " AND ";}
                                }
$such_query = $db->query($such_query."ORDER BY date_posted DESC");

		$result_string = '';
		$search_results = 0;

		while($results = mysqli_fetch_object($such_query))
		{
			$search_results++;
		
			$thread_id = $results->thread_id;
			$thread_title = $main->getThreadName($thread_id);
			$text = $results->text;
			$date_posted = $main->convertTime($results->date_posted);
			$author_id = $results->author_id;
				$author_data = $main->getUserdata($author_id);
			$author_avatar = $main->checkUserAvatar($author_data["avatar"]);
			$author_name = $author_data["name"];
			$author_name = $main->highlightkeyword($author_name, $search_key_last);
			$thread_title = $main->highlightkeyword($thread_title, $search_key_last);
			$text = $main->highlightkeyword($text, $search_key_last);
			
			
			$result_string .= '
				<div class="search_row smoothTransitionFast">
					<div class="search_row_header clearfix">
						<div class="search_row_header_left">
							<a href="?page=Index&threadID='.$thread_id.'">'.$thread_title.'</a>
						</div>
						<div class="search_row_header_right">
							'.$date_posted.'
						</div>
					</div>
					<div class="search_row_body">
						<div class="search_row_body_author">
							<a href="?page=Profile&User='.$author_id.'">
								<img src="'.$author_avatar.'" height="20px" class="UserImage">
								<span class="search_row_author_name">'.$author_name.'</span>
							</a>
						</div>
						'.$text.'
					</div>
				</div>
			';
		}
		
	$search_container = '
	<div class="search">
	  <form method="GET" action="?page=Search">
		<input type="hidden" value="Search" name="page">
		<input type="text" placeholder="Suchen ..." name="q" value="'.$_GET['q'].'">
		<input type="submit" class="submit" value="Suchen">
	  </form>
	</div>

	<div class="mainHeadline">
		<div class="icons" id="forumiconMain"></div>
		<div class="headlineContainer">
		  <h1>
			Suche
		  </h1>
		</div>
		<p>Die Suche für "'.$_GET['q'].'" ergab '.$search_results.' Treffer</p>
	</div>';
		
		echo $search_container.$result_string;

?>