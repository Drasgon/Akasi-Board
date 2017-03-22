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

	
class Armory
	{
		private $_db;
		private $_connection;
		private $_error;
		private $_formattedURL;
		private $armoryDebugging = FALSE;
		
		private $oauth_key = 'nr2gegr76qe55aw3f96dgq6pzf7b88tu';
		private $url_space_string = '%20';
		
		private $guild_realm = 'Arthas';
		private $guild = 'Bane of the Legion';
		private $locale = 'de_DE';
		private $fields = '';
		private $lastCacheString = "NONE";
		
		private $realm_toreplace = '_guildRealm';
		private $locale_toreplace = '_locale';
		private $guild_toreplace = '_guildName';
		private $fields_toreplace = '_fields';
		private $authkey_toreplace = '_oAuthKey';
		
		private $base_url = 'https://eu.api.battle.net/wow/guild/_guildRealm/_guildName?fields=_fields&locale=_locale&apikey=_oAuthKey';
		private $url;
		
		private $defaultField 		= 'members';
		private $table_base 		= 'table_guild_';
		private $armory_update_time = 3600;
		private $responseJSON;
		
		public $data_type = ARRAY(
			'members' 		=> 1,
			'news' 			=> 2,
			'achievements' 	=> 3
		);
		
		public $table_rows = ARRAY(
			'id',
			'name',
			'gender',
			'race',
			'class',
			'level',
			'avatar',
			'acmpoints',
			'rank'
		);
		
		public $class_data = ARRAY(
			0 => ARRAY('',					'#000000'),
			1 => ARRAY('Krieger', 			'#C79C6E'),
			2 => ARRAY('Paladin', 			'#F58CBA'),
			3 => ARRAY('Jäger', 			'#ABD473'),
			4 => ARRAY('Schurke', 			'#FFF569'),
			5 => ARRAY('Priester', 			'#FFFFFF'),
			6 => ARRAY('Todesritter', 		'#C41F3B'),
			7 => ARRAY('Schamane', 			'#0070DE'),
			8 => ARRAY('Magier', 			'#69CCF0'),
			9 => ARRAY('Hexenmeister', 		'#9482C9'),
			10 => ARRAY('Mönch', 			'#558A84'),
			11 => ARRAY('Druide', 			'#FF7D0A'),
			12 => ARRAY('Dämonenjäger',		'#A330C9'),
		);
		
		public $guild_ranks = '';
		
		
		public function __construct($db, $connection, $error)
		{
			$this->_db = $db;
			$this->_connection = $connection;
			$this->_error = $error;
			
			$this->guild = str_replace(' ', $this->url_space_string, $this->guild);
			$guildRealm = str_replace(' ', $this->url_space_string, $this->guild_realm);
			
			$this->url = str_replace($this->realm_toreplace, $guildRealm, $this->base_url);
			$this->url = str_replace($this->locale_toreplace, $this->locale, $this->url);
			$this->url = str_replace($this->guild_toreplace, $this->guild, $this->url);
			$this->url = str_replace($this->authkey_toreplace, $this->oauth_key, $this->url);
			
			$this->guild_ranks = parse_ini_file('config/ranks.ini', TRUE);
			$this->guild_ranks = $this->guild_ranks["ranks"];
		}
		
		
		
		public function generateURL($fields = 'members')
		{
			return $this->_formattedURL = str_replace($this->fields_toreplace, $fields, $this->url);
		}
		
		
		public function get_guild_data($fields)
		{

			if($this->check_cache_time($this->data_type[$fields]) === TRUE)
			{
				
				$this->lastCacheString = $this->generateURL($fields);
					if($this->armoryDebugging == TRUE)
					{
						ini_set('display_startup_errors',1);
						ini_set('display_errors',1);
						error_reporting(-1);
					}
					
				$arrContextOptions=array(
					"ssl"=>array(
						"verify_peer"=>false,
						"verify_peer_name"=>false,
					),
				);  
				$this->responseJSON = file_get_contents($this->lastCacheString, false, stream_context_create($arrContextOptions));
				if($this->armoryDebugging == TRUE)
					var_dump($this->responseJSON);
				
					if($this->responseJSON === false)
						$this->responseJSON = false;
					else
						$this->responseJSON = json_decode($this->responseJSON);
			}
				
				if($this->responseJSON)
				{
					// Automatically cache the results in the database
					$this->store_in_database($this->responseJSON, $fields);
				}
				
			return $this->responseJSON;
		}
		
		
		public function get_cache_time($type)
		{
			if(array_key_exists($type, $this->data_type))
			{
				$checkKey = $this->_db->query("SELECT timestamp FROM ".$this->_db->table_armory_time." WHERE type=".$this->data_type[$type]);
					if(mysqli_num_rows($checkKey) >= 1)
					{
						$row = mysqli_fetch_array($checkKey);
						$timestamp = $row[0];
					}
				
				if(isset($timestamp))
					return $timestamp;
				else
					return false;
			}
		}
		
		
		public function set_cache_time($type)
		{
			if(array_key_exists($type, $this->data_type))
			{
				if($this->check_cache_time($type) === true)
					$this->_db->query("INSERT INTO ".$this->_db->table_armory_time." (type, timestamp, query_string) VALUES (".$this->data_type[$type].", ".time().", '".$this->lastCacheString."') ON DUPLICATE KEY UPDATE timestamp='".time()."', query_string='".$this->lastCacheString."'");
			}
		}
		
		
		public function check_cache_time($type)
		{
			$time = $this->get_cache_time($type);
			
			if((isset($time) && (time() - $time >= $this->armory_update_time)) || !isset($time) || (isset($time) && $time == false))
				return true;
			else
				return false;
		}
		
		
		public function renew_database_store($type, $fields = 'members')
		{
			if(array_key_exists($type, $this->data_type) && $this->check_cache_time($type))
			{
				$json = $this->get_guild_data($fields);
				
				if($json)
				{
					$clear = $this->clear_database_store($type);
					$this->set_cache_time($type);
					
					return $json;
				}
				else
				{
					$this->set_cache_time($type);
					
					return false;
				}
				
			}
			else
				return false;
		}
		
		
		public function clear_database_store($type)
		{
			if(array_key_exists($type, $this->data_type))
			{
				$table = $this->_db->{$this->table_base.$type};
				
				$query = $this->_db->query("TRUNCATE TABLE ".$table);
				if($query)
					$query_armory = $this->_db->query("DELETE FROM ".$this->_db->table_armory_data." WHERE type=".$this->data_type[$type]);
					
				if(isset($query_armory) && $query_armory)
					return true;
				else
					return false;
			}
		}
		
		
		public function store_in_database($data, $fields)
		{
			if(array_key_exists($fields, $this->data_type))
			{
				/*
					Find current max "valid_id" and increase it by 1
				*/
					$validate_identifier = $this->_db->query("SELECT MAX(valid_id) AS valid_id FROM ".$this->_db->table_armory_data);
					$row = mysqli_fetch_array($validate_identifier);
					$validate_identifier = $row[0];
					$validate_identifier++;
					
					foreach($data->{$fields} as $object => $field)
					{
						$data_serial = addslashes(serialize($field));
						
						$object_serial = addslashes($object);
						$this->_db->query("INSERT INTO ".$this->_db->table_armory_data." (valid_id, type, object_name, value) VALUES('".$validate_identifier."', '".$this->data_type[$fields]."', '".$object_serial."', '".$data_serial."')");
					}
					
				$this->set_cache_time($fields);
			}
		}
		
		
		public function read_database_store($type, $fields, $search_string, $sort_by, $direction, $limit = '')
		{
			if(array_key_exists($type, $this->data_type))
			{
				$table = $this->_db->{$this->table_base.$type};
			
				$data = $this->_db->query("SELECT ".$fields." FROM ".$table." WHERE ".$search_string." ORDER BY ".$sort_by." ".$direction.$limit);
				
				return $data;
			}
		}
		
		
		public function get_rank($rank_id)
		{
			return $this->guild_ranks[$rank_id];
		}
		
		
		public function getClassData($classID)
		{
			return $this->class_data[$classID];
		}
	}
?>