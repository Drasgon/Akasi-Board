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

class SSH
{
	// 
	private $authKey 	= '347862347867843490238929042367842548';
	private $host 		= 'http://v22016103860138105.quicksrv.de/';
	
	private $_commands = ARRAY(
		1 => '0x800100', 	// TEAMSPEAK_START
		2 => '0x800110',	// TEAMSPEAK_STOP
		3 => '0x800120'		// TEAMSPEAK_RESTART
	);
	/*private $command_teamspeak_start = 'TEAMSPEAK_START';
	private $command_teamspeak_stop = 'TEAMSPEAK_START';
	private $command_teamspeak_restart = 'TEAMSPEAK_START';*/
	
	private $postData = '';
	private $opts = ARRAY();
	public $data = ARRAY();


	
	
	public function controlTeamSpeak($commandID)
	{
		switch($commandID)
		{
			case 1:
				$this->performCommand($this->_commands[1]);
			break;
			case 2:
				$this->performCommand($this->_commands[2]);
			break;
			case 3:
				$this->performCommand($this->_commands[3]);
			break;
		}
	}

	
	public function performCommand($command)
	{
		$this->buildPostData($command);
		$this->buildOperators();
		
		$this->data = file_get_contents($this->host, false, $this->opts);
	}

	
	public function buildPostData($data)
	{
		$this->postData = http_build_query(
			array(
				'authKey' => $this->authKey,
				'performSSHAction' => $data
			)
		);
	}

	
	// Build operator array according to the command
	private function buildOperators()
	{
		$this->opts = stream_context_create(array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $this->postData
			)
		));
	}
}
?>