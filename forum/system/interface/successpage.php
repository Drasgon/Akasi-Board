<?php
$changeSuccess = 'Ihre Änderung(en) wurde(n) erfolgreich übernommen!';

function throwSuccess($changeSuccess, $linkExt = NULL) {

	if($linkExt != NULL)
		$linkExt = $linkExt;
	else
		$linkExt = $_SERVER['HTTP_REFERER'];


	$successString = '
	<div class="successMain">
		<center>
			<div class="icons_big" id="success"></div>
		</center>
		<div class="innerWarning">
			<p>
				'.$changeSuccess.'
			</p>
			<p>
				<a href="'.$linkExt.'" class="ErrorLink">
					Zurück zur vorherigen Seite
				</a>
			</p>
		</div>
	</div>';

	echo $successString;
	
}

?>