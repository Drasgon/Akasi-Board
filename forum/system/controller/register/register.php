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
	
	if (!isset($security) || $security == NULL)
	{
		$main->useFile('./system/classes/akb_security.class.php', 1);
		$security = new Securityquestion();
	}
	
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
    $errorStatus          = FALSE;
	$mailEmpty			  = '';
	$mailRepeatEmpty	  = '';
	$mailNotMatching 	  = '';
	$mailError			  = '';
	$criticalRegError	  = '';
	$captchaError		  = FALSE;
	$captchaEmpty		  = '';
    
    $user_chars = "/[^a-z_\-0-9]/i";
    
    if ((empty($_POST["username_reg"])) 
	&& (empty($_POST["password_reg"])) 
	&& (empty($_POST["password_reg_repeat"])) 
	&& (empty($_POST["mail_reg"])) 
	&& (empty($_POST["mail_reg_repeat"]))
	&& (empty($_POST["security_question"]))
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
        
        $get_userCount = $db->query("SELECT * FROM $db->table_accdata WHERE username=('" . $username . "')");
        
        if (mysqli_num_rows($get_userCount) >= 1) {
            $usernameAlreadyInUse = true;
			$errorStatus = true;
        }
        ;
		
		if(empty($_POST['security_question'])) {
			$captchaEmpty = true;
			$errorStatus = true;
		}
		else
		{
			if(!$security->checkAnswer($_POST['security_question']))
			{
				$errorStatus = true;
				$captchaError = true;
			}
		}
       
        
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
		&& $errorStatus == FALSE
		) {
		
            $errorStatus = false;
            $main->useFile('./system/controller/crypt/md5_hash_gen.php');
			$hashData = generateHash($password);
            
			$timestamp = time();
			
            $register_sql = ("INSERT INTO $db->table_accounts (username, pass_hash, extra_val, registered_ip, email, crypt_level, registered_date) VALUES ('" . $username . "','" . $hashData['pass_hash_final'] . "','" . $hashData['time_visual'] . "','" . $client_ip . "', '" . $user_mail . "', '" . $hashData['rand_val'] . "', '" . $timestamp . "')");
            $reg_qry = $db->query($register_sql);
            if (!$reg_qry) {
                $criticalRegError = true;
            }
            ;
            if ($reg_qry) {
			
				if(!isset($_SESSION))
					session_start();
				$sid = session_id();
				$sid = md5($sid.$username.$password);
				
				$token = md5(rand(1, 256).':'.$username).md5(rand(1, 256).':'.$user_mail);
				$default_css = $main->serverConfig('default_css_template');
                
				$db->query("INSERT INTO $db->table_account_token (uid, token) VALUES ((SELECT id FROM $db->table_accounts WHERE username= ('" . $username . "')), ('".$token."'))");
                $db->query("INSERT INTO $db->table_accdata (account_id, username, email, design_template) VALUES ((SELECT id FROM $db->table_accounts WHERE username= ('" . $username . "')), '" . $username . "', '" . $user_mail . "', '" . $default_css . "')");
				$db->query("INSERT INTO $db->table_sessions (id, sid) VALUES ((SELECT id FROM $db->table_accounts WHERE username= ('" . $username . "')), ('" . $sid . "'))");
				$db->query("INSERT INTO $db->table_profile (id) VALUES ((SELECT id FROM $db->table_accounts WHERE username= ('" . $username . "')))");
                $registerSuccess = '1';
                
				$main->useFile('../includes/classes/botl.imap.mail.class.php');
				
				
				
				$html = '
					<html>
						<head>
							<meta name="Content-Type" content="text/html; charset=utf-8">
							<meta http-equiv="content-type" content="text/html; charset=utf-8">
							<style>
								@import url(https://fonts.googleapis.com/css?family=Josefin+Sans:400,400italic,700&subset=latin,latin-ext);
								.biglink {
									font-size:1.2em;
									color:#DDD;
								}
								.main, .footer {
									padding:11px;background:rgba(0, 0, 0, 0.75);
									border-radius:11px;
									width:70%;
									margin:100px auto;
								}
								body {
									background:url("http://baneofthelegion.de/img/bg/highmountain.jpg") center no-repeat,rgb(13,16,12);
									background-attachment:fixed;
									background-size:cover;
									font-family: "Josefin Sans",sans-serif;
									color:rgb(42, 154, 59);
									text-shadow:1px 1px 3px rgba(38, 84, 17, 0.71);
								}
								.header {
									background:url("http://baneofthelegion.de/img/gfx/logo_2_small.png") top center no-repeat;
									width:350px;
									height:127px;
									background-size:350px 127px;
									padding-bottom:12px;
									margin-bottom:12px;
									border-bottom: 2px groove rgb(40, 95, 40);
									width:100%;
								}
								.title, .message {
									text-align: left;
									margin:25px;
								}
								.message {
									padding-bottom:12px;
									border-bottom: 2px groove rgb(40, 95, 40);
								}
								.confirmAccount {
									font-size:1.15em;
									font-weight:bold;
								}
								.btn {
								  margin-top: 25px;
								  background: #6fc750;
								  background-image: -webkit-linear-gradient(top, #6fc750, #3d692e);
								  background-image: -moz-linear-gradient(top, #6fc750, #3d692e);
								  background-image: -ms-linear-gradient(top, #6fc750, #3d692e);
								  background-image: -o-linear-gradient(top, #6fc750, #3d692e);
								  background-image: linear-gradient(to bottom, #6fc750, #3d692e);
								  -webkit-border-radius: 6;
								  -moz-border-radius: 6;
								  border-radius: 6px;
								  -webkit-box-shadow: 0px 1px 3px #666666;
								  -moz-box-shadow: 0px 1px 3px #666666;
								  box-shadow: 0px 1px 3px #666666;
								  font-family: Arial;
								  color: #d1d1d1;
								  font-size: 20px;
								  padding: 10px 20px 10px 20px;
								  border: solid #438c1f 2px;
								  text-decoration: none;
								}

								.btn:hover {
								  background: #8bc7a5;
								  background-image: -webkit-linear-gradient(top, #8bc7a5, #35754b);
								  background-image: -moz-linear-gradient(top, #8bc7a5, #35754b);
								  background-image: -ms-linear-gradient(top, #8bc7a5, #35754b);
								  background-image: -o-linear-gradient(top, #8bc7a5, #35754b);
								  background-image: linear-gradient(to bottom, #8bc7a5, #35754b);
								  text-decoration: none;
								  cursor: pointer;
								}
								
								.footer {
									margin-top:175px;
									font-size:0.85em;
									width:50%;
								}
							</style>
						</head>
						<body>
							<center>
								<div class="main">
									<div class="header">
									</div>
									<div class="content">
										<h2 class="title">
											'.$username.', 
										</h2>
										<p class="message">
											wir heißen dich herzlichst Willkommen in unserem Hauseigenen Forum!<br>
											Bevor du jedoch loslegen und dich mit anderen Legionsflüchen abgeben darfst, musst du ledliglich eine Würdigkeit und mentale Existenz unter Beweis stellen!
										</p>
										<div class="confirmAccount">
											Durch einen Klick auf den unten zu findenden Button erklärst du dich, wie schon in der Registrierung auch, mit unseren Nutzungsbestimmungen einverstanden.
										</div>
										<form action="http://www.baneofthelegion.de/forum/?page=Portal&action=validuser&token='.$token.'">
											<input type="submit" value="Account verifizieren!" class="btn">
										</form>
										<a href="http://www.baneofthelegion.de/forum/?page=Portal&action=validuser&token='.$token.'">
											Wenn der oben stehende Button nicht funktioniert, klicke diesen Link!
										</a>
									</div>
								</div>
								<div class="footer">
									"Bane of the Legion" ist ein fiktiver Zusammenschluss im MMORPG "World of Warcraft".<br>
									<br>
									Diese Mail dient einzig und allein der Information und Verifikation der dazugehörigen Personen.<br>
									Haben Sie diese E-Mail fälschlicherweise erhalten, leiten Sie diese bitte an "admin@baneofthelegion.de" weiter.
								</div>
							</center>
						</body>
					</html>
				';
				$self_html = '
					<html>
						<head>
							<meta name="Content-Type" content="text/html; charset=utf-8">
							<meta http-equiv="content-type" content="text/html; charset=utf-8">
							<style>
								@import url(https://fonts.googleapis.com/css?family=Josefin+Sans:400,400italic,700&subset=latin,latin-ext);
								.biglink {
									font-size:1.2em;
									color:#DDD;
								}
								.main, .footer {
									padding:11px;background:rgba(0, 0, 0, 0.75);
									border-radius:11px;
									width:70%;
									margin:100px auto;
								}
								body {
									background:url("http://baneofthelegion.de/img/bg/highmountain.jpg") center no-repeat,rgb(13,16,12);
									background-attachment:fixed;
									background-size:cover;
									font-family: "Josefin Sans",sans-serif;
									color:rgb(42, 154, 59);
									text-shadow:1px 1px 3px rgba(38, 84, 17, 0.71);
								}
								.header {
									background:url("http://baneofthelegion.de/img/gfx/logo_2_small.png") top center no-repeat;
									width:350px;
									height:127px;
									background-size:350px 127px;
									padding-bottom:12px;
									margin-bottom:12px;
									border-bottom: 2px groove rgb(40, 95, 40);
									width:100%;
								}
								.title, .message {
									text-align: left;
									margin:25px;
								}
								.message {
									padding-bottom:12px;
									border-bottom: 2px groove rgb(40, 95, 40);
								}
								.confirmAccount {
									font-size:1.15em;
									font-weight:bold;
								}
								.btn {
								  margin-top: 25px;
								  background: #6fc750;
								  background-image: -webkit-linear-gradient(top, #6fc750, #3d692e);
								  background-image: -moz-linear-gradient(top, #6fc750, #3d692e);
								  background-image: -ms-linear-gradient(top, #6fc750, #3d692e);
								  background-image: -o-linear-gradient(top, #6fc750, #3d692e);
								  background-image: linear-gradient(to bottom, #6fc750, #3d692e);
								  -webkit-border-radius: 6;
								  -moz-border-radius: 6;
								  border-radius: 6px;
								  -webkit-box-shadow: 0px 1px 3px #666666;
								  -moz-box-shadow: 0px 1px 3px #666666;
								  box-shadow: 0px 1px 3px #666666;
								  font-family: Arial;
								  color: #d1d1d1;
								  font-size: 20px;
								  padding: 10px 20px 10px 20px;
								  border: solid #438c1f 2px;
								  text-decoration: none;
								}

								.btn:hover {
								  background: #8bc7a5;
								  background-image: -webkit-linear-gradient(top, #8bc7a5, #35754b);
								  background-image: -moz-linear-gradient(top, #8bc7a5, #35754b);
								  background-image: -ms-linear-gradient(top, #8bc7a5, #35754b);
								  background-image: -o-linear-gradient(top, #8bc7a5, #35754b);
								  background-image: linear-gradient(to bottom, #8bc7a5, #35754b);
								  text-decoration: none;
								  cursor: pointer;
								}
								
								.footer {
									margin-top:175px;
									font-size:0.85em;
									width:50%;
								}
							</style>
						</head>
						<body>
							<center>
								<div class="main">
									<div class="header">
									</div>
									<div class="content">
										<h2 class="title">
											'.$username.'
										</h2>
										<p class="message">
											Ist dem Forum beigetreten und muss sich nun nur noch registrieren!
										</p>
									</div>
								</div>
								<div class="footer">
									"Bane of the Legion" ist ein fiktiver Zusammenschluss im MMORPG "World of Warcraft".<br>
									<br>
									Diese Mail dient einzig und allein der Information und Verifikation der dazugehörigen Personen.<br>
									Haben Sie diese E-Mail fälschlicherweise erhalten, leiten Sie diese bitte an "admin@baneofthelegion.de" weiter.
								</div>
							</center>
						</body>
					</html>
				';
				// Send Auth mail to User.
				$mail = NEW Mail($user_mail, "Foren Authentifizierung", '', $html);
					$mail->set_default_headers();
					$mail->send_imap_mail();
				// Send mail to Admin.
				$mail_self = NEW Mail("admin@baneofthelegion.de", "Neue Foren Registrierung!", '', $self_html);
					$mail_self->set_default_headers();
					$mail_self->send_imap_mail();
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