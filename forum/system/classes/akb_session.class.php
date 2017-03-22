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


This class is property to be designed to handle all session activities a user creates.
It works with the global "$_SESSION", so it is immediately functional everywhere - from the point it was initialized.
*/

class Session extends Board
{
	// Set default variables
	protected $_database;
	protected $_link;

	/*
	**	SESSION DATA BEGIN
	*/
	
	private $sessionIdName 				= 'ID';
	private $sessionIp			 		= 'IP';
	private $sessionUserId 				= 'USERID';
	private $sessionUsername 			= 'USERNAME';
	private $sessionLastActivity 		= 'LASTACTIVITY';
	private $sessionStatus 				= 'STATUS';
	private $sessionAvatar 				= 'AVATAR';
	private $sessionUserAccess 			= 'USERACCESS';
	private $sessionModAccess 			= 'MODACCESS';
	private $sessionAdminAccess 		= 'ADMINACCESS';
	private $sessionPermissionId 		= 'PERMISSIONID';
	private $sessionPermissionName 		= 'PERMISSIONNAME';
	private $sessionCookie 				= 'PHPSESSID';
	private $sessionCookieDeletedValue 	= 'deleted';
	private $sessionCookieValue;
	private $storedSessionId;
	
	private $activityUpdateInterval = 600;
	
	/*
	**	SESSION DATA END
	*/
	
	// Default values for additional Session data are: False || 0 || none
	private $sessionData = ARRAY();
	private $defaultSessionData = ARRAY();
	
	// Permission Data
	private $userPermissions 			= ARRAY();
	private $modPermissions 			= ARRAY();
	private $adminPermissions			= ARRAY();
	
	
	public function __construct()
	{
		parent::__construct();
		
		// var_dump(get_object_vars());
		$this->_link = parent::getClassProperty('_link');
		
		
		/*
		**	Here, you'll find all the available session data.
		**	To add / modify data, simply create a new private variable in the "Session Data" section above.
		**	Then, create / modify the default permissions in THIS section here.
		**	Valid default values: 0 || false
		**	Also, you can use empty or simply non-empty strings
		*/
		
		$this->defaultSessionData = ARRAY(
			$this->sessionIdName 				=> 0,
			$this->sessionIp	 				=> 0,
			$this->sessionUserId				=> 0,
			$this->sessionUsername				=> '',
			$this->sessionAvatar				=> $this->getDefaultAvatar(),
			$this->sessionLastActivity			=> 0,
			$this->sessionStatus				=> FALSE,
			$this->sessionUserAccess 			=> FALSE,
			$this->sessionModAccess				=> FALSE,
			$this->sessionAdminAccess 			=> FALSE,
			$this->sessionPermissionId			=> 0,
			$this->sessionPermissionName		=> 'Gast'
		);
		
		$this->userPermissions = ARRAY(
			$this->sessionUserAccess			=> TRUE,
			$this->sessionModAccess				=> FALSE,
			$this->sessionAdminAccess 			=> FALSE,
			$this->sessionPermissionId			=> 1,
			$this->sessionPermissionName		=> 'User'
		);
		
		$this->modPermissions = ARRAY(
			$this->sessionUserAccess			=> TRUE,
			$this->sessionModAccess				=> TRUE,
			$this->sessionAdminAccess 			=> FALSE,
			$this->sessionPermissionId			=> 2,
			$this->sessionPermissionName		=> 'Moderator'
		);
		
		$this->adminPermissions = ARRAY(
			$this->sessionUserAccess			=> TRUE,
			$this->sessionModAccess				=> TRUE,
			$this->sessionAdminAccess 			=> TRUE,
			$this->sessionPermissionId			=> 3,
			$this->sessionPermissionName		=> 'Administator'
		);
		
		// Directly use the default data first. Just in case of an error, so even ANY data will be available throughout the other scripts.
		$this->sessionData = $this->defaultSessionData;
	}
	
	
	
	// Start a session.
	protected function startSession()
	{
		// If session is not present
		if(!isset($_SESSION) || (isset($_SESSION) && empty($_SESSION)))
		{
			// Check for session cookie
			if($this->readSessionCookie())
			{
				// If it is present, start a new session or continue the last one.
				if(!isset($_SESSION))
					session_start();
				return TRUE;
			}
			else
			{
				session_start();
				
				// Session started, but session cookie is missing.
				return FALSE;
			}
		}
		else
			return TRUE;
	}
	
	
	
	// Check wether the session cookie is present, or not.
	// Return false in case of no presency and true in case of success.
	private function readSessionCookie()
	{
		// If session cookie is not present
		if(!isset($_COOKIE[$this->sessionCookie]) 
		|| (isset($_COOKIE[$this->sessionCookie]) && empty($_COOKIE[$this->sessionCookie])) 
		|| (isset($_COOKIE[$this->sessionCookie]) && $_COOKIE[$this->sessionCookie] == $this->sessionCookieDeletedValue))

			return FALSE;
		else
		{
			$this->sessionCookieValue = mysqli_real_escape_string($this->_link, $_COOKIE[$this->sessionCookie]);
			
			return TRUE;
		}
	}
	
	
	// Merge all additional session data with the current session
	private function mergeSessionData()
	{
		/*
		** Merge the additional data with the session superglobal.
		** At this point, it MUST be absolutely sure, the session is 100% valid, since there's no point of return.
		*/ 
		$_SESSION = array_merge($_SESSION, $this->sessionData);
	}

	
	
