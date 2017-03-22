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

Forum class for Akasi Board ©
Copyright 2016, Alexander Bretzke - All rights reserved
*/
class Board extends Database
{	
	// Declare MySql Variables
    protected $_database;
    protected $_link;
	
	protected $bad_login_time	=	"-30 minutes";
	protected $maxLoginAttempts	=	3;
	
	protected $userData = ARRAY();
	protected $currentUserData = ARRAY();
	
	private $max_update_diff = 600; // Min. time between user status updates in seconds
	protected $development_mode = FALSE; // Whether or not to show runtime errors
	
	// Path to main folder - Relative to/from web root
	// USAGE: MUST contain backslashes instead of normal ones.
	// MORE USAGE: This functions as a delimiter for the working directory!
	// EVEN MORE USAGE!: The only thing needed here, is the name of the directory, that contains the index.php. Not more, not less.
	// If the depth is 1: Just the directory name. NOT A PATH!
	private $cwdir	=	'forum';
	
	// Path to web root - Relative to apache root
	// USAGE: Can contain normal slashes. - NOT NECCESSARY! It's recommended to leave this blank!
	private $workDir =	'';
	
	public $includeData = '';
	
	// Key: Number of posts
	// Value: Rank
	public $userRanks = ARRAY(
		0		=>	1,
		10		=>	2,
		25		=>	3,
		50		=>	4,
		100		=>	5,
		150		=>	6,
		250		=>	7,
		400		=>	8,
		700		=>	9,
		1000	=>	10,
		2000	=>	11,
		3500	=>	12,
		5000	=>	13,
		7000	=>	14,
		9000	=>	15,
		11000	=>	16,
		13000	=>	17,
		15000	=>	18,
		20000	=>	19,
		25000	=>	20,
		9999999	=>	21 // Kind of a hack to cheat with our system - NEEDED IN EVERY CASE
	);
	
	public $userRanksDisplay = ARRAY(
		1	=>	ARRAY(1, 'Rank1'),
		2	=>	ARRAY(2, 'Rank1'),
		3	=>	ARRAY(3, 'Rank1'),
		4	=>	ARRAY(4, 'Rank1'),
		5	=>	ARRAY(5, 'Rank1'),
		6	=>	ARRAY(1, 'Rank2'),
		7	=>	ARRAY(2, 'Rank2'),
		8	=>	ARRAY(3, 'Rank2'),
		9	=>	ARRAY(4, 'Rank2'),
		10	=>	ARRAY(5, 'Rank2'),
		11	=>	ARRAY(1, 'Rank3'),
		12	=>	ARRAY(2, 'Rank3'),
		13	=>	ARRAY(3, 'Rank3'),
		14	=>	ARRAY(4, 'Rank3'),
		15	=>	ARRAY(5, 'Rank3'),
		16	=>	ARRAY(1, 'Rank4'),
		17	=>	ARRAY(2, 'Rank4'),
		18	=>	ARRAY(3, 'Rank4'),
		19	=>	ARRAY(4, 'Rank4'),
		20	=>	ARRAY(5, 'Rank4')
	);

	
	/*****
         * Build Variables for access to table names
         *
         * @ PARAM:
		 * 1.: Database class
		 * 2.: MySqli link
    ******/
    public function __construct()
    {
		if($this->development_mode == TRUE)
		{
			ini_set('display_startup_errors',1);
			ini_set('display_errors',1);
			error_reporting(-1);
		}
		else
		{
			ini_set('display_startup_errors',0);
			ini_set('display_errors',0);
			error_reporting(0);
		}
		
		parent::__construct();
        
		$this->_link = parent::mysqli_db_connect();
		
		// var_dump(get_object_vars(parent::mysqli_db_connect()));
		
		if(!empty($this->workDir))
			chdir($this->workDir);
		$this->cwd		 = getcwd();
		$this->cwd		 = explode($this->cwdir, $this->cwd);
		$this->cwd		 = $this->cwd[0].$this->cwdir."/";
		$this->cwd		 = str_replace('\\', '/', $this->cwd);
		
		$type = setlocale(LC_TIME, 'de_DE.UTF8');
    }
	
	
	/*****
         * Custom include against include issues
         *
         * @ PARAM:
		 *
		 * 1.: Path of the file - Relative to the index file
		 * 2.: Mode. Leave blank vor default include. 1 for once.
    ******/
	public function useFile($path, $include_once = NULL)
	{
		if($include_once == 1)
			require_once($this->cwd.$path);
		else
			require($this->cwd.$path);
			
		return true;
	}
	
	
	public function getRoot()
	{
		return $this->cwd;
	}
	

