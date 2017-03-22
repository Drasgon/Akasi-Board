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


This class DOES depend on the session class, since there are some properties, that have to correlate with each other.
*/

class MD5Crypt
{
	
	private $cryptResult;
	
	private $cryptKey;
	private $cryptValue;
	private $cryptExtraValue;
	
	public function __construct()
	{
		
	}
	
	
		
	private function generateHash($password, $extraValue, $cryptLevel)
	{
		if(isset($cryptLevel) && isset($extraValue) && isset($password)) {
				
			$this->cryptKey			= $password . $password;
			$this->cryptValue		= $cryptLevel;
			$this->cryptExtraValue	= $extraValue;
			
			for ($i = $this->cryptValue; $i <= $this->cryptValue; $i++) {
				
				$passwordNew = $password . $i;
				
				$var = "$password.$this->cryptKey.$this->cryptExtraValue.$passwordNew";
				
				$passHash			= md5(strtoupper($password) . ":" . strtoupper($var));
				$this->cryptResult	= md5(strtoupper($passHash) . ":" . strtoupper($password));
			}
		}
		else
			return $this->cryptResult = NULL;
	}
		

	public function getHash($password, $extraValue, $cryptLevel)
	{
		$this->generateHash($password, $extraValue, $cryptLevel);
		
			return $this->cryptResult;
	}
	
	
	public function compareHash($hashFirst, $hashSecond)
	{
		if(empty($hashFirst) || empty($hashSecond))
			return FALSE;
		
		if($hashFirst == $hashSecond)		
			return TRUE;
		else
			return FALSE;
	}
}
?>