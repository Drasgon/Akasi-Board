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
*/

	ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(-1);

class Runtime

  {
	// Declare class vars
    public $_database;
    public $_link;
	private $_classNames = ARRAY(
		/*
			ARRAY("auth", "fb.auth.class", "Auth"),
			ARRAY("session", "fb.session.class", "Session"),
			ARRAY("user", "fb.account.class", "User"),
			ARRAY("posts", "fb.posts.class", "Post"),
			ARRAY("hash", "fb.hash.class", "Hash")
		*/
	);
	

	// Path to main folder - Relative to/from web root
	// USAGE: MUST contain backslashes instead of normal ones.
	private $cwdir	=	'www';
	
	// Path to web root - Relative to apache root
	// USAGE: Can contain normal slashes.
	private $workDir =	'';
	
	// Some Runtime Variables
	private $errorHandler = TRUE;
	private $modules = '/includes/modules/';
	private $classes = '/includes/classes/';
	private $phpExt = ".php";
	private $sbp = 'sbp';
	private $user = 'user';
	private $visitor = 'public';

	

	
	public function __construct($additionalClasses = ARRAY())
    {
		global $_classes;
			
			if(!isset($this->_database))
			{
				require_once('mysqli.class.php');
				
				$_classes['database'] = new Database();
				$_classes['link'] = $_classes['database']->mysqli_db_connect();
			}
		
			if(!empty($this->workDir))
				chdir($this->workDir);
			$this->cwd		 = getcwd();
			$this->cwd		 = explode($this->cwdir, $this->cwd);
			$this->cwd		 = $this->cwd[0].'\\'.$this->cwdir;
			$this->cwd		 = str_replace('\\', '/', $this->cwd);
			
			$iterations = 0;
			foreach ($additionalClasses as $key => $value)
			{
			
				if(is_int($key) && $value === TRUE)
				{
					$fileName = $this->_classNames[$key][1];
					$this->useFile($this->classes.$fileName.$this->phpExt, 1);
					$_classes[$this->_classNames[$key][0]] = NEW $this->_classNames[$key][2]($_classes['database'], $_classes['link']);
				}
				$iterations++;
			}
    }
	
	public function __destruct()
	{
			
	}
	
	
	/*****
         * Custom include against include issues
         *
         * @ PARAM:
		 *
		 * 1.: Path of the file - Relative to the index file
		 * 2.: Mode. Leave blank for default include. 1 for once.
    ******/
	public function useFile($path, $include_once = NULL)
	{
		if($include_once == 1)
			return require_once($this->cwd.$path);
		else
			return require($this->cwd.$path);
	}
	
	
	/*****
         * Read the browsers URL
         *
         * @ PARAM:
		 *
		 * --- None ---
		 * 	   No Params required.
    ******/
	function readURL()
	{
			 $pageURL = 'http';
			 if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $pageURL .= "s";
			 $pageURL .= "://";
			 if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
			  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			 } else {
			  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			 }
			 
		return $pageURL;
	}
	
	
	/*****
         * Send a error Note
         *
         * @ PARAM:
		 *
		 * 1.: Note text
    ******/
	public function throwNote($msg)
	{
			if($this->errorHandler)
			{
				$this->errorHandler = $this->useFile("./includes/modules/controller/processors/errorhandler.php");
				
				
				writeNote($msg);
			}
			else
				return;
	}
	
	
	/*****
         * Convert an unix timestamp to a readable format
         *
         * @ PARAM:
		 *
		 * 1.: Unix timestamp
    ******/
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
	
	
	/*****
         * Loader for the "Session-Based-Page" system
         *
         * @ PARAM:
		 *
		 * 1.: Filename
		 * 2.: Type - Either "user" or "public". Or leave it blank.
		 * --------------
		 * @ USAGE:
		 *
		 * One public and one "users only" file is needed.
		 * This system allows to easily create and automatically switch between
		 * 2 PHP Files.
		 * The usage depends on the session status.
		 * If "type" is left empty, the system will decide by itself, which module has to be loaded.
		 * SBP directory is: "./includes/modules/sbp"
    ******/
	public function loadSbpModule($moduleName, $type = NULL)
	{
			if($type == NULL)
			{
				if(isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true)
					$this->sbpModule = $this->modules.$this->sbp.'/'.$this->user.'/'.$moduleName;
				else
					$this->sbpModule = $this->modules.$this->sbp.'/'.$this->visitor.'/'.$moduleName;
			} elseif($type == 'user')
			{
				$this->sbpModule = $this->modules.$this->sbp.'/'.$this->user.'/'.$moduleName;
			} elseif($type == 'public')
			{
				$this->sbpModule = $this->modules.$this->sbp.'/'.$this->visitor.'/'.$moduleName;
			}
			
			$this->useFile($this->sbpModule.'.php');
	}
	
	
	/*****
         * Parser for links
         *
         * @ PARAM:
		 *
		 * 1.: String to parse
    ******/
	public function parse_links($str)
	{
			$str = str_replace('www.', 'http://www.', $str);
			$str = preg_replace('|http://([a-zA-Z0-9-./]+)|', '<a href="http://$1">$1</a>', $str);
			$str = preg_replace('/(([a-z0-9+_-]+)(.[a-z0-9+_-]+)*@([a-z0-9-]+.)+[a-z]{2,6})/', '<a href="mailto:$1">$1</a>', $str);
		return $str;
	}
	
	
	public function html2text($html, $restoreLinebreak = false)
	{
			$html = htmlentities($html);
			
			if(isset($restoreLinebreak) && $restoreLinebreak == true)
				$html = $this->restoreLinebreak($html);
			
		return $html;
	}
	
	
	public function restoreLinebreak($html)
	{
			$html = str_replace("&lt;br&gt;", "<br>", $html);
			$html = str_replace("&lt;br /&gt;", "<br />", $html);
			
		return $html;
	}
	
	public function getChildProperty($class, $property = '')
	{
			global $_classes;
			if(!empty($property))
				$property = '->'.$property;
			$string = strval('return $_classes["'.$class.'"]'.$property.';');
		return eval($string);
	}
  }
?>