	/*public function url($file = '', $query = null) {
		$file=   ltrim($file, '/');
		$proto = isset($_SERVER['HTTPS']) ? 'https' : 'http';
		$url = $proto . ':/dignum-aliorum' . $_SERVER['HTTP_HOST'] . '/' . $file;
			  
		if(is_array($query)) {
			$url .= '?' . http_build_query($query);
		}
			return $url;
	}*/
	
	
	public function buildThreadUrl($threadID, $threadName)
	{
		$threadName = str_replace(' ', '-', $threadName);
		$threadName = str_replace("'", '', $threadName);
		$threadName = str_replace('"', '', $threadName);
		
		
		$url = '?page=Index&threadID='.$threadID.'/'.$threadName;
		
		return $url;
	}
    
	
	/*****
         * Get a specific thread name
         *
         * @ PARAM:
		 * 1.: Id of the thread
    ******/
    public function getThreadName($threadID)
    {
		// Escape the "Input"
        $threadID     = mysqli_real_escape_string($this->_link, $threadID);
		
		// Execute a query to get the thread title
        $getName      = $this->query("SELECT title FROM (".$this->table_thread.") WHERE id=('" . $threadID . "')");
		
		// Fetch the query result and return it
        $resolveName = mysqli_fetch_object($getName);
            $name = $resolveName->title;
        
        return $name;
    }
	
	
	public function getThreadBoardID($threadID)
	{
			// Escape the "Input", just to be sure
			$threadID     = mysqli_real_escape_string($this->_link, $threadID);
			
			// Execute a query to get the thread title
			$getID      = $this->query("SELECT main_forum_id FROM (".$this->table_thread.") WHERE id=('" . $threadID . "')");
			
			// Fetch the query result and return it
			$resolveID = mysqli_fetch_object($getID);
				$main_forum_id = $resolveID->main_forum_id;
			
			return $main_forum_id;
	}
	
	
	public function boardConfig($boardID, $config)
	{
			// Escape the "Input", just to be sure
			$boardID     = mysqli_real_escape_string($this->_link, $boardID);
			
			// Execute a query to get the thread title
			$getConfig      = $this->query("SELECT $config FROM (".$this->table_boards.") WHERE id=('" . $boardID . "')");
			
			// Fetch the query result and return it
			$result = mysqli_fetch_object($getConfig);
			$data = $result->{$config};
			
			return $data;

	}
    
    
	/*****
         * Collect all user data in an array and return it
         *
         * @ PARAM:
		 *
		 * 1.: Value of the field to search in.
		 *     E.g.: Session ID, Username, Posts . .  .
		 * 2.: Type of the search. It HAVE TO equal to the given Value(first parameter) AND the database field.
		 *     E.g.: Session ID = sid, Username = username, Posts = post_counter . . .
    ******/
    public function getUserdata($value, $type='account_id')
    {
		// Escape the "Input", to be 100 per cent sure
        $value		= mysqli_real_escape_string($this->_link, $value);
		$type			= mysqli_real_escape_string($this->_link, $type);

		if(!isset($this->userData[$type][$value]))
		{
			if($value != "0")
			{
			
				// What should be selected?
				$selectors = "".$this->table_accdata.".account_id, ".$this->table_accdata.".username, ".$this->table_accdata.".gender, 
								".$this->table_accdata.".avatar, ".$this->table_accdata.".avatar_border, ".$this->table_accdata.".post_counter, 
								".$this->table_accdata.".signature, ".$this->table_accdata.".email, ".$this->table_accdata.".user_rank, 
								".$this->table_accdata.".user_title, ".$this->table_accdata.".messenger_skype, ".$this->table_accdata.".profile_views, 
								".$this->table_sessions.".online, ".$this->table_accdata.".character_realm, ".$this->table_accdata.".character_name";
					
					// Build first part of the query
					$getData = "SELECT $selectors FROM (".$this->table_accdata.") 
								INNER JOIN ".$this->table_profile." ON ".$this->table_accdata.".account_id = ".$this->table_profile.".id
								INNER JOIN ".$this->table_sessions." ON ".$this->table_accdata.".account_id = ".$this->table_sessions.".id
								WHERE ";
						
						// Depending on the "type" parameter use another "WHERE" value
						switch($type)
						{
							// If the type is "sid"
							case 'sid':
									$getData .= "account_id=(SELECT id FROM (".$this->table_sessions.") WHERE sid=('" . $value . "'))";
								break;
								
							// If the type is "id"
							case 'id':
									$getData .= "account_id=('" . $value . "')";
								break;
								
							// If the type parameter contains a column that can be accessed directly in the accounts table
							default:
									$getData .= "$type=('" . $value . "')";
								break;
						
						}
						
						// Execute the built query
						$getData = $this->query($getData);
						
						// Fetch all query data and throw them straight into an array
						if ($resolveData = mysqli_fetch_object($getData)) {
						
							$account_id	=	$resolveData->account_id;
							$username	=	$resolveData->username;
						
							if(!$accepted = $this->CheckUserVerification($account_id))
								$username = '<strike>'.$username.'</strike>';
						
							$this->currentUserData = array(
								
								'account_id' 	=> $account_id,
								'name' 			=> $username,
								'gender' 		=> $resolveData->gender,
								'avatar' 		=> $this->checkUserAvatar($resolveData->avatar),
								'avatar_border'	=> $resolveData->avatar_border,
								'signature'		=> $resolveData->signature,
								'title'			=> $resolveData->user_title,
								'rank'			=> $resolveData->user_rank,
								'posts' 		=> $resolveData->post_counter,
								'email' 		=> $resolveData->email,
								'msngr_skype' 	=> $resolveData->messenger_skype,
								'profile_views' => $resolveData->profile_views,
								'accepted'		=> $accepted,
								'online'		=> $resolveData->online,
								'character_realm' 	=> $resolveData->character_realm,
								'character_name' 	=> $resolveData->character_name
							);
							
						}
			}
			else
			{
				// If is unregistered
				
				$this->currentUserData = array(
					
					'account_id' 	=> '0',
					'name' 			=> 'Gast',
					'gender' 		=> '1',
					'avatar' 		=> $this->getDefaultAvatar(),
					'avatar_border'	=> '36,103,20,0.64',
					'signature'		=> '',
					'title'			=> 'Unregistriert',
					'rank'			=> '',
					'posts' 		=> 'n.A',
					'email' 		=> 'n.A',
					'msngr_skype' 	=> '',
					'profile_views' => 'n.A',
					'accepted'		=> false,
					'character_realm' 	=> '',
					'character_name' 	=> ''
				);
			}
			
			$this->userData[$type][$value] = $this->currentUserData;
			
		}
		else
			$this->currentUserData = $this->userData[$type][$value];
				
		// Give $this->userData a value, if the query fails or does not returns any data
		if (!isset($this->currentUserData) || empty($this->currentUserData)) { $this->currentUserData= 'No data returned.'; }
		
		
        
		// Return the entire thing
        return $this->userData[$type][$value];
    }
	
	
	public function getUsername($userid = NULL)
	{
		if($userid == NULL)
		{
			$this->sid = $_SESSION['ID'];
			$data = $this->getUserdata($this->sid, "sid");
			
				return ($username = $data['name']);
		}
		else
		{	
			$data = $this->getUserdata($userid, "id");
				return ($username = $data['name']);
		}
	}
	
	
	public function getUseravatar($userid = NULL)
	{
		if($userid == NULL)
		{
			$this->sid = $_SESSION['ID'];
			$data = $this->getUserdata($this->sid, "sid");
			
				$avatar = $this->userData['avatar'];
		}
		else
		{
			$data = $this->getUserdata($userid, "id");
				
				$avatar = $data['avatar'];
		}
		
		return $this->checkUserAvatar($avatar);
	}
	
	
	public function getUserId()
	{
		$this->sid = $_SESSION['ID'];
		$data = $this->getUserdata($this->sid, "sid");
		
		if(!empty($data))
			return ($userId = $data['account_id']);
		else
			return false;
	}
	
	
	public function displayEmoticons($dir = 'images/emoticons')
	{
		   $files = glob($dir."/*.*");
		   
		   $images = '';

			for ($i=1; $i<count($files); $i++)

			{

				$image = $files[$i];

				$images .= '<img src="'.$image .'" />'."<br />";
				
			}
			
		return $images;
	}
	
