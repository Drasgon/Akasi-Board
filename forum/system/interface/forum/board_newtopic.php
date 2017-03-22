<?php
// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);


$string = '';
			
$threadContainer = '
			
<div class="thread-addContainer">
  <p>
    Ein neues Thema erstellen
  </p>
  <form method="POST" action="';

                $threadContainer .= $main->getURI();;
                
                
                if (isset($_GET['token']) && !empty($_GET['token'])) {
                    $token = mysqli_real_escape_string($connection, $_GET['token']);
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
	
	<div class="onoffswitch">
		Save: 
		<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="topic_save_btn" disabled="disabled">
		<label class="onoffswitch-label" for="topic_save_btn">
			<span class="onoffswitch-inner"></span>
			<span class="onoffswitch-switch"></span>
		</label>
	</div>
	
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

?>