<?php
if(ob_get_level() == 0)
{
// Start output buffering for working session cookies
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
    ob_start("ob_gzhandler");
else
    ob_start();
}
	
function login()
{
	
	require_once(dirname(__FILE__).'/../../classes/akb_mysqli.class.php');
	require_once(dirname(__FILE__).'/../../classes/akb_main.class.php');
	require_once(dirname(__FILE__).'/../../classes/akb_session.class.php');
	require_once(dirname(__FILE__).'/../../classes/akb_login.class.php');
	require_once(dirname(__FILE__).'/../../classes/akb_crypt.class.php');

	if (!isset($db) || $db == NULL)
	{
		$db = new Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);
	if (!isset($session) || $session == NULL)
		$session = new Session();
	if (!isset($login) || $login == NULL)
		$login = new Login();
	if (!isset($crypt) || $crypt == NULL)
		$crypt = new MD5Crypt();
	
	
	if($accountData = $login->getAccountDetails(mysqli_real_escape_string($connection, $_POST['username'])))
	{
		$accountData['inputHash'] 		= $crypt->getHash($_POST['password'], $accountData['extraVal'], $accountData['cryptLevel']);
		
		// If credentials are valid and the session is able to be built
		if($accountData['hashIsValid'] 	= $login->compareHash($accountData['inputHash'], $accountData['databaseHash']))
		{	
			if($login->setSession($_POST['password']))
			{
				echo '	<span id="response_success" class="responseSuccess">Login erfolgreich!</span>
						<meta http-equiv="refresh" content="3">';
			}
		}
		else
		{
			echo $login->getOutputMessage();
		}
	}
	else
	{
		echo $login->getOutputMessage();
	}

}

if(isset($_POST["username"]) && isset($_POST["password"])) login();

// End of the output buffering. Also the end of all content. Cookies can't be set after this.
	ob_end_flush();
?>