	public function convertTime($time)
	{
		if($time == NULL)
			return 0;
	
			if (date('Y-m-d', $time) == date('Y-m-d')) {
                $timeConverted = strftime('<span class="timeRange">Heute</span>, %H:%M', $time);
            } elseif (date('Y-m-d', $time) == date('Y-m-d', strtotime("Yesterday"))) {
                $timeConverted = strftime('<span class="timeRange">Gestern</span>, %H:%M', $time);
            } elseif (date('Y-m-d', $time) < date('Y-m-d', strtotime("Yesterday"))) {
                $timeConverted = strftime("%A, %d %B %Y %H:%M", $time);
            }
			
		return utf8_encode($timeConverted);
	}
	
	public function detectUnreadThread($threadID)
	{
			$searchThread = $this->query("SELECT account_id FROM (".$this->table_forum_read.") WHERE account_id=(SELECT id FROM (".$this->table_sessions.") WHERE sid = ('".$_SESSION['ID']."')) AND thread_id=('".$threadID."')");
			
			if(mysqli_num_rows($searchThread) >= 1)
			{
				return true;
			} else {
				return false;
			}
	}
	
	public function detectUnreadThreadsInBoard($boardID)
	{
			if(isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true)
			{
				$searchBoard = $this->query("SELECT COUNT(*) AS count_threads FROM (".$this->table_thread.") WHERE main_forum_id=('" . $boardID . "')");
					
				$resolveData = mysqli_fetch_object($searchBoard);
				$totalThreads = $resolveData->count_threads;
				
				$searchUnreads = $this->query("SELECT COUNT(*) AS count_unreads FROM (".$this->table_forum_read.") WHERE account_id=(SELECT id FROM (".$this->table_accounts.") WHERE sid='" . $_SESSION['ID'] . "') AND board_id=('" . $boardID . "')");
					
				$unreadsResult = mysqli_fetch_object($searchUnreads);
				$totalUnreads = $unreadsResult->count_unreads;
				
				
				if ($totalThreads - $totalUnreads <= 0 || (!isset($_SESSION['STATUS']) || $_SESSION['STATUS'] == false)) {
						$totalUnreads =	false;
					} else {
					
						$totalUnreads		=	$totalThreads - $totalUnreads;
						$totalUnreads		=	" ($totalUnreads)";
					}
					
				return $totalUnreads;
			}
			
			else {
				return false;
			}
	}
	