	// Check database for occurencies of the current stored session id
	private function getDatabaseSessionInfo()
	{
		$result = $this->query('SELECT '.$this->table_accounts.'.id, '.$this->table_accdata.'.username, '.$this->table_accdata.'.avatar, '.$this->table_sessions.'.sid, '.$this->table_sessions.'.online
		FROM '.$this->table_accounts.'
		INNER JOIN '.$this->table_accdata.' ON '.$this->table_accounts.'.id = '.$this->table_accdata.'.account_id
		INNER JOIN '.$this->table_sessions.' ON '.$this->table_accounts.'.id = '.$this->table_sessions.'.id
		WHERE '.$this->table_sessions.'.sid = "'.$this->getStoredSessionId().'"');
		
		// EXPECTED RESULT: 
		// [id] => 1 [username] => Arenima [avatar] => ./images/avatars/avatar-836798a11d0fdb57f595d73a92f8b105.png [sid] => vr0bof10jn8fqn7a55jtu9ocm2 [online] => 0
		// At minimum query time (IMPORTANT)
		
		// If no occurency was found
		if (mysqli_num_rows($result) == 0 || mysqli_num_rows($result) > 1)
		{
			$this->destroySession();
		
			return FALSE;
		}
		else
		{
			$data = mysqli_fetch_object($result);
		
			$this->sessionData[$this->sessionUserId] 	= $data->id;
			$this->sessionData[$this->sessionIdName] 	= $data->sid;
			$this->sessionData[$this->sessionUsername] 	= $data->username;
			$this->sessionData[$this->sessionAvatar] 	= $data->avatar;
			$this->sessionData[$this->sessionStatus] 	= $data->online;
			$this->sessionData[$this->sessionIp] 		= $this->getUserIp();

				$this->mergeSessionData();
				
			$this->sessionData['USERID'] = $this->getUserId();
				$this->mergeSessionData();
				
			return TRUE;
		}
	}
	
	
	
	// Munch all those nommy session cookiez and turn davy jones wit' it
	private function destroySession()
	{
		setcookie($this->sessionCookie, '', time() - 3600);
		if(session_status() == 'PHP_SESSION_DISABLED')
			session_destroy();
		$_SESSION = $this->defaultSessionData;
	}
	
	
	
	// Set the stored session id in the class scope and return it.
	private function setStoredSessionId($sessionId)
	{
		return $this->storedSessionId = $sessionId;
	}
	
	
	
	// Get the stored session id in the class scope and return it.
	private function getStoredSessionId()
	{
		if(empty($this->storedSessionId))
			$this->setStoredSessionId($this->sessionCookieValue);
		
		return $this->storedSessionId;
	}
	
	
	
	// Check if the session is stored in "session_id()" - leave out the database at this point.
	private function checkSessionValidity()
	{
		if($this->startSession() && $this->readSessionCookie() == $this->getStoredSessionId())
		{
			if($this->getDatabaseSessionInfo())
				return TRUE;
			else
				return FALSE;
		}
		else
			return FALSE;
	}
	
	public function updateSessionStatus()
	{
		if(!isset($_SESSION['lastActivity']) || (isset($_SESSION['lastActivity']) && (time() - $_SESSION['lastActivity'] >= $this->activityUpdateInterval)) && (!isset($GLOBALS['ajaxStatus']) || (isset($GLOBALS['ajaxStatus']) && $GLOBALS['ajaxStatus'] == false)))
		{
			$queryReslt = $this->query('UPDATE '.$this->table_sessions.' SET online=1, last_activity=' . time() . ' WHERE id=' . $_SESSION['USERID']);
			
			$_SESSION['lastActivity'] = time();
		}
	}
	
	
	private function setSessionStatusActive()
	{
		return $this->sessionData[$this->sessionStatus] = 1;
	}
	
	
	
	private function buildSessionPermissions()
	{
		if($_SESSION['USERACCESS'] == NULL || $_SESSION['MODACCESS'] == NULL || $_SESSION['ADMINACCESS'] == NULL) {
			
			$usePermissionData = ARRAY();
			
			switch(intval(parent::getAccountSecurity($_SESSION['USERID'])))
			{
				case 1:
					$usePermissionData = $this->userPermissions;
				break;
				case 2:
					$usePermissionData = $this->modPermissions;
				break;
				case 3:
					$usePermissionData = $this->adminPermissions;
				break;
			}
			
			foreach($usePermissionData as $key => $value)
				$this->sessionData[$key] = $value;
			
		}
		else
			return true;
	}
	
	
	
	private function outputSessionData($state = 0)
	{
		if(parent::getClassProperty('development_mode') == 1)
		{
			if($state == 1 || $state == 0)
			{
				echo '<br>Final Session Superglobal Data:: ';
				print_r($_SESSION);
			}
			if($state == 2 || $state == 0)
			{
				echo '<br>Final additional Session Data:: ';
				print_r($this->sessionData);
			}
		}
	}
	
	
	
	// Set the session id key with its corresponding value.
	public function initializeSession()
	{
		if ($this->checkSessionValidity())
		{
			$_SESSION[$this->sessionIdName] = $this->getStoredSessionId();
			$this->setSessionStatusActive();
			$this->updateSessionStatus();
			$this->buildSessionPermissions();
		}
		else
		{
			$this->destroySession();
		}
		
		$this->mergeSessionData();
		
		$this->outputSessionData(2);
	}
}
?>