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

Gallery class for Akasi Board ©
Copyright 2015, Alexander Bretzke - All rights reserved
*/
class Gallery

{
	// Declare MySql Variables
    private $_database;
    private $_link;
	
	// Directories
	
	public $userBase;
	public $dirBase;
	public $mainDir;
	public $publicDirShort;
	public $tempDirShort;
	public $publicDirectory;
	public $tempDirectory;
	public $userDirectory;
	
	// Thumbnail target height
	public $thumbTargetY = 180;
	
	public $sid;
	
	private $icon_image_not_found = './images/icons/99.png';
	
	public $themes = array(
		'General Art',
		'Fantasy',
		'Abstract',
		'Comics'
	);
		
	public $category = array(
		'Traditional',
		'Digitalart',
		'Painting',
		'Photography',
		'Sketch'
	);
		
	public $rating = array(
		'Jugendfreigabe',
		'Keine Jugendfreigabe ( Gewalt )',
		'Keine Jugendfreigabe ( Sexuelle Inhalte )'
	);
	
	/*****
         * Build Variables for access to table names
         *
         * @ PARAM:
		 * 1.: Database class
		 * 2.: MySqli link
    ******/
    public function __construct($database, $link, $main = NULL)
    {
        $this->_database = $database;
        $this->_link     = $link;
		if($main != NULL)
			$this->_main = $main;
		
		$this->dirBase = './images/gallery/';
		
			if(isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true)
			{
				$this->userBase = $this->dirBase.$_SESSION['USERNAME'];
				$this->userid = $_SESSION['USERID'];
			}
		
			$this->publicDirShort = "public";
			$this->publicDirectory = $this->userBase."/".$this->publicDirShort."/";
			$this->tempDirShort = "temp";
			$this->tempDirectory = $this->userBase."/".$this->tempDirShort."/";
	
    }

	function processRating($rating_val)
	{
		$ratingString = $this->rating[$rating_val];
		
		return $ratingString;
	}
	
	
	public function processTheme($theme_val)
	{
		$themeString = $this->themes[$theme_val];
		
		return $themeString;
	}
	
	
	public function processCategory($category_val)
	{
		$categoryString = $this->category[$category_val];
		
		return $categoryString;
	}
	
	
	public function getOption($array)
	{
		switch($array)
		{
			case 'rating':
					$option = $this->rating;
				break;
			case 'theme':
					$option = $this->themes;
				break;
			case 'category':
					$option = $this->category;
				break;
				
			default:
					$option = $this->rating;
				break;
		}
		
		return $option;
	}
	
	
	public function imageData($searchFor, $type)
    {
		// Escape the "Input"
        $searchFor		= mysqli_real_escape_string($this->_link, $searchFor);
		$type			= mysqli_real_escape_string($this->_link, $type);
        
		// What should be selected?
		$selectors = "id, img_name, img_display_name, img_description, uploaded_by_id, comments, favorites, category, theme, rating";
			
			// Build first part of the query
			$getData = "SELECT $selectors FROM (".$this->_database->table_gallery_data.") WHERE (".$type.")=(".$searchFor.")";
				
				// Execute the built query
				$getData = $this->_database->query($getData);
				
				// Fetch all query data and throw them straight into an array
                while ($resolveData = mysqli_fetch_object($getData)) {
				
                    $data = array(
                        
						'id' 				=> $resolveData->id,
                        'img_name' 			=> $resolveData->img_name,
                        'img_display_name' 	=> $resolveData->img_display_name,
                        'img_description' 	=> $resolveData->img_description,
                        'uploaded_by_id' 	=> $resolveData->uploaded_by_id,
                        'favorites' 		=> $resolveData->favorites,
                        'category' 			=> $resolveData->category,
						'theme' 			=> $resolveData->theme,
						'rating' 			=> $resolveData->rating
                        
                    );
                }
				
		// Give $data a value, if the query fails or does not returns any data
		if (!isset($data) || empty($data)) { $data= 'No data returned.'; }
        
		// Return the entire thing
        return $data;
    }
	
	
	public function checkArrayKey($arrayType, $value)
	{
		$array = $this->getOption($arrayType);
		
		if (array_key_exists($value, $array)) return true;
		else return false;
	}
	
	
	public function getLastUploaded($userId)
	{
		$lastUploaded = $this->_database->query("SELECT img_name FROM ".$this->_database->table_gallery_directory." WHERE uploader_id=('".$userId."') ORDER BY id DESC LIMIT 1");
		
		if($data = mysqli_fetch_object($lastUploaded))
			return $data->img_name;
		else
			return false;
	}
	
	
	public function publishImage($imageName, $imageTitle, $imageDesc, $imageTheme, $imageRating, $imageCategory)
	{
		
		// Get base name
		$imageName = explode("-", $imageName);
		$baseName = $imageName[1];
		
		// Move Image
		rename($this->tempDirectory."img-".$baseName, $this->publicDirectory."img-".$baseName);
		rename($this->tempDirectory."thumb-".$baseName, $this->publicDirectory."thumb-".$baseName);
		
		$timestamp = time();
		
		$publish = $this->_database->query("INSERT INTO ".$this->_database->table_gallery_data." (id, img_name, img_display_name, img_description, theme, rating, category, uploaded_by_id, upload_time) VALUES (
		(SELECT id FROM ".$this->_database->table_gallery_directory." WHERE img_name='img-".$baseName."'),
		'img-".$baseName."',
		'".$imageTitle."',
		'".$imageDesc."',
		'".$imageTheme."',
		'".$imageRating."',
		'".$imageCategory."',
		(SELECT uploader_id FROM ".$this->_database->table_gallery_directory." WHERE img_name='img-".$baseName."'),
		'".$timestamp."'
		)");
		
		if($publish)
		{
				$files = glob($this->tempDirectory.'*'); // get all file names
				foreach($files as $file){ // iterate files
				  if(is_file($file))
					unlink($file); // delete file
				}
			return true;
		}
		else
			return false;
	}
	
	
	public function getUserDir($username)
	{
		$imagePath = $this->dirBase.$username."/".$this->publicDirShort."/";
		
		return $imagePath;
	}
	
	
	public function getDefaultImage()
	{
		return $this->icon_image_not_found;
	}
	
	
	public function validateImage($file)
	{
		if(!$this->_main->checkImage($file))
			return $this->getDefaultImage();
		else
			return $file;
	}
	
}

?>
