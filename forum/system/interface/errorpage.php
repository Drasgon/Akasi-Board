<?php
$pageRights = 'Sie haben leider nicht die notwendigen Zugriffsrechte, um diese Seite zu besuchen.';
$getIssue = "Die aktuelle URL enthält unzulässige Werte, bitte überprüfen Sie die Schreibweise und Richtigkeit der Daten.";
$postEdit_differentAuthors = 'Dieser Beitrag ist nicht ihrem Account zugeordnet, ein Bearbeiten ist daher nicht möglich.';

function throwError($ErrorPgMsg, $linkExt = NULL) {

	if($linkExt != NULL)
		$linkExt = $linkExt;
	else
	{
		if(isset($_SERVER['HTTP_REFERER']))
			$linkExt = $_SERVER['HTTP_REFERER'];
		else
			$linkExt = '?page=Portal';
	}
	

	$errorString = '
	<div class="errorMain">
		<center>
			<div class="icons_big" id="warning"></div>
		</center>
		<div class="innerWarning">
			<p>
				'.$ErrorPgMsg.'
			</p>
			<p>
				<a href="'.$linkExt.'" class="ErrorLink">
					Zurück zur vorherigen Seite
				</a>
			</p>
		</div>
	</div>';

	echo $errorString;
}
?>