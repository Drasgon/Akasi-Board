<?php
$pageRights = 'Sie haben leider nicht die notwendigen Zugriffsrechte, um diese Seite zu besuchen.';
$getIssue = "Die aktuelle URL enthält unzulässige Werte, bitte überprüfen Sie die Schreibweise und Richtigkeit der Daten.";
$postEdit_differentAuthors = 'Dieser Beitrag ist nicht ihrem Account zugeordnet, ein Bearbeiten ist daher nicht möglich.';

function throwError($ErrorPgMsg) {

	if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == '')
		$prevPage = $_SERVER['HTTP_REFERER'];
	else
		$prevPage = "http://" . $_SERVER['HTTP_HOST'];

	$errorString ='
	<div class="alertMain">
		<center>
			<img src="./images/graphics/warning_detailed.png" width="70px" height="67px" class="warningImg">
		</center>
		<div class="innerWarning">
			<p>
				'.$ErrorPgMsg.'
			</p>
			<p>
				<a href="'.$prevPage.'" class="ErrorLink">
					Zurück zur vorherigen Seite
				</a>
			</p>
		</div>
	</div>';

	echo $errorString;
}
?>