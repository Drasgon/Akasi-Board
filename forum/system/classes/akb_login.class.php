<?php
/*
Copyright (C) 2016  Alexander Bretzke

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.


This class DOES depend on the session class, since there are some properties, that have to correlate with each other.
*/

class Login extends Session
{
	// Set default variables
	protected $_database;
	protected $_link;

	public $sessionCookie;
	public $sessionCookieValue;
	
	private $accountDoesExist 		= 'Account Exists';
	private $accountDoesNotExist 	= 'Account does not Exist';
	private $logMessages 	= ARRAY();
	private $outputMessages = ARRAY();
	private $outputMessage	= '';
	private $accountData 	= ARRAY();
	private $clientIp	= 0;
	private $username 	= '';
	private $password 	= '';
	private $passHash 	= '';
	private $userId 	= 0;
	private $extraVal 	= 0;
	private $cryptLevel	= 0;
	private $accepted	= 0;
	
	
	
	public function __construct()
	{
		parent::__construct();
		
		// var_dump(get_object_vars());
		$this->_link = parent::getClassProperty('_link');
		
		$this->sessionCookie = parent::getClassProperty('sessionCookie');
		
		
		$this->logMessages 		= ARRAY(
			'LoginDenied'		=>	'Die Login Anfrage wurde abgelehnt.',
			'UserBanned'		=>	'Der angegebene Account ist gesperrt!',
			'InvalidUser'		=>	'Der angegebene Username existiert nicht.',
			'InvalidPass'		=>	'Das angegebene Passwort passt nicht zum angegebenen Account.',
			'ValidPass'			=>	'Das angegebene Passwort passt zum angegebenen Account.',
			'BadLogins'			=>	'Die IP weist zu viele gescheiterte Login Anfragen auf',
			'NotVerified'		=>	'Dieser Account wurde noch nicht verifiziert!',
		);
		$this->outputMessages 	= ARRAY(
			'LoginDenied'		=>	'Ihre Login Anfrage wurde abgelehnt.',
			'UserBanned'		=>	'Dieser Account ist gesperrt!',
			'InvalidUser'		=>	'Ungültiger Username oder Passwort!',
			'InvalidPass'		=>	'Ungültiger Username oder Passwort!',
			'ValidPass'			=>	'',
			'BadLogins'			=>	'Sie haben zu viele fehlgeschlagene Login Versuche unternommen. Versuchen Sie es später erneut.',
			'NotVerified'		=>	'Dieser Account wurde noch nicht verifiziert!',
		);
		
	}
	
	
	public function checkAccount($username = '')
	{
		if($username == '' && isset($this->accountData['username']))
			$username = $this->accountData['username'];
		
		$query = $this->query('SELECT id, username, pass_hash, extra_val, crypt_level, accepted FROM '.$this->table_accounts.'  WHERE username = "'.$username.'" LIMIT 1');
		if($result = mysqli_fetch_object($query))
		{
			$this->userId 		= $result->id;
			$this->username 	= $result->username;
			$this->passHash 	= $result->pass_hash;
			$this->extraVal 	= $result->extra_val;
			$this->cryptLevel 	= $result->crypt_level;
			$this->accepted 	= $result->accepted;
			
			return TRUE;
		}
		else
			// If account does NOT exist
			return FALSE;
	}
	
	
	private function packAccountData()
	{
		$this->accountData = ARRAY(
			'userId'		=>	$this->userId,
			'username'		=>	$this->username,
			'databaseHash'	=>	$this->passHash,
			'inputHash'		=>	0,
			'clientIp'		=>	$this->clientIp,
			'extraVal'		=>	$this->extraVal,
			'cryptLevel'	=>	$this->cryptLevel,
			'accepted'		=>	$this->accepted
		);
	}
	
	
	public function getAccountDetails($username)
	{
		// Get at least the client ip, to possibly ban it from requesting too many logins
		$this->clientIp 				= $this->getUserIp();
		$this->accountData['clientIp']	= $this->clientIp;
		
		// Just check once to prevent multiple logging entries
		$canLogin = $this->canLogin();

		
		if($canLogin)
		{
			
			if($canLogin === 'BadLogins')
			{
				return;
			}
			if($canLogin === 'NotVerified')
			{
				return;
			}
			
			// If account exists
			if($this->checkAccount($username))
			{
				$this->packAccountData();
				
				// If account was not verified
				if($this->accountData['accepted'] == 0)
				{
					$this->addAccountLog($this->logMessages['NotVerified'], FALSE, NULL, $this->accountData['clientIp']);
					$this->setOutputMessage($this->outputMessages['NotVerified']);
					
					return;
				}
				 
				
				// If account is banned
				if($this->isBanned($this->accountData['userId']))
				{
					$this->addAccountLog($this->logMessages['UserBanned'], FALSE, NULL, $this->accountData['clientIp']);
					$this->setOutputMessage($this->outputMessages['UserBanned']);
					
					return FALSE;
				}
				else
					return $this->accountData;
			}
			// If account does NOT exist
			else
			{
				$this->addAccountLog($this->logMessages['InvalidUser'], FALSE, NULL, $this->accountData['clientIp']);
				$this->setOutputMessage($this->outputMessages['InvalidUser']);
				
				return FALSE;
			}
		}
		else
		{
			$this->addAccountLog($this->logMessages['LoginDenied'], FALSE, NULL, $this->accountData['clientIp']);
			$this->setOutputMessage($this->outputMessages['LoginDenied']);
		}
	}
	
	
	public function processInputHash($hash)
	{
		$this->accountData['inputHash'] = $hash;
	}
	
	
	public function compareHash($inputHash, $databaseHash)
	{
		if(empty($inputHash) || empty($databaseHash))
			return FALSE;
		
		if($inputHash == $databaseHash)
		{			
			$this->addAccountLog($this->logMessages['ValidPass'], FALSE, NULL, $this->accountData['clientIp']);
			$this->setOutputMessage($this->outputMessages['ValidPass']);
			
			return TRUE;
		}
		else
		{
			$this->addAccountLog($this->logMessages['InvalidPass'], FALSE, NULL, $this->accountData['clientIp']);
			$this->setOutputMessage($this->outputMessages['InvalidPass']);
			
			$this->increaseBadLogins($this->accountData['clientIp']);
			
			return FALSE;
		}
	}
	
	
	private function canLogin()
	{		
		if(!$this->hasTooManyLoginAttempts($this->accountData['clientIp']))
		{
			return TRUE;
		}
		else
		{
			if($this->hasTooManyLoginAttempts($this->accountData['clientIp']))
			{
				$this->addAccountLog($this->logMessages['BadLogins'], FALSE, NULL, $this->accountData['clientIp']);
				$this->setOutputMessage($this->outputMessages['BadLogins']);
				
				return 'BadLogins';
			}
		}
	}
	
	
	public function setSession($passwordRaw)
	{
		$this->generateSid($passwordRaw);
		
		if($this->query('UPDATE '.$this->table_sessions.' SET sid="'.mysqli_real_escape_string($this->_link, $this->sessionCookieValue).'" WHERE id=' . $this->accountData['userId']))
		{
			// Unwanted "Hack", since a LOT of queries still use this table for accessing the SID.
			$this->query('UPDATE '.$this->table_accounts.' SET sid="'.mysqli_real_escape_string($this->_link, $this->sessionCookieValue).'" WHERE id=' . $this->accountData['userId']);
				//setcookie($this->sessionCookie, $this->sessionCookieValue, time() + 3600 * 24 *365, '/');
			session_id($this->sessionCookieValue);
			
			$this->startSession();
			
			return TRUE;
		}
		else
			return FALSE;
		
	}
	
	
	public function generateSid($passwordRaw)
	{
		$this->sessionCookieValue	= md5($passwordRaw . ':' . $passwordRaw . time());
	}
	
	
	private function setOutputMessage($str)
	{
		return $this->outputMessage = $str;
	}
	
	
	public function getOutputMessage()
	{
		return $this->outputMessage;
	}
	
	
	
	
}
?>