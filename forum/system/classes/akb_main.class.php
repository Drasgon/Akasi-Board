<?php
/*
Copyright (C) 2015  Alexander Bretzke

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
Copyright 2014, Alexander Bretzke - All rights reserved
*/
class Board
{	
	// Declare MySql Variables
    private $_database;
    private $_link;
	
	protected $bad_login_time	=	"-30 minutes";
	protected $maxLoginAttempts	=	3;
	
	protected $userData = ARRAY();
	protected $currentUserData = ARRAY();
	
	private $max_update_diff = 600; // Max time between user status updates in seconds
	
	// Path to main folder - Relative to/from web root
	// USAGE: MUST contain backslashes instead of normal ones.
	// MORE USAGE: This functions as a delimiter for the working directory!
	// EVEN MORE USAGE!: The only thing needed here, is the name of the directory, that contains the index.php. Not more, not less.
	// Just the directory name. NOT A PATH!
	private $cwdir	=	'forum';
	
	// Path to web root - Relative to apache root
	// USAGE: Can contain normal slashes. - NOT NECCESSARY! It's recommended to leave this blank!
	private $workDir =	'';
	
	public $includeData = '';

	
	/*****
         * Build Variables for access to table names
         *
         * @ PARAM:
		 * 1.: Database class
		 * 2.: MySqli link
    ******/
    public function __construct($database, $link)
    {
        $this->_database = $database;
        $this->_link     = $link;
		if(!empty($this->workDir))
			chdir($this->workDir);
		$this->cwd		 = getcwd();
		$this->cwd		 = explode($this->cwdir, $this->cwd);
		$this->cwd		 = $this->cwd[0].$this->cwdir."/";
		$this->cwd		 = str_replace('\\', '/', $this->cwd);
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
			return require_once($this->cwd.$path);
		else
			return require($this->cwd.$path);
	}
	
	
	public function getRoot()
	{
		return $this->cwd;
	}
	

