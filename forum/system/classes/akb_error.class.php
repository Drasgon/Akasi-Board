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

The purpose of this class is, to show an customized error inside of the HTML wrapper.
*/



class Error
{
	// Define error array, to hold multiple errors at once
	public $errors = ARRAY();
	
	private $errorMsg;
	
	private $html 		= '';
	private $data		= '';
	private $htmlOpen 	= '<div class="akb-action-error"><h3>';
	private $htmlClose	= '</ul></div>';
	
	
	public function __construct($errorHeaderStr)
	{
		$this->errorMsg = $errorHeaderStr;
	}
	
	public function addError($str)
	{
		array_push($this->errors, $str);
	}
	
	private function processErrors()
	{
		for($i = 0; $i < sizeof($this->errors); $i++)
		{
			$this->data .= '<li>'.$this->errors[$i].'</li>';
		}
	}
	
	private function generateOutput()
	{
		$this->html = $this->htmlOpen . $this->errorMsg . '</h3><ul>' . $this->data . $this->htmlClose;
	}
	
	public function getOutput()
	{
		$this->processErrors();
		$this->generateOutput();
	
		if(!empty($this->errors))
			return $this->html;
		else
			return false;
	}
}