	public function closetags($html) {
			  #put all opened tags into an array
			  preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
			  $openedtags = $result[1];
			 
			  #put all closed tags into an array
			  preg_match_all('#</([a-z]+)>#iU', $html, $result);
			  $closedtags = $result[1];
			  $len_opened = count($openedtags);
			  # all tags are closed
			  if (count($closedtags) == $len_opened) {
				return $html;
			  }
			  $openedtags = array_reverse($openedtags);
			  # close tags
			  for ($i=0; $i < $len_opened; $i++) {
				if (!in_array($openedtags[$i], $closedtags)){
				  $html .= '</'.$openedtags[$i].'>';
				} else {
				  unset($closedtags[array_search($openedtags[$i], $closedtags)]);
				}
			  }
			  return $html;
	}
	
	public function serverConfig($config_name)
	{
			$config_name = mysqli_real_escape_string($this->_link, $config_name);
			
			$getValue = $this->query("SELECT option_value FROM (".$this->table_configs.") WHERE option_name = ('".$config_name."')");
			$resolveValue = mysqli_fetch_object($getValue);
			
			if(!$resolveValue)
				$this->mysqli_db_error("Option '".$config_name."' not found.", "Option can't be found.", $this->mysql_errors["option"]);
			else
				return $resolveValue->option_value;
	}
	
	public function serverConfigSet($config_name, $new_value)
	{
			$config_name = mysqli_real_escape_string($this->_link, $config_name);
			$new_value = mysqli_real_escape_string($this->_link, $new_value);
			
			$setValue = $this->query("UPDATE (".$this->table_configs.") SET option_value = ('".$new_value."') WHERE option_name = ('".$config_name."')");
	}
	
