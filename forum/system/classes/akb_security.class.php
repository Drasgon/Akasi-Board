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


This class DOES depend on the Board class, since there are some properties, that have to correlate with each other.
*/

class Securityquestion extends Board
{
	// Set default variables
	private $questionData = ARRAY();
	private $clientIp;
	
	private $answerSeparator = ';;';
	private $answers = ARRAY();
	
	
	public function __construct()
	{
		parent::__construct();
		
		// var_dump(get_object_vars());
		$this->_link = parent::getClassProperty('_link');
		
		$this->clientIp = $this->getUserIp();
		
	}
	
	
	public function setQuestion()
	{
		$query = $this->query('SELECT id FROM '.$this->table_securityquestions.' ORDER BY RAND() LIMIT 1');
		if($result = mysqli_fetch_object($query))
		{
			$id 	= $result->id;
			
			$setData = $this->query('INSERT INTO '.$this->table_security_data.' (client_ip, question_id) VALUES ("'.$this->clientIp.'", '.$id.') ON DUPLICATE KEY UPDATE question_id = '.$id);
		}
	}
	
	
	public function getQuestion()
	{
		$query = $this->query('SELECT question_id FROM '.$this->table_security_data.' WHERE client_ip = "'.$this->clientIp.'"');
		if($result = mysqli_fetch_object($query))
		{
			$questionId 	= $result->question_id;
			$questionQuery = $this->query('SELECT question, answer FROM '.$this->table_securityquestions.' WHERE id = '.$questionId);
			if($data = mysqli_fetch_object($questionQuery))
			{
				$this->questionData = ARRAY(
					'question'	=>	$data->question,
					'answer'	=>	$data->answer
				);
				
				$this->parseAnswers();
				
				return $this->questionData['question'];
			}
			else
				return FALSE;
			
		}
		else
		{
			$this->setQuestion();
			
			return FALSE;
		}
	}
	
	
	public function checkAnswer($answer)
	{
		if(empty($this->answers))
			$this->getQuestion();
		
		if(in_array(strtolower($answer), array_map('strtolower', $this->answers)))
			return TRUE;
		else
			return FALSE;
	}
	
	
	private function parseAnswers()
	{
		return $this->answers = explode($this->answerSeparator, $this->questionData['answer']);
	}
	
	
	
	
	
	
	
}
?>