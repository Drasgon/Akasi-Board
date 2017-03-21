<?php
function register()
{
	/// Re initialize the DB and Runtime Class
	if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);
	
    global $register_dataArray;
    
    $everythingEmpty      = '';
    $passwordsNotMatching = '';
    $usernameToShort      = '';
    $usernameToLong       = '';
    $passwordToShort      = '';
    $passwordToLong       = '';
	$passwordEmpty		  = '';
	$passwordRepeatEmpty  = '';
    $usernameIllegalChars = '';
    $usernameAlreadyInUse = '';
    $usernameEmpty        = '';
    $registerSuccess      = '0';
    $errorStatus          = '';
	$mailEmpty			  = '';
	$mailRepeatEmpty	  = '';
	$mailNotMatching 	  = '';
	$mailError			  = '';
	$criticalRegError	  = '';
	$captchaError		  = '';
	$captchaEmpty		  = '';
    
    $user_chars = "/[^a-z_\-0-9]/i";
    
    if ((empty($_POST["username_reg"])) 
	&& (empty($_POST["password_reg"])) 
	&& (empty($_POST["password_reg_repeat"])) 
	&& (empty($_POST["mail_reg"])) 
	&& (empty($_POST["mail_reg_repeat"]))
	&& (empty($_POST["captcha_code"]))
	) {
        $everythingEmpty = true;
        $errorStatus     = true;
    } else {
        
        $username   = mysqli_real_escape_string($GLOBALS['connection'], $_POST["username_reg"]);
        $password   = mysqli_real_escape_string($GLOBALS['connection'], $_POST["password_reg"]);
        $password2  = mysqli_real_escape_string($GLOBALS['connection'], $_POST["password_reg_repeat"]);
        $user_mail  = mysqli_real_escape_string($GLOBALS['connection'], $_POST["mail_reg"]);
        $user_mail2 = mysqli_real_escape_string($GLOBALS['connection'], $_POST["mail_reg_repeat"]);
        
        if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        if (empty($username)) {
            $usernameEmpty = true;
			$errorStatus = true;
        }
		
		if (empty($password)) {
            $passwordEmpty = true;
			$errorStatus = true;
        }
		
		if (empty($password2)) {
            $passwordRepeatEmpty = true;
			$errorStatus = true;
        }
        
        if ($password != $password2) {
            $passwordsNotMatching = true;
			$errorStatus = true;
        }
        ;
        
        if (strlen($username) < 3) {
            $usernameToShort = true;
			$errorStatus = true;
        }
        ;
        
        if (strlen($username) > 13) {
            $usernameToLong = true;
			$errorStatus = true;
        }
        ;
        
        if (strlen($password) < 6) {
            $passwordToShort = true;
			$errorStatus = true;
        }
        ;
        
        if (strlen($password) > 34) {
            $passwordToLong = true;
			$errorStatus = true;
        }
        ;
		
		if (empty($user_mail)) {
			$mailEmpty = true;
			$errorStatus = true;
		}
		;
		
		if (empty($user_mail2)) {
			$mailRepeatEmpty = true;
			$errorStatus = true;
		}
		;
		
		if ($user_mail != $user_mail2) {
			$mailNotMatching = true;
			$errorStatus = true;
		}
		;
		
		if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$user_mail)) {
			$mailError = true; 
			$errorStatus = true;
		}
		;
        
        if (preg_match($user_chars, $username)) {
            $usernameIllegalChars = true;
			$errorStatus = true;
        }
        ;
        
        $get_userCount = $db->query("SELECT * FROM $db->table_accdata WHERE username=('" . $username . "')") or die(mysql_error());
        
        if (mysqli_num_rows($get_userCount) >= 1) {
            $usernameAlreadyInUse = true;
			$errorStatus = true;
        }
        ;
		
		if(empty($_POST['captcha_code'])) {
			$captchaEmpty = true;
			$errorStatus = true;
		} else {
		
		include_once('./system/security/securimage/securimage.php');
		
		$securimage = new Securimage();
		
		if ($securimage->check($_POST['captcha_code']) == false) {
			$captchaError = true;
			$errorStatus = true;
		} }
       
        
        if (empty($everythingEmpty)
		&& empty($passwordsNotMatching)
		&& empty($usernameToShort)
		&& empty($usernameToLong)
		&& empty($passwordToShort)
		&& empty($passwordToLong)
		&& empty($passwordEmpty)
		&& empty($passwordRepeatEmpty)
		&& empty($usernameEmpty)
		&& empty($usernameIllegalChars)
		&& empty($sernameAlreadyInUse)
		&& empty($mailEmpty)
		&& empty($mailRepeatEmpty)
		&& empty($mailNotMatching)
		&& empty($mailError)
		&& empty($criticalRegError)
		&& empty($captchaError)
		&& empty($captchaEmpty)
		) {
		
            $errorStatus = false;
            $main->useFile('./system/controller/crypt/md5_hash_gen.php');
			$hashData = generateHash($password);
            
			$timestamp = time();
			
            $register_sql = ("INSERT INTO $db->table_accounts (username, pass_hash, extra_val, registered_ip, email, crypt_level, registered_date) VALUES ('" . $username . "','" . $hashData['pass_hash_final'] . "','" . $hashData['time_visual'] . "','" . $client_ip . "', '" . $user_mail . "', '" . $hashData['rand_val'] . "', '" . $timestamp . "')") or die(mysql_error());
            $reg_qry = $db->query($register_sql);
            if (!$reg_qry) {
                $criticalRegError = true;
            }
            ;
            if ($reg_qry) {
			
				if(!isset($_SESSION))
					session_start();
				$sid = session_id();
				$sid = md5($sid);
				
				$token = md5(rand(1, 256).':'.$username).md5(rand(1, 256).':'.$user_mail);
                
				$db->query("INSERT INTO $db->table_account_token (uid, token) VALUES ((SELECT id FROM $db->table_accounts WHERE username= ('" . $username . "')), ('".$token."'))");
                $db->query("INSERT INTO $db->table_accdata (account_id, username, email) VALUES ((SELECT id FROM $db->table_accounts WHERE username= ('" . $username . "')), '" . $username . "', '" . $user_mail . "')") or die(mysql_error());
				$db->query("INSERT INTO $db->table_sessions (id, sid) VALUES ((SELECT id FROM $db->table_accounts WHERE username= ('" . $username . "')), ('" . $sid . "'))") or die(mysql_error());
				$db->query("INSERT INTO $db->table_profile (id) VALUES ((SELECT id FROM $db->table_accounts WHERE username= ('" . $username . "')))") or die(mysql_error());
                $registerSuccess = '1';
                
				$main->useFile('../includes/classes/botl.imap.mail.class.php');
				
				
				
				$html = '
					<center style="padding:11px;background:rgba(0, 0, 0, 0.75);border-radius:11px;width:70%;margin:100px auto;">
						<p>
							Vielen Dank, für die Registrierung im Forum von Bane of the Legion, '.$username.'!
						</p>
						<p>
							Um ihren Account freizuschalten, klicken Sie auf den unten stehenden Link.
						</p>
						<p class="biglink">
							<a href="http://www.baneofthelegion.de/forum/?page=Portal&action=validuser&token='.$token.'">Authentifizieren</a>
						</p>
					</center>
				';
				$mail = NEW Mail($user_mail, "Foren Authentifizierung", '', $html);
				$mail->set_default_headers();
				$mail->send_imap_mail();
            }
        }
    }
    $register_dataArray = array(
        'everythingEmpty' => $everythingEmpty,
        'passwordsNotMatching' => $passwordsNotMatching,
        'usernameToShort' => $usernameToShort,
        'usernameToLong' => $usernameToLong,
        'passwordToShort' => $passwordToShort,
        'passwordToLong' => $passwordToLong,
		'passwordEmpty' => $passwordEmpty,
		'passwordRepeatEmpty' => $passwordRepeatEmpty,
        'usernameEmpty' => $usernameEmpty,
        'usernameIllegalChars' => $usernameIllegalChars,
        'usernameAlreadyInUse' => $usernameAlreadyInUse,
        'registerSuccess' => $registerSuccess,
        'mailEmpty'       => $mailEmpty,
		'mailRepeatEmpty' => $mailRepeatEmpty,
		'mailNotMatching' => $mailNotMatching,
		'mailError' 	  => $mailError,
		'captchaError'	  => $captchaError,
		'captchaEmpty'	  => $captchaEmpty,
		
		'criticalRegError' => $criticalRegError,
        'errorStatus' => $errorStatus
    );
    return $register_dataArray;
}
?>