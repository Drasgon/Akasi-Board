<?php
$changeSuccess = 'Ihre Änderung(en) wurde(n) erfolgreich übernommen!';

function throwSuccess_thread($changeSuccess, $linkString) {

$successString ='
<div class="successMain">
<center>
<div class="icons_big" id="success"></div>
</center>
<div class="innerWarning">
<p>
'.$changeSuccess.'
</p>
<p>
<a href="'.$linkString.'" class="ErrorLink">
Falls die automatische Weiterleitung nicht funktioniert, klicken Sie bitte hier.
</a>
</p>
</div>
</div>';

echo $successString;
}
?>