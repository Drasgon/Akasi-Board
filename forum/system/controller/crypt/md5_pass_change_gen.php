<?php
$sec = $value_actual.$value_actual;
$rand_val = $crypt_level;
$time_visual = $extra_val;

for ($i = $rand_val; $i 
<= $rand_val; $i++) {

$password_new = $value_actual.$i;

$var = "$value_actual.$sec.$time_visual.$password_new";

$pass_hash_ = md5(strtoupper($value_actual) . ":" . strtoupper($var));
$pass_hash_actual = md5(strtoupper($pass_hash_) . ":" . strtoupper($value_actual));
}
?>