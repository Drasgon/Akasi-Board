<?php
// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

// Re initialize the DB

	$db         = new Database();
	$connection = $db->mysqli_db_connect();


// VARIABLES
                
                $threadID          = mysqli_real_escape_string($connection, $_GET['threadID']);
                $postID            = mysqli_real_escape_string($connection, $_GET['postID']);
                $checkUserIdentity = $db->query("SELECT author_id FROM $db->table_thread_posts WHERE author_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')) AND id=('" . $postID . "')");
                
                if (mysqli_num_rows($checkUserIdentity) == 1 || isset($_SESSION['MODACCESS']) && $_SESSION['MODACCESS'] == true) {
				
					$getInitial = $db->query("SELECT text, author_id FROM $db->table_thread_posts WHERE id=('" . $postID . "') AND thread_id=('" . $threadID . "')");
                    $initialData = mysqli_fetch_object($getInitial);
                        $initialContent = $initialData->text;
						$initialAuthor_id =  $initialData->author_id;
						
						$initialAuthor = $main->getUsername($initialAuthor_id);
                    
                    // CONTENT DATA
					
						if (isset($_SESSION['USERACCESS']) && $_SESSION['USERACCESS'] == true)
							$permissionRang = 'User';
						if (isset($_SESSION['MODACCESS']) && $_SESSION['MODACCESS'] == true) 
							$permissionRang = 'Moderator';
						if (isset($_SESSION['ADMINACCESS']) && $_SESSION['ADMINACCESS'] == true)
							$permissionRang = 'Administrator';
						
			
				if (isset($_GET['action']) && $_GET['action'] == 'postEdit' && isset($_SESSION['STATUS'])) {
                        $main->useFile('./system/controller/board_controller/board_edit_post.php');
                        
                        submitPostEdit();
                    }
	
				$threadContainer = '
					  <div class="post-addContainer">
					<p class="postEditHeader changeInformation">
					  Sie bearbeiten einen Beitrag von <a href="?page=Profile&User='.$initialAuthor_id.'" target="_blank">'.$initialAuthor.'</a> als '.$permissionRang.'
				  </p>
				  <fieldset class="PostAddLegend">
					<legend>
					  Beitrag
					</legend>
					<form method="POST" action="?page=Index&threadID='.$_GET['threadID'].'&postID='.$_GET['postID'].'&form=postEdit&action=postEdit" class="postAddForm" id="postAddForm">
						<textarea type="hidden" id="postAddArea" name="postEditArea">

										'.$initialContent.'

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
							
						  <div class="submitPost" style="margin:30px 0;">
							<input type="submit" value="Absenden" id="postAddSubmitBtn">
							<input type="reset" value="Zurücksetzen" id="postAddResetBtn">
						  </div>
						 </form>
						 <span id="PostAddResponse_failed" class="responseFailed">';

                    
                    

				$threadContainer .='
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
	
	

                } else {
                    throwError($postEdit_differentAuthors);
                }

?>