<?php
$pageRights = 'Sie haben leider nicht die notwendigen Zugriffsrechte, um diese Seite zu besuchen.';
$postEdit_differentAuthors = 'Dieser Beitrag ist nicht ihrem Account zugeordnet, ein Bearbeiten ist daher nicht möglich.';

function throwError_cc($ErrorPgMsg) {


	if(isset($_SERVER['HTTP_REFERER'])) {
		$referer = $_SERVER['HTTP_REFERER'];
	   }
	else
	{
	   $referer = '?page=Portal';
	}

	$errorString ='
		<div class="alertMain_cc">
		<center>
		<img src="./images/graphics/warning_detailed.png" width="70px" height="67px" class="warningImg">
		</center>
		<div class="innerWarning">
		<p>
		'.$ErrorPgMsg.'
		</p>
		<p>
		<a href="'.$referer.'" class="ErrorLink">
		Zurück zur vorherigen Seite
		</a>
		</p>
		</div>
		</div>';

	echo $errorString;
}
?>