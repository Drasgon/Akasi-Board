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

class Admin
{
	// Set default values
    private $_database;
    private $_link;
    
    public function __construct($database, $link)
    {
        $this->_database = $database;
        $this->_link     = $link;
        
		// @TODO
		// Redefine of the table names, because otherwise they won't work with this class. 
        $this->table_accounts       = $this->_database->table_accounts;
        $this->table_accdata        = $this->_database->table_accdata;
        $this->table_sessions       = $this->_database->table_sessions;
        $this->table_boards         = $this->_database->table_boards;
        $this->table_thread         = $this->_database->table_thread;
        $this->table_thread_posts   = $this->_database->table_thread_posts;
        $this->table_thread_saves   = $this->_database->table_thread_saves;
        $this->table_post_saves     = $this->_database->table_post_saves;
        $this->table_chat_public    = $this->_database->table_chat_public;
        $this->table_accountlogs    = $this->_database->table_accountlogs;
        $this->table_accountchanges = $this->_database->table_accountchanges;
        $this->table_forum_accdata  = $this->_database->table_forum_accdata;
        $this->table_forum_read     = $this->_database->table_forum_read;
        $this->table_hiddenboards   = $this->_database->table_hiddenboards;
        $this->table_categories     = $this->_database->table_categories;
        $this->table_msg_request    = $this->_database->table_msg_request;
        $this->table_subdata        = $this->_database->table_subdata;
        $this->table_configs        = $this->_database->table_configs;
        $this->table_ranks          = $this->_database->table_ranks;
        $this->table_portal_news    = $this->_database->table_portal_news;
        $this->table_profile        = $this->_database->table_profile;
        $this->table_ban            = $this->_database->table_ban;
    }
    
	// Function to accept new registered users
    public function AcceptUser($userID)
    {
        $this->userID = mysqli_real_escape_string($this->_link, $userID);
		
        $db->query($this->_link, "UPDATE $this->table_accounts SET accepted=1 WHERE id=('" . $this->userID . "')");
    }
    
    
	// Function to ban a user and kick him, if a session exists
    public function BanUser($userID, $banType, $banDuration)
    {
		// Set class values by function call
        $this->userID      = mysqli_real_escape_string($this->_link, $userID);
        $this->banType     = mysqli_real_escape_string($this->_link, $banType);
        $this->banDuration = mysqli_real_escape_string($this->_link, $banDuration);
        $this->bannedAt    = mysqli_real_escape_string($this->_link, time());
		
		// Get SID of executing user/admin
        $this->userSid = $_SESSION['ID'];
        
		// Use SID to get the real user id
        $this->getSid = $db->query($this->_link, "SELECT id FROM $this->table_sessions WHERE sid=('" . $this->userSid . "')");
        
        while ($this->fetchSid = mysqli_fetch_object($this->getSid)) {
            $this->userID_bannedBy = mysqli_real_escape_string($this->_link, $this->fetchSid->id);
        }
        
		// Write a log-row
        $this->processBan = $db->query($this->_link, "INSERT INTO $this->table_ban (id, ban_type, ban_duration, banned_at, banned_by) VALUES (('" . $this->userID . "'), ('" . $this->banType . "'), ('" . $this->banDuration . "'), ('" . $this->bannedAt . "'), ('" . $this->userID_bannedBy . "'))");
		
		// Perform a function call, to kick the user
        $this->processKick = $this->KickUser($this->userID);
		
		// Return true if everything went correctly | Else return false
        if ($this->processBan && $this->processKick)
            return true;
        else
            return false;
    }
    
    
	// Kick a user by destroy the stored session id		<|| Maybe have to work out that one.
    public function KickUser($userID)
    {
		// Set class values by function call
        $this->userID = mysqli_real_escape_string($this->_link, $userID);
		
		// Query for getting the user id
        $this->getSid = $db->query($this->_link, "SELECT sid FROM $this->table_sessions WHERE id=('" . $this->userID . "')");
        
        while ($this->fetchSid = mysqli_fetch_object($this->getSid)) {
            $this->UserSid = mysqli_real_escape_string($this->_link, $this->fetchSid->sid);
        }
        
		// Query for destroying the stored session
        $this->destroySession = $db->query($this->_link, "UPDATE $this->table_sessions SET sid='0', current_user_ip='0', online='0' WHERE id=('" . $this->userID . "')");
		
		$counter_1 = mysqli_affected_rows($this->_link);
		
		$this->destroySession_second = $db->query($this->_link, "UPDATE $this->table_accounts SET sid='0' WHERE id=('" . $this->userID . "')");
		
		$counter_2 = mysqli_affected_rows($this->_link);
        
		// If the session destroy was successful
        if ($this->destroySession && ($counter_1 + $counter_2) == 2) {
		
			// Define the log message
            $this->kickMessage = "Nutzer wurde durch die Administration ausgeloggt.";
			// Write a new row to the logs
            $this->logKick = $db->query($this->_link, "INSERT INTO $this->table_accountlogs (account_id, message, sid) VALUES (('" . $this->userID . "'), ('" . $this->kickMessage . "'), ('" . $this->userSid . "'))");
        }
    }
	
	public function RemoveUser($userID)
	{
		// Set class values by function call
		$this->userID = mysqli_real_escape_string($this->_link, $userID);
		
		$this->removeUseracc = $db->query($this->_link, "DELETE FROM $this->table_accounts WHERE id = ('".$this->userID."')") or die(mysqli_error($this->_link));
	}
    
}
?>