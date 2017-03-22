<?php
function changeAvatarBorder() {

	$avatarBorderUpdateError_fatal = '';

	if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);
	
	// Convert values to int, to be 100% sure
		$r = (isset($_POST['r']) && !empty($_POST['r'])) ? intval($_POST['r']) : 0;
		$g = (isset($_POST['g']) && !empty($_POST['g'])) ? intval($_POST['g']) : 0;
		$b = (isset($_POST['b']) && !empty($_POST['b'])) ? intval($_POST['b']) : 0;
		$a = (isset($_POST['a']) && !empty($_POST['a'])) ? intval($_POST['a']) : 0;
		
		// echo $r . ', ' . $g . ', ' . $b . ', ' . $a;
	
	// If values are numeric and between 0 and 255
	if($r >= 0 && $r <= 255 && is_numeric($r)
		&& $g >= 0 && $g <= 255 && is_numeric($g)
		&& $b >= 0 && $b <= 255 && is_numeric($b)
		&& $a >= 0 && $a <= 100 && is_numeric($a))
	{
	
		// If values are safe, escape them
		$r = mysqli_real_escape_string($connection, $r);
		$g = mysqli_real_escape_string($connection, $g);
		$b = mysqli_real_escape_string($connection, $b);
		$a = number_format(mysqli_real_escape_string($connection, $a) / 100, 2, '.', '');
		
		$value = $r.','.$g.','.$b.','.$a;
			
		$result = $main->updateAccount('avatar_border', $value);

		if(!$result) {
		$avatarBorderUpdateError_fatal = 'Krititscher Fehler beim Update des Rahmens.';
				throwError_cc($avatarBorderUpdateError_fatal);
				return;
		} else {
		$success_status = true;
			if($success_status == true) {
			$changeSuccess = 'Ihre Änderung(en) wurde(n) erfolgreich übernommen!';
			throwSuccess($changeSuccess);
			}
		}

	} else {
			$avatarBorderUpdateError_fatal = 'Fehler beim Update des Rahmens!';
				throwError_cc($avatarBorderUpdateError_fatal);
				
			return;
	}
}
?>