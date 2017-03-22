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


This class is meant to output a fully functional survey that can just be replied by registered members.
*/

class Survey
{
	// Set default values
    private $_database;
    private $_link;
	private $_main;
	
	public $id;
	public $choices;
	public $name;
	public $description;
	private $choiceSeparator = ';;';
	public $html;
	private $noSurveys = '<p class="no_surveys">Momentan gibt es keine Umfragen!</p>';
	private $formAction = '?page=Portal&action=submitToSurvey';
	private $formActionReverse = '?page=Portal&action=removeFromSurvey';
	private $htmlList;
	private $htmlListPlaceholder;
	private $lastResult;
	
	// Replace values for templates
	private $inputValueReplace = '$$ChoiceValue';
	private $listElementReplace = '$$ChoiceData';
	private $currentListReplace = '$$CurrentListElement';
	private $mainElementReplace = '$$ListData';
	private $formActionReplace = '$$FormAction';
	private $formActionReverseReplace = '$$FormActionReverse';
	private $currentListWidthReplace = '$$currentListResult';
	
	
	// Templates
	private $mainElementVote = '
		<form action="$$FormAction" method="POST">
			<ul>
				$$ListData
			</ul>
			<input type="hidden" value="12098" name="verify">
			<input type="submit" value="Abstimmen">
		</form>';
	private $mainElementVoted = '
			<ul>
				$$ListData
			</ul>
			<form action="$$FormActionReverse" method="POST">
				<input type="hidden" value="12098" name="verify">
				<input type="submit" value="Stimme rückgängig machen">
			</form>';
	private $listElementVote = '
		<li>
			<input type="radio" name="akb_survey_choice" id="$$CurrentListElement" value="$$ChoiceValue">
			<label for="$$CurrentListElement">$$ChoiceData</label>
		</li>';
	private $listElementVoted = '
		<li class="survey_bars">
			$$ChoiceData (Stimmen: $$currentListResult)
		</li>';
	
    
	
	
    public function __construct($database, $link, $main)
    {
        $this->_database = $database;
        $this->_link     = $link;
		$this->_main	 = $main;
    }
    
	public function initializeSurvey($id)
	{
		if($id == 'latest')
		{
			$this->id = $this->_database->query('SELECT max(id) FROM ' . $this->_database->table_survey . ' ORDER BY id DESC LIMIT 1');
			$this->id = mysqli_fetch_array($this->id);
			$this->id = $this->id['max(id)'];
		}
		else
			$this->id = $id;
		
		if($this->loadSurvey($this->id))
		{
			$this->parseChoices($this->choices);
			
			if($this->_main->checkSessionAccess("USER"))
			{
				if(!$this->userVoted($this->_main->getUserId()))
				{
					for($i = 0; $i < count($this->choices); $i++)
					{
						$this->htmlListPlaceholder = $this->parseElement($this->listElementReplace, $this->choices[$i], $this->listElementVote);
						$this->htmlListPlaceholder = $this->parseElement($this->currentListReplace, $i, $this->htmlListPlaceholder);
						$this->htmlListPlaceholder = $this->parseElement($this->inputValueReplace, $i, $this->htmlListPlaceholder);
						
						$this->htmlList .= $this->htmlListPlaceholder;
					}
					
					$this->html = $this->parseElement($this->mainElementReplace, $this->htmlList, $this->mainElementVote);
					$this->html = $this->parseElement($this->formActionReplace, $this->formAction, $this->html);
				}
			}
				
			if(!$this->_main->checkSessionAccess("USER") || $this->userVoted($this->_main->getUserId()))
			{
				for($i = 0; $i < count($this->choices); $i++)
				{
					$this->htmlListPlaceholder = $this->parseElement($this->listElementReplace, $this->choices[$i], $this->listElementVoted);
					$this->htmlListPlaceholder = $this->parseElement($this->currentListReplace, $i, $this->htmlListPlaceholder);
					$this->htmlListPlaceholder = $this->parseElement($this->inputValueReplace, $i, $this->htmlListPlaceholder);
					$this->htmlListPlaceholder = $this->parseElement($this->currentListWidthReplace, $this->currentListVotes($i), $this->htmlListPlaceholder);
					
					$this->htmlList .= $this->htmlListPlaceholder;
				}
				
				$this->html = $this->parseElement($this->mainElementReplace, $this->htmlList, $this->mainElementVoted);
				$this->html = $this->parseElement($this->formActionReplace, $this->formAction, $this->html);
				
				if($this->_main->checkSessionAccess("USER"))
				{
					$this->html = $this->parseElement($this->formActionReverseReplace, $this->formActionReverse, $this->html);
				}
			}
			
		}
		else
			$this->html = $this->noSurveys;
		
	}
	
	public function removeUserVote()
	{
		if($this->_main->checkSessionAccess("USER"))
		{
			if($this->userVoted($this->_main->getUserId()))
			{
				if($this->_database->query('DELETE FROM '.$this->_database->table_survey_data.' WHERE user_id='.$this->_main->getUserId().' AND id='.$this->id))
					return true;
				else
					return false;
			}
		}
	}
	
	private function parseChoices($str)
	{
		return $this->choices = explode($this->choiceSeparator, $str);
	}
	
	protected function parseElement($search, $replace, $str)
	{
		return str_replace($search, $replace, $str);
	}
	
	private function userVoted($userid)
	{
		if($userid != false)
		{
			$result = $this->_database->query('SELECT user_id FROM '.$this->_database->table_survey_data.' WHERE id='.$this->id.' AND user_id='.$userid);
			if(mysqli_num_rows($result) == 1)
				return true;
			else
				return false;
		}
		else
			return false;
	}
	
	private function currentListVotes($index)
	{
		$result = $this->_database->query('SELECT id FROM '.$this->_database->table_survey_data.' WHERE id='.$this->id.' AND value='.$index);

		return mysqli_num_rows($result);
	}
	
	public function registerUserVote($userid, $choice)
	{
		if(!$this->userVoted($userid))
		{
			if(array_key_exists($choice, $this->choices))
			{
				$result = $this->_database->query('INSERT INTO '.$this->_database->table_survey_data.' (id, user_id, value) VALUES ('.$this->id.', '.$userid.', '.$choice.')');
				if($result)
					return true;
			}
			else
				return false;
			
			
		}
		else
			return false;
	}
	
	public function loadSurvey($id)
	{
		if(is_numeric($id))
		{
			$query = $this->_database->query('SELECT name, description, choices FROM ' . $this->_database->table_survey . ' WHERE id=' . $id);
			if($result = mysqli_fetch_object($query))
			{
				$this->name 	= $result->name;
				$this->description 	= $result->description;
				$this->choices 	= $result->choices;
				
				return true;
			}
			else // If not so, an error occured
				return false;
		}
		else
			return false;
	}
    
}
?>