	// CHECK ONLINE STATUS FOR EVERY USER
	
	public function updateUserStatus()
	{
		$lastUpdate = $this->serverConfig("last_user_status_update");
		
		if(time() - $lastUpdate >= $this->max_update_diff)
		{
			$this->query("UPDATE ".$this->table_sessions." SET online=0 WHERE UNIX_TIMESTAMP()-last_activity >= 600");
			
			$this->serverConfigSet("last_user_status_update", time());
		}
	}
	
	// Check wheter or not there are any bad logins within of the specified time
	public function checkBadLogins($userIp)
	{
		if($this->serverConfig("login_ban_system") == TRUE)
		{
			// If any row in the blacklist exists
			$check_logins = $this->query("SELECT bad_logins, last_try FROM (".$this->table_blocked_ip.") WHERE ip = ('".$userIp."')");
			if(mysqli_num_rows($check_logins) >= 1)
			{
				while($check_logins_data = mysqli_fetch_object($check_logins))
				{
					$badLogins	= $check_logins_data->bad_logins;
					$lastTry 	= $check_logins_data->last_try;
				}
				
				$loginData = array('badLogins' => $badLogins, 'lastTry' => $lastTry, 'max_attempts_allowed' => $this->maxLoginAttempts);
				
				/* Return:
				** The number of bad logins
				** Time of the last try
				** The allowed maximum of login attempts within the given time
				*/
				return $loginData;
			}
			else
			{
				$this->query('INSERT INTO '.$this->table_blocked_ip.' (ip) VALUES ("'.$userIp.'")');
				
				return TRUE;
			}
		} else
				return TRUE;
	}
	
	// Simply increase the bad logins for the current IP by 1, if the ban is NOT over.
	public function increaseBadLogins($userIp)
	{		
		if($this->checkBadLoginExpiration($userIp) == FALSE)
		{
			$increaseLogins = $this->query("UPDATE (".$this->table_blocked_ip.") SET bad_logins = bad_logins+1 WHERE ip = ('".$userIp."')");
			
			return TRUE;
		}
		else
			return FALSE;
	}
    
	/* Check if the current existing entry in blacklist already expired.
	** 
	** Returns FALSE if the ban time is NOT over.
	** ELSE
	** Returns TRUE
	*/
	public function checkBadLoginExpiration($userIp)
	{
		if($this->serverConfig("login_ban_system") == TRUE)
		{
			$countLogins = $this->checkBadLogins($userIp);
			
			if(isset($countLogins['lastTry']))
			{
				$countLogins['lastTry'] = strtotime($countLogins['lastTry']);
				$this->bad_login_time = strtotime($this->bad_login_time);
				
				// If ban time is over
				if ($countLogins['lastTry'] <= $this->bad_login_time)
				{
					$this->query("UPDATE (".$this->table_blocked_ip.") SET bad_logins = 0 WHERE ip = ('".$userIp."')");
					return TRUE;
				}
				// If ban time is not over
				else
					return FALSE;
			}
			else
				// If a new blacklist entry was created
				return TRUE;
		} else
			return TRUE;
	}
	
	public function hasTooManyLoginAttempts($userIp)
	{
		$this->checkBadLoginExpiration($userIp);
		
		$data = $this->checkBadLogins($userIp);
		
		if($data['badLogins'] >= $this->maxLoginAttempts)
			return TRUE;
		else
			return FALSE;
	}
	
	// Returns ban data if user is banned. Else False.
	public function isBanned($userId)
	{
		$query = $this->query('SELECT ban_duration, banned_at FROM '.$this->table_ban.' WHERE id = ' . $userId);
		if($data = mysqli_fetch_object($query))
		{
			$banData = ARRAY(
				'id'			=>	$userId,
				'banDuration'	=>	$data->ban_duration,
				'bannedAt'		=>	$data->banned_at
			);
			
			return $banData;
		}
		else
			return FALSE;
	}
	
	public function checkImage($path)
	{
			if(!file_exists($this->getRoot().$path)) return false;
			else return true;
	}
	
