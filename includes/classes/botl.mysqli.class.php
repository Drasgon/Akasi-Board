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

class Database

  {

  // DB CONFIG

  private $db_user 		= 'root';
  private $db_pwd  		= '';
  private $db_host 		= '127.0.0.1';
  private $db_port 		= '3306';
  private $db_name 		= 'botl';
  private $db_charset 	= 'utf8';
  private $use_port		= false;

  // CLASS CONFIG

  public $appname = 'Bane of the Legion';
  public $appname_short = 'BotL';
  
  public $queryExecutionTime = 0;
  public $queriesFired = 0;
  private $queryErrorMessage = 'Something went wrong and the system couldn\'t access the required table.';
  private $adminMail = 'admin@baneofthelegion.de';

  private $showErrors = 1;
  private $devMode = TRUE;
  
  // Error Numbers
  
  public $mysql_errors = array
  (
	"host"		=> 20,
	"login"		=> 45,
	"query" 	=> 121,
	"charset" 	=> 134,
	"table" 	=> 259,
	"database" 	=> 641,
	"link" 		=> 650,
	"option"	=> 801
  );

  // DEFINE TABLES

  public $table_armory_data 		= 'armory_data';
  public $table_armory_time   		= 'armory_time';
  public $table_guild_members   	= 'guild_characters';
  public $table_guild_news   		= 'guild_news';
  public $table_guild_achievements  = 'guild_achievements';


  
  // Build mysqli connection

  public function mysqli_db_connect()
    {
		global $globalLink;
		
		$start = microtime(TRUE);
		
		$globalLink instanceof MySQLi;
		
		if(get_class($globalLink) == 'Database')
		{
			$port = ($this->use_port == true) ? ':' .$this->db_port : '';
			$globalLink = new mysqli($this->db_host . $port, $this->db_user, $this->db_pwd, $this->db_name);
			if (!$globalLink) $this->mysqli_db_error('Database connect failed', 'One of the given values is incorrect', $this->mysql_errors["login"]);
			else
			{
				$this->link = $globalLink;
				$this->selectDB = mysqli_select_db($this->link, $this->db_name);
				if (!$this->selectDB) $this->mysqli_db_error('Database select failed', 'One of the given values is incorrect', $this->mysql_errors["database"]." or ".$this->mysql_errors["login"]);
				if ($this->link) $this->set_db_charset($this->db_charset, $this->link);
				
				//echo round((microtime(TRUE) - $start) * 1000, 3).'<br>';
				
				
				return $globalLink;
			}
		}
		else
		{
			return $this->link = $globalLink;
		}
    }

  // Set Database charset

  public function set_db_charset()
    {
		if ($this->db_charset != "") $this->setCharset = mysqli_set_charset($this->link, $this->db_charset);
		if (!$this->setCharset) $this->mysqli_db_error('Failed to set charset to - ' . $this->db_charset . ' -', 'Illegal charset');
    }
	
  // Execute Queries (For better control and logging)
	
  public function query($queryString)
	{
		global $totalQueries, $totalQueryTime;
		$start = microtime(true);
		
		$queryExecution = mysqli_query($this->link, $queryString) or die($this->mysqli_db_error($this->queryErrorMessage, "An error occured while executing the database query.", $this->mysql_errors["query"], $queryString));

		$end = microtime(true);
		$totalQueryTime = $totalQueryTime + ($end - $start);
		$totalQueries++;
		
		return $queryExecution;
	}
	
  // Determine the number of results in a mysqli result or simply pass a query string as parameter.
  // The query will be executed automatically then.
	
  public function get_max_possible_results($data, $is_mysql_result = FALSE)
    {
		if($is_mysql_result == FALSE)
				$data = $this->query($data);
		
		return mysqli_num_rows($data);
	}

  // Error handler
	
  public function mysqli_db_error($errormsg, $error_reason = "", $error_number = "", $query_string = "")
    {
		$this->errdesc = mysqli_error($this->link);
		if(empty($error_number))
		{
			$this->errno = mysqli_errno($this->link);
			$this->err_string = 'Mysql';
		}
		else
		{
			$this->errno = $error_number;
			$this->err_string = $this->appname_short;
		}
		$trace = debug_backtrace();
		$caller = array_shift($trace);
		
		if($error_number != 121)
		{
			$scriptName = basename($caller['file']);
			$lineNumber = $caller['line'];
		}
		else
		{
			$scriptName = basename($trace[1]['file']);
			$lineNumber = $trace[1]['line'];
		}
		
		if(!isset($this->link))
			$error_reason = 'Es konnte/wurde keine Datenbank Verbindung aufgebaut (werden).';
		$errormsg = "<b>Fatal error</b>: $errormsg\n<br />";
		if (empty($error_reason)) $errormsg.= "<b>Reason:</b> $this->errdesc\n<br />";
		  else
			$errormsg.= "<b>Reason:</b> $error_reason\n<br />";
		$errormsg.= "<b>$this->err_string error number:</b> $this->errno\n<br />";
		$errormsg.= "<b>Time:</b> " . date("d.m.Y - H:i") . "\n<br />";
			if(isset($query_string) && !empty($query_string) && $this->devMode == TRUE)
				$errormsg.= "<b>Query string:</b> " . $query_string . "\n<br />";
		$errormsg.= "<b>Script:</b> " . $scriptName . "\n<br />";
		$errormsg.= "<b>On line:</b> " . $lineNumber . "\n<br />";
		$mailSubject = $this->appname." error '".$error_number."' occured";
		$mailBody = $errormsg;
		$notAllowed = ARRAY("<br>", "<br />");
		$mailBody = str_replace($notAllowed, "%0D%0A", $mailBody);
		$notAllowed = ARRAY("<b>", "</b>", "\n");
		$mailBody = str_replace($notAllowed, "", $mailBody);
		$errormsg.= "<br><span class=\"information\"><a href=\"mailto:".$this->adminMail."?subject=".$mailSubject."&body=".$mailBody."\">Please inform the administration about this problem.<a></span>\n<br>";
		if ($this->showErrors)
			$errormsg = "$errormsg";
		  else
			$errormsg = "\n<!-- $errormsg -->\n";
		die("<link rel=stylesheet href=\"./css/akb-error.css\" media=\"screen\"></table><font face=\"Verdana\" class=\"databaseError\" size=2><b>DATABASE ERROR</b><br /><br />" . $errormsg . "</font></table>");
    }
  }
?>