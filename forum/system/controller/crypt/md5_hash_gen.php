<?php
function generateHash($password)
{
	$password = $password;
	$sec = $password.$password;
	$rand_val = rand(8,256);
	$time_visual = rand(1400405000,2400405000);

	for ($i = $rand_val; $i <= $rand_val; $i++) {

	$password_new = $password.$i;

	$var = "$password.$sec.$time_visual.$password_new";

	$pass_hash_ = md5(strtoupper($password) . ":" . strtoupper($var));
	$pass_hash_final = md5(strtoupper($pass_hash_) . ":" . strtoupper($password));
	}
	
	return ARRAY(
		'pass_hash_final' 	=> $pass_hash_final,
		'rand_val' 			=> $rand_val,
		'time_visual' 		=> $time_visual,
	);
}
?>