	public function getDefaultAvatar()
	{
			// Read server config for paths
			$this->defaultPath   = $this->serverConfig("default_avatar_path");
			$this->defaultAvatar = $this->serverConfig("default_avatar");
			
			return $this->defaultPath.$this->defaultAvatar;
	}
	
	public function checkUserAvatar($avatar_path)
	{
			if(!$this->checkImage($avatar_path))
			{
				if($this->checkImage($this->serverConfig("default_avatar_path").$avatar_path))
					return $this->serverConfig("default_avatar_path").$avatar_path;
				else
				
				return $this->getDefaultAvatar();
			}
			else
				return $avatar_path;
	}
	
	public function getURI()
	{
			return $_SERVER['REQUEST_URI'];
	}
	
	public function html2text($html, $restoreLinebreak = false)
	{
			$html = htmlentities($html);
			
			if(isset($restoreLinebreak) && $restoreLinebreak == true)
				$html = $this->restoreLinebreak($html);
			
			return $html;
	}
	
	public function delayedTableUpdate($query, $sessionvar_name, $sessionvar_key, $time)
	{
			$_SESSION[$sessionvar] = time();
			
			if(!isset($_SESSION[$sessionvar_name][$sessionvar_key]) || (isset($_SESSION[$sessionvar_name][$sessionvar_key]) && (time() - $_SESSION[$sessionvar_name][$sessionvar_key][0] >= $time)))
			{
			  $this->query($query);
			  $_SESSION[$sessionvar_name][$sessionvar_key][0] = time();
			}
	}
	
	
	public function highlightkeyword($str, $search)
	{
			$highlightcolor = "#daa732";
			$occurrences = substr_count(strtolower($str), strtolower($search));
			$newstring = $str;
			$match = array();
		 
			for ($i=0;$i<$occurrences;$i++) {
				$match[$i] = stripos($str, $search, $i);
				$match[$i] = substr($str, $match[$i], strlen($search));
				$newstring = str_replace($match[$i], '[#]'.$match[$i].'[@]', strip_tags($newstring));
			}
		 
			$newstring = str_replace('[#]', '<span style="color: '.$highlightcolor.';">', $newstring);
			$newstring = str_replace('[@]', '</span>', $newstring);
			
			return $newstring;
 
	}
	
	/*****
         * Check access of current session
         *
         * @ PARAM:
		 * 1.: Type of acces (user, mod, admin)
    ******/
	public function checkSessionAccess($access)
	{
		if(isset($_SESSION[$access.'ACCESS']) && $_SESSION[$access.'ACCESS'] == true)
			return true;
		else
			return false;
	}
	
	public function getAccountSecurity($accountID)
	{
		$query = $this->query('SELECT account_level FROM '.$this->table_accounts.' WHERE id='.$accountID);
		$result = mysqli_fetch_object($query);
		
		return $security = $result->account_level;
	}
	
	
	/*****
         * Check if current user is allowed in Board X
         *
         * @ PARAM:
		 * 1.: Id of the thread
		 * 2.: Return Type. 0 = Error; 1 = false
    ******/
	public function checkBoardPermission($boardID, $returnType, $errorText = '', $linkExt = NULL)
	{
		if($this->boardConfig($boardID, 'member_exclusive') == 1)
		{
			if(isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == TRUE)
			{
				// ON IS EXCLUSIVE AND LOGGED IN
				return true;
			}
			else
			{
				// ON IS EXCLUSIVE AND NOT LOGGED IN
				if($returnType == 0)
				{
					$this->throwError($errorText, $linkExt);
					
					return false;
				}
				else if($returnType == 1)
					return false;
			}
		}
		else
			return true;
	}
	
	// Update the current account settings - based on the session ID
	public function updateAccount($type, $value)
	{
		$type = mysqli_real_escape_string($this->_link, $type);
		$value = mysqli_real_escape_string($this->_link, $value);
		
		$result = $this->query("UPDATE ". $this->table_accdata . " SET $type = '" . $value . "' WHERE account_id=(SELECT id FROM (".$this->table_accounts.") WHERE sid='" . $_SESSION['ID'] . "')");
		
		return $result;
	}
	
