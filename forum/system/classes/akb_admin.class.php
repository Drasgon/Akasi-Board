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
*/

class Admin
{
	// Set default values
    private $_database;
    private $_link;
	private $_main;
    
    public function __construct($database, $link, $main = '')
    {
        $this->_database = $database;
        $this->_link     = $link;
		$this->_main	 = $main;
    }
    
	// Accept new registered users
    public function AcceptUser($userID)
    {
        $this->userID = mysqli_real_escape_string($this->_link, $userID);
		
        $this->_database->query($this->_link, "UPDATE ".$this->_database->table_accounts." SET accepted=1 WHERE id=('" . $this->userID . "')");
    }
    
    
	// Ban a user and kick him, if a session exists
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
        $this->getSid = $this->_database->query($this->_link, "SELECT id FROM ".$this->_database->table_sessions." WHERE sid=('" . $this->userSid . "')");
        
        while ($this->fetchSid = mysqli_fetch_object($this->getSid)) {
            $this->userID_bannedBy = mysqli_real_escape_string($this->_link, $this->fetchSid->id);
        }
        
		// Write a log-row
        $this->processBan = $this->_database->query($this->_link, "INSERT INTO ".$this->_database->table_ban." (id, ban_type, ban_duration, banned_at, banned_by) VALUES (('" . $this->userID . "'), ('" . $this->banType . "'), ('" . $this->banDuration . "'), ('" . $this->bannedAt . "'), ('" . $this->userID_bannedBy . "'))");
		
		// Perform a function call, to kick the user.
		// Return true if everything went correctly | Else return false
        if ($this->processBan && $this->KickUser($this->userID))
            return true;
        else
            return false;
    }
    
    
	// "Kick" a user by destroying the stored session id		<|| Maybe have to work out that one.
    public function KickUser($userID)
    {
		// Set class values by function call
        $this->userID = mysqli_real_escape_string($this->_link, $userID);
		
		// Query for getting the user id
        $this->getSid = $this->_database->query($this->_link, "SELECT sid FROM ".$this->_database->table_sessions." WHERE id=('" . $this->userID . "')");
        
        $this->fetchSid = mysqli_fetch_object($this->getSid);
            $this->UserSid = mysqli_real_escape_string($this->_link, $this->fetchSid->sid);
        
		// Query for destroying the stored session
        $this->destroySession = $this->_database->query($this->_link, "UPDATE ".$this->_database->table_sessions." SET sid='0', current_user_ip='0', online='0' WHERE id=('" . $this->userID . "')");
		
		$destroyed_rows_first = $this->_database->get_affected_rows();
		
		$this->destroySession_second = $this->_database->query($this->_link, "UPDATE ".$this->_database->table_accounts." SET sid='0' WHERE id=('" . $this->userID . "')");
        
		// If the session destruction was successful and both last queries did their job
        if ($this->destroySession && ($destroyed_rows_first + $this->_database->get_affected_rows()) == 2) {
		
			// Define the log message
            $this->kickMessage = "Nutzer wurde durch die Administration ausgeloggt.";
			// Write a new row to the logs
            $this->logKick = $this->_database->query($this->_link, "INSERT INTO ".$this->_database->table_accountlogs." (account_id, message, sid) VALUES (('" . $this->userID . "'), ('" . $this->kickMessage . "'), ('" . $this->userSid . "'))");
        }
    }
	
	public function RemoveUser($userID)
	{
		// Set class values by function call
		$this->userID = mysqli_real_escape_string($this->_link, $userID);
		
		$this->removeUseracc = $this->_database->query($this->_link, "DELETE FROM ".$this->_database->table_accounts." WHERE id = ('".$this->userID."')") or die(mysqli_error($this->_link));
	}
	
	public function TestMailFunction($to)
	{
		if(!is_object($this->_main))
			throw NEW Exception('Trying to call a function that requires an additional class');
		
		$this->_main->UseFile('../includes/classes/botl.imap.mail.class.php');
		
		$mail = NEW Mail($to, 'Functional Test', '', '');
		$mail->set_default_headers();
		
		echo $mail->send_imap_mail();
	}
	
	public function MoveThread($id, $targetID)
	{
		echo 'Beginne verschieben . . .';
			$this->_database->query('UPDATE '.$this->_database->table_thread.' SET main_forum_id = '.$targetID.' WHERE id = '.$id);
			$this->_database->query('UPDATE '.$this->_database->table_forum_read.' SET board_id = '.$targetID.' WHERE thread_id = '.$id);
		echo 'Verschieben abgeschlossen.';
	}
    
}
?>