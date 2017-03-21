 <html>
    <body>
<form method="post" action="verify.php">
<?php
require_once('./system/security/captcha/recaptchalib.php');
  $publickey = "your_public_key"; // you got this from the signup page
  echo recaptcha_get_html($publickey);
?>
	<input type="submit" />
</form>
</body>
  </html>


<?php
  require_once('recaptchalib.php');
  $privatekey = "your_private_key";
  $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) {
    // What happens when the CAPTCHA was entered incorrectly
    die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
         "(reCAPTCHA said: " . $resp->error . ")");
  } else {
    // Your code here to handle a successful verification
  }
  ?>