<?php
/*---------------------------------------------------------------------------------------------
				   \	Anti - Session Hijacking System	   /
					\_____________________________________/

Überprüfe die aktuelle User IP und gleiche sie mit der gespeicherten in der DB ab.
Befinden sich die beiden IP's nicht in Reichweite, dann wird der User ausgeloggt und sämtliche
Sessions, welche dem Account zugeordnet waren, werden zerstört.


WICHTIG: Dieses Script ist auf JEDER Seite, welche ein Nutzerkonto nutzt einzubinden.
         Dazu zählen schon User Panels.

		 
Mögliche Komplikationen:	Falls ein Session Cookie für einen dauerhaften Login genutzt wird
							und der User nach der Zwangstrennung des Anbieters eine neue IP erhalten hat,
							tritt dieses System in Kraft und macht die Session sowie Cookie zunichte.

							
Mögliche Lösung:			Bei der Auswertung der IP Adresse nicht die aktuelle IP sondern
							die IP-Range auswerten.

							
---------------------------------------------------------------------------------------------*/

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

$modeConfig = $main->serverConfig("ip_validation_system");
$mode = ($modeConfig >= 1) ? true : false;

function Validate($mode) {
if ($mode) {

session_start(); 
if(isset($_COOKIE['PHPSESSID'])) 
{ 
		// User IP auslesen
        if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $client_ip_val = $_SERVER['REMOTE_ADDR'];
        } else {
            $client_ip_val = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

		// Gespeicherte User IP auslesen
		$session_id_val = mysqli_real_escape_string($GLOBALS['connection'], session_id());
		$user_ip = $db->query("SELECT last_login_ip FROM $db->table_accounts WHERE sid=('" . $session_id_val . "')");
		$user_ip_con = mysqli_fetch_object($user_ip);
		$user_ip_val = $user_ip_con->last_login_ip;
		
	// Falls User IP und gespeicherte IP nicht übereinstimmen	
	if($client_ip_val != $user_ip_val) {
			if(!$_GET['form'] == 'UserLogin') {
		
	$session_id_validated = mysqli_real_escape_string($GLOBALS['connection'], session_id());
	$db->query("INSERT INTO protection_system_logs (message, account_id, user_ip, saved_ip, date) VALUES ('User wurde aufgrund nicht identischer IP Adressen durch das Anti-Session Hijacking System ausgeloggt.', (SELECT id FROM $db->table_accounts WHERE sid= ('" . $session_id_validated . "')), ('" . $client_ip_val . "'), ('" . $user_ip_val . "'), NOW())") or die(mysql_error());
	$db->query("UPDATE $db->table_accounts SET last_sid=sid,sid=0,logged_in=0 WHERE sid=('" . $session_id_validated . "')");
	$db->query("UPDATE $db->table_sessions SET last_sid=sid,sid=0,active=0 WHERE sid=('" . $session_id_validated . "')");
	
	// Session Keks zerkrümeln
	setcookie ("PHPSESSID", "", time() - 3600);
								}
								
// Falls User IP und gespeicherte IP übereinstimmen: Funktion verlassen
} else { exit(); }
}			
// Falls System deaktiviert: Funktion verlassen
} else { exit(); }
}
Validate();
?>