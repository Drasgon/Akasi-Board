<?php

// Re initialize the DB and Runtime Class
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
	
	
if($main->serverConfig("can_register"))
{
	// If session has at least user access, a valid user is already logged in. Simply abort the registration.
	if(!$main->checkSessionAccess('USER'))
	{

		if (isset($_GET['page']) && $_GET['page'] == 'Register') {
			// REGISTER TERMS
			if (!isset($_GET['subRegister'])) {
				if (!isset($_POST['decline']) || (isset($_POST['decline']) && $_POST['decline'] == false)) {
					
					include('tos.php');
					
					$errorStatus      = false;
					$termsNotAccepted = false;
					
					if (isset($_GET['page']) && $_GET['page'] == 'Register' && isset($_GET['action']) && $_GET['action'] == 'presubmit') {
						if (!$_POST['preRegister_terms'] && $_POST['accept']) {
							$errorStatus      = true;
							$termsNotAccepted = true;
						}
						if ($_POST['preRegister_terms'] && $_POST['accept']) {
							echo '<meta http-equiv="refresh" content="0;url=?page=Register&subRegister=registerForm">';
						}
					}
					
					$registerContainer = '
		<div class="registerContainer">
		<div class="registerMainContainer">
		  <div class="mainHeadline">
			<div class="headlineContainer">
			  <h1>
				<div class="icons" id="termofuse"></div> Registrierung - Nutzungsbestimmungen
			  </h1>
			</div>
			<p class="register_textmargin">Bitte lesen Sie diese Nutzungsbestimmungen aufmerksam und sorgfältig!<br>Verstöße können eine Sperrung, oder anderes, zur Folge haben!</p>
			';
					
					if ($errorStatus == true) {
						$registerContainer .= '<p class="error">Ihre Angaben sind ungültig. Überprüfen Sie die rot markierten Felder und versuchen Sie es erneut.</p>';
					}
					
					$registerContainer .= '</div><div class="registerMain">';
					
					$registerContainer .= $tos_string;
					$registerContainer .= '
		  <form class="registerPreForm no-smoothstate" method="POST" action="?page=Register&action=presubmit">';
					if ($termsNotAccepted == true) {
						$registerContainer .= '<div class="formContainer formError">';
					} else {
						$registerContainer .= '<div class="formContainer">';
					}
					$registerContainer .= '
		  <input type="checkbox" name="preRegister_terms" id="register_terms_read">
		  <label for="register_terms_read">
			 Ich habe die Nutzungsbestimmungen aufmerksam gelesen und akzeptiere diese.
		  </label>
		  </div>';
					if ($termsNotAccepted == true) {
						$registerContainer .= '<p class="innerError">Sie müssen die Nutzungsbestimmungen lesen und ihnen zustimmen.</p>';
					}
					$registerContainer .= '
			<script>
			$(document).ready(function() {
				$("#register_terms_read").change(function()
				{
					if(!$("#register_terms_read").prop("checked"))
					{
						$("#tosAccept").attr("disabled", "disabled");
					} else {
						$("#tosAccept").removeAttr("disabled");
					}
				});
			});
			</script>
		  <div class="submitPreForm">
		  <input type="submit" name="accept" value="Akzeptieren" id="tosAccept" disabled="disabled">
		  <input type="submit" name="decline" value="Ablehnen" id="tosDecline">
		  </div>
		  </form>
			</div>
		  </div>
		</div>';
					
					echo $registerContainer;
					
				} else {
					echo '<meta http-equiv="refresh" content="0;url=?page=Index">';
				}
			} else {
				if (isset($_GET['subRegister']) && $_GET['subRegister'] == 'registerForm') {
					
					// REGISTER FORM
					
					if (isset($_GET['page']) && $_GET['page'] == 'Register' && isset($_GET['subRegister']) && $_GET['subRegister'] == 'registerForm' && isset($_GET['action']) && $_GET['action'] == 'formsubmit') {
						$main->useFile('./system/controller/register/register.php');
						$register_dataArray = register();
					}
					
					// IF SUCCESS
					
					if (isset($register_dataArray) && ($register_dataArray['registerSuccess'] == false) || !isset($register_dataArray)) {
						
						// SUCCESS END
						
						
						$registerForm = '
		<div class="mainHeadline">
		  <div class="headlineContainer">
			<h2>
			  <div class="icons" id="register"></div><b>Registrieren</b>
			</h2>
		  </div>
		  ';
						if (isset($register_dataArray) && ($register_dataArray['errorStatus'] == true)) {
							$registerForm .= '<p class="error">Ihre Angaben sind ungültig. Überprüfen Sie die markierten Felder und versuchen Sie es erneut.</p>';
						}
						$registerForm .= '</div>
					<div class="userInfobox">
			<div class="userInfobox_inner">
				<div class="userInfobox_img">
					<img src="./images/3Dart/information.png">
				</div>
				<div>
					Um sich zu registrieren müssen Sie alle folgenden Formularfelder mit erlaubtem Inhalt ausfüllen. Richtlinien zum ausfüllen einzelner Felder erhalten Sie jeweils unter diesen.<br>
					Falls Probleme mit der Registrierung oder einem darauffolgendem Login entstehen, wenden Sie sich bitte an die Administration.<br>
					Weitere Informationen zur Nutzung dieser Software erhalten Sie <a href="?page=Index&threadID=2">hier</a>.
				</div>
			</div>
		</div>

		<form class="registerMain no-smoothstate" method="POST" action="?page=Register&subRegister=registerForm&action=formsubmit">
		  <div class="Container_reg">
			';
						
						
						if (isset($register_dataArray) && ($register_dataArray['everythingEmpty'] == true || $register_dataArray['usernameToShort'] == true || $register_dataArray['usernameToLong'] == true || $register_dataArray['usernameAlreadyInUse'] == true || $register_dataArray['usernameIllegalChars'] == true)) {
							
							$usernameErrorMsg = ($register_dataArray['everythingEmpty'] == true || $register_dataArray['usernameEmpty'] == true) ? 'Bitte geben Sie einen Benutzernamen ein.' : '';
							$usernameErrorMsg = ($register_dataArray['usernameToShort'] == true) ? 'Der Benutzername ist zu kurz.' : '';
							$usernameErrorMsg = ($register_dataArray['usernameToLong'] == true) ? 'Der Benutzername ist zu lang.' : '';
							$usernameErrorMsg = ($register_dataArray['usernameAlreadyInUse'] == true) ? 'Der Benutzername ist bereits in Benutzung.' : '';
							$usernameErrorMsg = ($register_dataArray['usernameIllegalChars'] == true) ? 'Der Benutzername beinhaltet unzulässige Zeichen.' : '';
							$posted_username  = (isset($_POST['username_reg'])) ? 'value="'.$_POST['username_reg'].'"' : '';
							
							$registerForm .= '<div class="formField_label formError">
			  <label for="username_reg">
			  Benutzername
			  </label>
			</div>
			<div class="reg_containerInput formError">
			  <input type="text" name="username_reg" id="username_reg" class="registerInputField" maxlength="13" autocomplete="disabled" autofill="disabled" '.$posted_username.'>
			  ' . $usernameErrorMsg . '
			</div>';
							
						} else {
							
							$registerForm .= '<div class="formField_label">
			  <label for="username_reg">
			  Benutzername
			  </label>
			</div>
			<div class="reg_containerInput">
			  <input type="text" name="username_reg" value id="username_reg" class="registerInputField" maxlength="13">
			</div>';
						}
						$registerForm .= '
			<div class="reg_containerInput_desc">
			  <ul>
				<li>
				  Der Benutzername muss mindestens 3 Zeichen und darf maximal 13 Zeichen lang sein.
				</li>
				<li>
				  Zeichen von a - Z sowie Zahlen von 0 - 9 sind erlaubt.
				</li>
				<li>
				  Sie können ihren Benutzernamen alle 365 Tage ändern. [ Zurzeit deaktiviert ]
				</li>
				<li>
				  Anstößige sowie gewaltverherrlichende oder verspottende Benutzernamen sind strengstens Verboten. Bei Verstoß ist mit Strafen in Form einer temporären Account Sperre oder/und Strafpunkten zu rechnen.
				</li>
			  </ul>
			</div>
		  </div>
		  
		  <fieldset class="registerFielset">
			<legend>E-Mail Adresse</legend>
			<div class="Container_reg">';
						
						
						$posted_mail = (isset($_POST['mail_reg'])) ? 'value="'.$_POST['mail_reg'].'"' : '';
						$posted_mail_repeat = (isset($_POST['mail_reg_repeat'])) ? 'value="'.$_POST['mail_reg_repeat'].'"' : '';
						
						if (isset($register_dataArray) && ($register_dataArray['everythingEmpty'] == true || $register_dataArray['mailEmpty'] == true || $register_dataArray['mailRepeatEmpty'] == true || $register_dataArray['mailNotMatching'] == true || $register_dataArray['mailError'] == true)) {
							
							$mailErrorMsg = ($register_dataArray['everythingEmpty'] == true || $register_dataArray['mailEmpty'] == true) ? 'Bitte geben Sie eine E-Mail Adresse ein.' : '';
							$mailErrorMsg = ($register_dataArray['mailError'] == true) ? 'Unzulässige E-Mail Adresse! Überprüfen Sie die Schreibweise der E-Mail und versuchen Sie es erneut. Sollte das Problem weiterhin bestehen, kontaktieren Sie die Administration.' : '';
							$mailError_repeat_Msg = ($register_dataArray['mailRepeatEmpty'] == true) ? 'Bitte Wiederholen Sie die oben angegebene E-Mail Adresse.' : '';
							$mailError_repeat_Msg = ($register_dataArray['mailNotMatching'] == true) ? 'Die Wiederholung stimmt nicht mit der E-Mail Adresse überein.' : '';
							
							
							$registerForm .= '
				<div class="formField_label formError">
					<label for="mail_reg">
						E-Mail Adresse
					</label>
				</div>
				<div class="reg_containerInput formError">
					<input type="text" name="mail_reg" id="mail_reg" class="registerInputField" autocomplete="disabled" autofill="disabled" '.$posted_mail.'>
						' . $mailErrorMsg . '
				</div>
				<div class="reg_containerInput_desc">
					<ul>
					  <li>
						Es sind alle Mail Provider erlaubt.
					  </li>
					  <li>
						Sie erhalten keine Werbemails bzw. Newsletter von uns, außer sie stimmen dem ausdrücklich durch eine Kennzeichnung des dafür vorgesehenen Feldes zu.
					  </li>
					</ul>
				</div>
			</div>
			<div class="Container_reg formError">
			  <div class="formField_label">
				<label for="mail_reg_repeat">
				E-Mail Adresse bestätigen
				</label>
			  </div>
			  <div class="reg_containerInput formError">
				<input type="text" name="mail_reg_repeat" id="mail_reg_repeat" class="registerInputField" autocomplete="disabled" autofill="disabled" '.$posted_mail_repeat.'>
				' . $mailError_repeat_Msg . '
			  </div>
			  <div class="reg_containerInput_desc">
				Wiederholen Sie zur Sicherheit ihre E-Mail Adresse.
			  </div>';
							
						} else {
							
							$registerForm .= '
			<div class="formField_label">
				<label for="mail_reg">
				E-Mail Adresse
				</label>
			  </div>
			  <div class="reg_containerInput">
				<input type="text" name="mail_reg" value id="mail_reg" class="registerInputField" autocomplete="disabled" autofill="disabled" '.$posted_mail.'>
			  </div>
			  <div class="reg_containerInput_desc">
				<ul>
				  <li>
					Es sind alle Mail Provider erlaubt.
				  </li>
				  <li>
					Sie erhalten keine Werbemails bzw. Newsletter von uns, außer sie stimmen dem ausdrücklich durch eine Kennzeichnung des dafür vorgesehenen Feldes zu.
				  </li>
				</ul>
			  </div>
			</div>
			<div class="Container_reg">
			  <div class="formField_label">
				<label for="mail_reg_repeat">
				E-Mail Adresse bestätigen
				</label>
			  </div>
			  <div class="reg_containerInput">
				<input type="text" name="mail_reg_repeat" value id="mail_reg_repeat" class="registerInputField" autocomplete="disabled" autofill="disabled" '.$posted_mail_repeat.'>
			  </div>
			  <div class="reg_containerInput_desc">
				Wiederholen Sie zur Sicherheit Ihre E-Mail Adresse.
			  </div>';
						}
						$registerForm .= '
				
			  
			</div>
		  </fieldset>
		  <fieldset class="registerFielset">
			<legend>Kennwort</legend>
			<div class="Container_reg">';
						
						
						if (isset($register_dataArray) && ($register_dataArray['everythingEmpty'] == true || $register_dataArray['passwordToShort'] == true || $register_dataArray['passwordToLong'] == true || $register_dataArray['passwordsNotMatching'] == true || $register_dataArray['passwordEmpty'] == true || $register_dataArray['passwordRepeatEmpty'] == true)) {
							$passwordErrorMsg         = '';
							$passwordError_repeat_Msg = '';
							$passwordErrorMsg .= ($register_dataArray['everythingEmpty'] == true || $register_dataArray['passwordEmpty'] == true) ? '<br>Bitte geben Sie ein Kennwort ein.' : '';
							$passwordError_repeat_Msg .= ($register_dataArray['passwordEmpty'] == false && $register_dataArray['passwordRepeatEmpty'] == true) ? '<br>Bitte Wiederholen Sie das abgegebene Kennwort.' : '';
							$passwordError_repeat_Msg .= ($register_dataArray['passwordsNotMatching'] == true) ? '<br>Die Kennwörter stimmen nicht überein' : '';
							$passwordError_repeat_Msg .= ($register_dataArray['passwordToLong'] == true) ? '<br>Das Passwort ist zu lang!' : '';
							$passwordError_repeat_Msg .= ($register_dataArray['passwordToShort'] == true) ? '<br>Das Passwort ist zu kurz!' : '';
							
							$registerForm .= '

						
			  <div class="formField_label formError">
				<label for="password_reg">
				Kennwort
				</label>
			  </div>
			  <div class="reg_containerInput formError">
				<input type="password" name="password_reg" value id="password_reg" class="registerInputField" autocomplete="off" maxlength="30">
				' . $passwordErrorMsg . '
			  </div>
			  <div class="reg_containerInput_desc">
				<ul>
				  <li>
					Das Kennwort darf maximal 30 Zeichen lang sein
				  </li>
				  <li>
					Es sind sämtliche Zeichen erlaubt.
				  </li>
				  <li>
					Die Felder "Kennwort" und "Kennwort bestätigen" müssen das selbe Passwort beinhalten.
				  </li>
				  <li>
					Das Kennwort kann nach der Registrierung beliebig oft geändert werden.
				  </li>
				</ul>
			  </div>
			</div>
			<div class="Container_reg">
			  <div class="formField_label formError">
				<label for="password_reg_repeat">
				Kennwort bestätigen
				</label>
			  </div>
			  <div class="reg_containerInput formError">
				<input type="password" name="password_reg_repeat" value id="password_reg_repeat" class="registerInputField" autocomplete="off" maxlength="30">
				' . $passwordError_repeat_Msg . '
			  </div>
			  <div class="reg_containerInput_desc">
				Wiederholen Sie zur Sicherheit ihr Kennwort.
			  </div>';
							
						} else {
							
							$registerForm .= '
			
			
			  <div class="formField_label">
				<label for="password_reg">
				Kennwort
				</label>
			  </div>
			  <div class="reg_containerInput">
				<input type="password" name="password_reg" value id="password_reg" class="registerInputField" autocomplete="off" maxlength="30">
			  </div>
			  <div class="reg_containerInput_desc">
				<ul>
				  <li>
					Das Kennwort darf maximal 30 Zeichen lang sein
				  </li>
				  <li>
					Es sind sämtliche Zeichen erlaubt.
				  </li>
				  <li>
					Die Felder "Kennwort" und "Kennwort bestätigen" müssen das selbe Passwort beinhalten.
				  </li>
				  <li>
					Das Kennwort kann nach der Registrierung beliebig oft geändert werden.
				  </li>
				</ul>
			  </div>
			</div>
			<div class="Container_reg">
			  <div class="formField_label">
				<label for="password_reg_repeat">
				Kennwort bestätigen
				</label>
			  </div>
			  <div class="reg_containerInput">
				<input type="password" name="password_reg_repeat" value id="password_reg_repeat" class="registerInputField" autocomplete="off" maxlength="30">
			  </div>
			  <div class="reg_containerInput_desc">
				<ul>
					<li>
						Wiederholen Sie zur Sicherheit ihr Kennwort.
					</li>
				</ul>
			  </div>';
							
							
						}
						
						
						$registerForm .= '	  
			</div>
		  </fieldset>
		  <fieldset class="registerFielset">
			<legend>Sicherheitsabfrage</legend>
			<div class="Container_reg">';
						
						
			$security->setQuestion(); // Sets a random question
			$securityQuestion = $security->getQuestion();
						
						
	if (isset($register_dataArray) && ($register_dataArray['everythingEmpty'] == true || $register_dataArray['captchaEmpty'] == true || $register_dataArray['captchaError'] == true)) {
		print_r($register_dataArray);
		
		$captchaMsg = '';
		
		if(isset($register_dataArray) && ($register_dataArray['everythingEmpty'] == true || $register_dataArray['captchaEmpty'] == true))
			$captchaMsg = 'Sie müssen die Sicherheitsabfrage ausfüllen!';
		else
		if(isset($register_dataArray) && ($register_dataArray['captchaError'] == true))
			$captchaMsg = 'Die eingegebene Zeichenfolge war falsch!';
		
		$registerForm .= '
			
			  <div class="formField_label formError">
				<label>
				Frage 
				</label>
				<p class="registerQuestion">
					'.$securityQuestion.'
				</p>
				<label for="security_question">
				Antwort 
				</label>
			  </div>
			  <div class="reg_containerInput formError">
				<input type="text" name="security_question" id="security_question" size="150" maxlength="150" class="registerInputField"  autocomplete="off"/>
				' . $captchaMsg . '
			  </div>
			  <div class="reg_containerInput_desc">
				Antworten Sie auf die oben stehende Frage. Groß und Kleinschreibung müssen dabei nicht beachtet werden.
			  </div>
			</div>
			<div class="Container_reg">';
							
	} else {
							
							$registerForm .= '
				<div class="formField_label">
				<label>
				Frage 
				</label>
				<p class="registerQuestion">
					'.$securityQuestion.'
				</p>
				<label for="security_question">
				Antwort 
				</label>
			  </div>
			  <div class="reg_containerInput">
				<input type="text" name="security_question" id="security_question" size="150" maxlength="150" class="registerInputField"  autocomplete="off"/>
			  </div>
			  <div class="reg_containerInput_desc">
				Antworten Sie auf die oben stehende Frage. Groß und Kleinschreibung müssen dabei nicht beachtet werden.
			  </div>
			</div>';
			
	}
						
			$registerForm .= '
		  </fieldset>
		  <div class="reg_containerInput_desc_bottom">
				<ul>
				  <li>
					Mit der Registrierung stimmen Sie den zuvor genannten <a target="_blank" href="?page=tos">Nutzungsbedingungen</a> zu.
				  </li>
				</ul>
			  </div>
		  <div class="submitPreForm">
			<input type="submit" name="accept" value="Absenden">
			<input type="reset" value="Zurücksetzen">
		  </div>
		</form>';
						
						echo $registerForm;
						
					}
					if (isset($register_dataArray) && ($register_dataArray['registerSuccess'] == '1')) {
						require('./system/interface/successpage.php');
						throwSuccess("Registrierung erfolgreich!<br>Sie werden in Kürze eine Bestätigungs E-Mail erhalten, welcher weitere Instruktionen beigelegt sind.", "?page=Index");
					}
				}
			}
		}
	}
	else
	{
		throwError("Sie sind bereits eingeloggt! Wozu ein neuer Benutzeraccount?");
	}
}
else
{
	throwError("Die Registrierung ist zurzeit deaktiviert");
}
?>