	/*****
         * Calculate User rank, based on number of posts and account Level
         *
         * @ PARAM:
		 * [(1.: Number of posts)]
		 * [(2.: Account Level)]
		 * [(3.: User ID to look for)]
    ******/
	public function calculateRank($postNum = 0, $accLevel = 0, $accountID = 0)
	{
		
		if($accountID != 0)
		{
			$userData = $this->getUserdata($accountID);
			
			$postNum = $userData['posts'];
		}
		
		$prevKey = 0;
		$rank = ARRAY();
		
		foreach($this->userRanks as $key => $value)
		{
			// If substraction is smaller than 0, we reached the next coming rank.
			if($postNum - $key < 0)
			{
				// So select our previous rank
				$rank[0] = $this->userRanks[$prevKey];
				
				// And exit the loop
				break;
			}
			
			// On each successful iteration, set the previous key
			$prevKey = $key;
		}
		
		$iterations = $this->userRanksDisplay[$rank[0]];
		$rank[1] = '';
		
		if($this->getAccountSecurity($accountID) == 1 || $this->getAccountSecurity($accountID) == 2)
		{
			switch($this->getAccountSecurity($accountID))
			{
				case 1: 
					$prefix = 'user';
					$rank[2] = 'Nutzer';
				break;
				case 2: 
					$prefix = 'mod';
					$rank[2] = 'Moderator';
				break;
			}
			
			for($a = 1; $a <= $iterations[0]; $a++)
			{
				$rank[1] .= '<img src="./images/icons/ranks/'.$prefix.$this->userRanksDisplay[$rank[0]][1].'.png">';
			}
		}
		else if ($this->getAccountSecurity($accountID) == 3)
		{
			$prefix = 'admin';
			$rank[2] = 'Administrator';
			
			for($a = 1; $a <= 5; $a++)
			{
				$rank[1] .= '<img src="./images/icons/ranks/adminRank.png">';
			}
		}
		

		// Return the whole dataset
		return $rank;
	}
	
	// The target here is to check the "accepted" status of an account and return wheter true or false
	public function CheckUserVerification($id)
	{
		$query = $this->query("SELECT accepted FROM ".$this->table_accounts." WHERE id=".$id);
		
		if($result = mysqli_fetch_object($query))
		{
			if($result->accepted == 1)
				return true;
			else
				return false;
		}
		else
			return false;
	}
	
	
	public function getUserIp()
	{
		if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['REMOTE_ADDR'];
		} //!isset( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] )
		else {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
	}
	
	
	protected function getClassProperty($property)
	{
		return $this->{$property};
	}
	
	
	public function buildOnlineStatus($status, $username)
	{
		if($status == 1)
		{
			return '<div class="green_circle_small" title="'.$username.' ist grade online."></div>';
		}
		else
		{
			return '<div class="red_circle_small" title="'.$username.' ist grade offline."></div>';
		}
	}
	
	
	public function buildArmoryLink($character_realm, $character_name)
	{
		if(!empty($character_name) &&!empty($character_realm))
			return '<a href="http://eu.battle.net/wow/de/character/'.$character_realm.'/'.$character_name.'/advanced" target="_blank"><div class="icons_wide" id="armory"></div></a>';
		else
			return '';
	}
	
	
	public function addAccountLog($message, $showMessage = FALSE, $accountId = NULL, $clientIp = NULL)
	{
		if($showMessage == TRUE)
			echo $message;
		
		if($this->query('INSERT INTO '.$this->table_accountlogs.' (message, user_ip, account_id) VALUES ("' . $message . '", "' . $clientIp . '", "' . $accountId . '")'))
			return TRUE;
		else
			return FALSE;
		
	}
	
	
	/*****
         * Custom Error function with dynamic referer
         *
         * @ PARAM:
		 * 1.: Text to display
		 * 2.: URI the button should refer to
    ******/
	public function throwError($ErrorPgMsg, $linkExt = NULL)
	{
		if($linkExt != NULL)
			$linkExt = $linkExt;
		else
			$linkExt = $_SERVER['HTTP_REFERER'];
		

		$errorString = '
		<div class="errorMain">
			<center>
				<div class="icons_big" id="warning"></div>
			</center>
			<div class="innerWarning">
				<p>
					'.$ErrorPgMsg.'
				</p>
				<p>
					<a href="'.$linkExt.'" class="ErrorLink">
						Zurück zur vorherigen Seite
					</a>
				</p>
			</div>
		</div>';

		echo $errorString;
	}

}

?>