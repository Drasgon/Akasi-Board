<?php
$changeSuccess = 'Ihre Änderung(en) wurde(n) erfolgreich übernommen!';

function throwSuccess_login($changeSuccess) {

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
<a href="'.$_SERVER['HTTP_REFERER'].'" class="ErrorLink">
Zurück zur Startseite
</a>
</p>
</div>
</div>';

return $successString;
}
?>