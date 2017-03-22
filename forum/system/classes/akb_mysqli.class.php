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

class Database

  {

  // DB CONFIG

  private $db_user = 'ni204675_1sql3';
  private $db_pwd  = 'asas78astzda80sud9asdjalsdhaklsdh';
  private $db_host = 'vweb17.nitrado.net';
  private $db_port = '3306';
  private $db_name = 'ni204675_1sql3';
  private $db_charset = 'utf8';
  private $db_use_port = FALSE;

  // CLASS CONFIG

  public $appname = 'Akasi Board';
  public $appname_short = 'Akasi';
  
  public $queryExecutionTime = 0;
  public $queriesFired = 0;
  private $queryErrorMessage = 'Something went wrong and the system couldn\'t access the required table.';

  private $showErrors = 1;
  private $devMode = true;
  
  public $link = NULL;
  
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

  public $table_accounts 		= 'akb_account';
  public $table_accdata   		= 'akb_account_data';
  public $table_account_token	= 'akb_account_token';
  public $table_sessions 		= 'akb_user_sessions';
  public $table_boards   		= 'akb_forum_main';
  public $table_thread  		= 'akb_forum_thread';
  public $table_thread_posts 	= 'akb_forum_thread_posts';
  public $table_thread_saves	= 'akb_forum_thread_create_save';
  public $table_post_saves		= 'akb_forum_post_create_save';
  public $table_chat_public  	= 'akb_chat_public';
  public $table_chat_private  	= 'akb_chat_private';
  public $table_chat_rooms		= 'akb_chat_rooms';
  public $table_accountlogs  	= 'akb_account_logs';
  public $table_accountchanges  = 'akb_account_changelogs';
  public $table_forum_accdata  	= 'akb_forum_accountdata';
  public $table_forum_read  	= 'akb_forum_read_data';
  public $table_hiddenboards  	= 'akb_user_hidden_boards';
  public $table_categories  	= 'akb_board_categories';
  public $table_notes			= 'akb_notes';
  public $table_msg_request  	= 'akb_message_request_data';
  public $table_subdata  		= 'akb_forum_subdata';
  public $table_configs  		= 'akb_server_configs';
  public $table_ranks  			= 'akb_ranks';
  public $table_user_rank		= 'akb_user_rank';
  public $table_user_rank_status= 'akb_user_rank_data';
  public $table_portal_news 	= 'akb_portal_data';
  public $table_protection_logs	= 'akb_protection_system_logs';
  public $table_profile 		= 'akb_account_data_profile';
  public $table_ban 			= 'akb_account_ban';
  public $table_blocked_ip		= 'akb_blocked_ip';
  public $table_gallery_data	= 'akb_gallery_data';
  public $table_gallery_thumb	= 'akb_gallery_data_thumb';
  public $table_gallery_comments= 'akb_gallery_comments';
  public $table_gallery_directory= 'akb_gallery_temp';
  public $table_survey			= 'akb_survey';
  public $table_survey_data		= 'akb_survey_data';
  public $table_security_data	= 'akb_securitydata';
  public $table_securityquestions= 'akb_securityquestions';

  
  public function __construct()
	{
		if (strpos($_SERVER['DOCUMENT_ROOT'],'/dignum-aliorum') != TRUE) {
			$_SERVER['DOCUMENT_ROOT'] = '/dignum-aliorum';
		}
	}
  
  // Build mysqli connection

  public function mysqli_db_connect()
    {
		global $globalLink;
		
		$start = microtime(TRUE);
		
		$globalLink instanceof MySQLi;
		
		if(get_class($globalLink) == 'Database')
		{
			$globalLink = new mysqli($this->db_host, $this->db_user, $this->db_pwd, $this->db_name, $this->db_port);
			if (!$globalLink) $this->mysqli_db_error('Database connect failed', 'One of the given values is incorrect', $this->mysql_errors["login"]);
			else
			{
				$this->link = $globalLink;
				$this->selectDB = mysqli_select_db($this->link, $this->db_name);
				if (!$this->selectDB) $this->mysqli_db_error('Database select failed', 'One of the given values is incorrect', $this->mysql_errors["database"]." or ".$this->mysql_errors["login"]);
				if ($this->link) $this->set_db_charset($this->db_charset, $this->link);
				
				//echo round((microtime(TRUE) - $start) * 1000, 3).'<br>';
				
				// var_dump(get_object_vars($globalLink));
				
				return $globalLink;
			}
		}
		else
		{
			return $this->link = $globalLink;
		}
    }
		
		
  // Get affected Rows in last query
  
  public function get_affected_rows()
  {
		return mysqli_affected_rows($this->link);
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
		
		if(!$queryExecution = mysqli_query($this->link, $queryString))
			$this->mysqli_db_error($this->queryErrorMessage, "An error occured while executing the database query.", $this->mysql_errors["query"], $queryString);

		$end = microtime(true);
		$totalQueryTime = $totalQueryTime + ($end - $start);
		$totalQueries++;
		
		return $queryExecution;
	}

  // Error handler
	
  public function mysqli_db_error($errormsg, $error_reason = "", $error_number = "", $query_string = "")
    {
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			echo '<span class="error">Es ist ein schwerwiegender Fehler aufgetreten!</span>';
			
			exit();
		}
	
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
		
			$scriptName = basename($caller['file']);
			$lineNumber = $caller['line'];
			
			$scriptName .= ', ' . basename($trace[1]['file']);
			$lineNumber .= ', ' . $trace[1]['line'];

		
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
		$mailSubject = "Akasi Board error '".$error_number."' occured";
		$mailBody = $errormsg;
		$notAllowed = ARRAY("<br>", "<br />");
		$mailBody = str_replace($notAllowed, "%0D%0A", $mailBody);
		$notAllowed = ARRAY("<b>", "</b>", "\n");
		$mailBody = str_replace($notAllowed, "", $mailBody);
		$errormsg.= "<br><span class=\"information\"><a href=\"mailto:Ian.Tanthal@gmx.de?subject=".$mailSubject."&body=".$mailBody."\">Please inform the administration about this problem.<a></span>\n<br>";
		if ($this->showErrors)
			$errormsg = "$errormsg";
		  else
			$errormsg = "\n<!-- $errormsg -->\n";
		die("<link rel=stylesheet href=\"./css/akb-error.css\" media=\"screen\"></table><font face=\"Verdana\" class=\"databaseError\" size=2><b>DATABASE ERROR</b><br /><br />" . $errormsg . "</font></table>");
    }
  }
?>