	public function url($file = '', $query = null) {
		$file=   ltrim($file, '/');
		$proto = isset($_SERVER['HTTPS']) ? 'https' : 'http';
		$url = $proto . ':/dignum-aliorum' . $_SERVER['HTTP_HOST'] . '/' . $file;
			  
		if(is_array($query)) {
			$url .= '?' . http_build_query($query);
		}
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
        $getName      = $this->_database->query("SELECT title FROM (".$this->_database->table_thread.") WHERE id=('" . $threadID . "')");
		
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
			$getID      = $this->_database->query("SELECT main_forum_id FROM (".$this->_database->table_thread.") WHERE id=('" . $threadID . "')");
			
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
			$getConfig      = $this->_database->query("SELECT $config FROM (".$this->_database->table_boards.") WHERE id=('" . $boardID . "')");
			
			// Fetch the query result and return it
			$config = mysqli_fetch_object($getConfig);
			$config = $config->guest_posts;
			
			return $config;

	}
    
    
	/*****
         * Collect all user data in an array and return it
         *
         * @ PARAM:
		 *
		 * 1.: Value of the field to search in.
		 *     E.g.: Session ID, Username, Posts . .  .
		 * 2.: Type of the search. It HAVE TO equal to the given Value(first parameter) AND the database field.
		 *     E.g.: Session ID = sid, Username = username, Posts = post_counter . .  .
    ******/
    public function getUserdata($searchFor, $type='account_id')
    {
		// Escape the "Input"
        $searchFor		= mysqli_real_escape_string($this->_link, $searchFor);
		$type			= mysqli_real_escape_string($this->_link, $type);
        
		if(!isset($this->userData[$type][$searchFor]))
		{
			if($searchFor != "0")
			{
			
				// What should be selected?
				$selectors = "account_id, username, gender, avatar, post_counter, email, user_title, messenger_skype, profile_views";
					
					// Build first part of the query
					$getData = "SELECT $selectors FROM (".$this->_database->table_accdata.") WHERE ";
						
						// Depending on the "type" parameter use another "WHERE" value
						switch($type)
						{
							// If the type is "sid"
							case 'sid':
									$getData .= "account_id=(SELECT id FROM (".$this->_database->table_accounts.") WHERE sid=('" . $searchFor . "'))";
								break;
								
							// If the type is "id"
							case 'id':
									$getData .= "account_id=('" . $searchFor . "')";
								break;
								
							// If the "type" parameter is empty or doesn't exists( unlikely, but just to be sure )
							default:
									$getData .= "$type=('" . $searchFor . "')";
								break;
						
						}
						
						// Execute the built query
						$getData = $this->_database->query($getData);
						
						// Fetch all query data and throw them straight into an array
						while ($resolveData = mysqli_fetch_object($getData)) {
						
							$this->currentUserData = array(
								
								'account_id' 	=> $resolveData->account_id,
								'name' 			=> $resolveData->username,
								'gender' 		=> $resolveData->gender,
								'avatar' 		=> $resolveData->avatar,
								'posts' 		=> $resolveData->post_counter,
								'email' 		=> $resolveData->email,
								'msngr_skype' 	=> $resolveData->messenger_skype,
								'profile_views' => $resolveData->profile_views
								
							);
						}
			}
			else
			{
				$this->currentUserData = array(
					
					'account_id' 	=> "0",
					'name' 			=> "Gast",
					'gender' 		=> "1",
					'avatar' 		=> $this->getDefaultAvatar(),
					'posts' 		=> "n.A",
					'email' 		=> "n.A",
					'msngr_skype' 	=> "",
					'profile_views' => "n.A"
								
				);
			}
			
			$this->userData[$type][$searchFor] = $this->currentUserData;
			
		}
		else
			$this->currentUserData = $this->userData[$type][$searchFor];
				
		// Give $this->userData a value, if the query fails or does not returns any data
		if (!isset($this->currentUserData) || empty($this->currentUserData)) { $this->currentUserData= 'No data returned.'; }
		
		
        
		// Return the entire thing
        return $this->userData[$type][$searchFor];
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
			
				return ($username = $this->userData['name']);
		}
		else
		{
			$data = $this->getUserdata($userid, "id");
				
				return ($username = $data['avatar']);
		}
	}
	
	
	public function getUserId()
	{
		$this->sid = $_SESSION['ID'];
		$data = $this->getUserdata($this->sid, "sid");
			
			return ($userId = $data['account_id']);
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
			$searchThread = $this->_database->query("SELECT account_id FROM (".$this->_database->table_forum_read.") WHERE account_id=(SELECT id FROM (".$this->_database->table_sessions.") WHERE sid = ('".$_SESSION['ID']."')) AND thread_id=('".$threadID."')");
			
			if(mysqli_num_rows($searchThread) >= 1)
			{
				return true;
			} else {
				return false;
			}
	}
	
	public function detectUnreadThreadsInBoard($boardID)
	{
			if(isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true)
			{
				$searchBoard = $this->_database->query("SELECT COUNT(*) AS count_threads FROM (".$this->_database->table_thread.") WHERE main_forum_id=('" . $boardID . "')");
					
				$resolveData = mysqli_fetch_object($searchBoard);
				$totalThreads = $resolveData->count_threads;
				
				$searchUnreads = $this->_database->query("SELECT COUNT(*) AS count_unreads FROM (".$this->_database->table_forum_read.")WHERE account_id=(SELECT id FROM (".$this->_database->table_accounts.") WHERE sid='" . $_SESSION['ID'] . "') AND board_id=('" . $boardID . "')");
					
				$unreadsResult = mysqli_fetch_object($searchUnreads);
				$totalUnreads = $unreadsResult->count_unreads;
				
				
				if ($totalThreads == $totalUnreads || (!isset($_SESSION['angemeldet']) || $_SESSION['angemeldet'] == false)) {
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
			
			$getValue = $this->_database->query("SELECT option_value FROM (".$this->_database->table_configs.") WHERE option_name = ('".$config_name."')");
			$resolveValue = mysqli_fetch_object($getValue);
			
			if(!$resolveValue)
				$this->_database->mysqli_db_error("Option '".$config_name."' not found.", "Option can't be found.", $this->_database->mysql_errors["option"]);
			else
				return $resolveValue->option_value;
	}
	
	public function serverConfigSet($config_name, $new_value)
	{
			$config_name = mysqli_real_escape_string($this->_link, $config_name);
			$new_value = mysqli_real_escape_string($this->_link, $new_value);
			
			$setValue = $this->_database->query("UPDATE (".$this->_database->table_configs.") SET option_value = ('".$new_value."') WHERE option_name = ('".$config_name."')");
	}
	
	// CHECK ONLINE STATUS FOR EVERY USER
	
	public function updateUserStatus()
	{
		$lastUpdate = $this->serverConfig("last_user_status_update");
		
		if(time() - $lastUpdate >= $this->max_update_diff)
		{
			$this->_database->query("UPDATE ".$this->_database->table_sessions." SET online=0 WHERE UNIX_TIMESTAMP()-last_activity >= 600");
			
			$this->serverConfigSet("last_user_status_update", time());
		}
	}
	
	public function checkBadLogins($user_ip)
	{
		if($this->serverConfig("login_ban_system") == TRUE)
		{
			$check_logins = $this->_database->query("SELECT bad_logins, last_try FROM (".$this->_database->table_blocked_ip.") WHERE ip = ('".$user_ip."')");
			if(mysqli_num_rows($check_logins) >= 1)
			{
				while($check_logins_data = mysqli_fetch_object($check_logins))
				{
					$bad_logins	= $check_logins_data->bad_logins;
					$last_try 	= $check_logins_data->last_try;
				}
				
				$login_data = array('bad_logins' => $bad_logins, 'last_try' => $last_try, 'max_attempts_allowed' => $this->maxLoginAttempts);
				
				return $login_data;
			}
			
			if(mysqli_num_rows($check_logins) < 1)
			{
				$this->_database->query('INSERT INTO '.$this->_database->table_blocked_ip.' (ip) VALUES ("'.$user_ip.'")');
				
				return true;
			}
		} else
				return true;
	}
	
	public function increaseBadLogins($user_ip)
	{
			$checkLogins = $this->checkBadLoginExpiration($user_ip);
			if($checkLogins == TRUE)
			{
			$increaseLogins = $this->_database->query("UPDATE (".$this->_database->table_blocked_ip.") SET bad_logins = bad_logins+1 WHERE ip = ('".$user_ip."')");
			
			if($increaseLogins)
				return true;
			else 
				return false;
			}
	}
    
	
	public function checkBadLoginExpiration($user_ip)
	{
			if($this->serverConfig("login_ban_system") == TRUE)
			{
				$countLogins = $this->checkBadLogins($user_ip);
				
				if(isset($countLogins['last_try']))
				{
					$countLogins['last_try'] = strtotime($countLogins['last_try']);
					$this->bad_login_time = strtotime($this->bad_login_time);

					// If ban time is over
					if ($countLogins['last_try'] <= $this->bad_login_time) {
						$this->_database->query("UPDATE (".$this->_database->table_blocked_ip.") SET bad_logins = 0 WHERE ip = ('".$user_ip."')");
						return $time_lasted = ($countLogins['last_try'] - (strtotime(date("Y-m-d H:i:sa"))));
					}
					// If ban time is not over
					else {
						return true;
					}
				}
				
				// If Array is NOT set, a new table row was created
				if(!isset($countLogins['last_try']))
				{
					return true;
				}
			} else
					return true;
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
			  $this->_database->query($query);
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

}

?>