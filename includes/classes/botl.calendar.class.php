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

class Calendar

  {
	// Declare class vars
    public $_database;
    public $_link;
	
	private 		$calendar_type = CAL_GREGORIAN;
	private 		$columnEndDelimiter = 'end_';
	public 			$days_in_next_month_buffer = 4;
	const			NEW_MONTH_START = 1;
	
	private $prefix_scan_start = 0;
	private $prefix_scan_end = 4;
	private $prefix_offset = 0;
	private $event_end_prefix = 'end_';
	private $db_cols = ARRAY(
		'id', 
		'day_num', 
		'end_day_num', 
		'month_num', 
		'end_month_num', 
		'event_name', 
		'end_event_name', 
		'event_desc', 
		'end_event_desc', 
		'time', 
		'end_time', 
	);
	private $db_cols_end = ARRAY(
		0 => 'end_day_num', 
		1 => 'end_month_num', 
		2 => 'end_event_name', 
		3 => 'end_event_desc', 
		4 => 'end_time'
	);
	
	public $day_names = ARRAY(
		'Montag',
		'Dienstag',
		'Mittwoch',
		'Donnerstag',
		'Freitag',
		'Samstag',
		'Sonntag'
	);
	public $month_names = ARRAY(
		1	=>	'Januar',
		2	=>	'Februar',
		3	=>	'März',
		4	=>	'April',
		5	=>	'Mai',
		6	=>	'Juni',
		7	=>	'Juli',
		8	=>	'August',
		9	=>	'September',
		10	=>	'Oktober',
		11	=>	'November',
		12	=>	'Dezember'
	);

	
	public function __construct($db, $connection)
    {
		$this->_database = $db;
		$this->_link = $connection;
		
		$this->prefix_scan_start += $this->prefix_offset;
		$this->prefix_scan_end += $this->prefix_offset;
    }
	
	
	public function getCalendarData($year, $month)
	{
		$str = $this->_database->build_columns_from_array($this->db_cols);
		$query = $this->_database->query("SELECT ".$str." FROM " . $this->_database->table_guild_calendar . " WHERE year = " . $year . " AND month_num = " . $month . " OR month_num = " . ($month+1));
		
		$all_results = ARRAY();
		while ($result = mysqli_fetch_assoc($query)){
			$all_results[] = $result;
		}
		
		return $all_results;
	}
	
	
	public function getCalendarMonthTemplate($events = NULL, $year, $month)
	{
		if($events == NULL)
			$events = $this->getCalendarData($year, $month);

		$days_in_month = cal_days_in_month($this->calendar_type, $month, $year);
		$current_month = date("n");
		$current_day_num = date("j");
		$next_month_day = self::NEW_MONTH_START;
		$query_month = $month;
		$day_in_month;
		
		$html = '';
		
		for($a = 1; $a <= ($days_in_month+$this->days_in_next_month_buffer); $a++)
		{
			
			
			if($a > $days_in_month)
			{
				$extra_class = 'class="calendar_event_nextmonth"';
				$rawDate = ($month+1).'/'.$next_month_day.'/'.$year;
				$day_in_month = $next_month_day;
				
				$next_month_day++;
				$current_month = $month+1;
				$query_month = $month+1;
			}
			else
			{
				$day_in_month = $a;
				$rawDate = $month.'/'.$a.'/'.$year;
				$passed = (($current_day_num > $a && $current_month == $month) || $current_month > $month) ? 'passed' : '' ;
				$extra_class = ($current_day_num == $a && $current_month == $month) ? 'class="current '.$passed.'"' : 'class="'.$passed.'"';
			}

			/* echo 'CURRENT_DAY_NUM = ' . $day_in_month . '<br>';
			echo 'CURRENT_MONTH = ' . $query_month . '<br>';
			echo 'MONTH = ' . $month . '<br>'; */
			
			$day_num =  date('N', strtotime($rawDate));
			/*echo 'MONTH: ' . $current_month . '<br>';
			echo 'DAY: ' . $day_in_month . '<br>';*/
			
			
			
			$html .= '<li '.$extra_class.'>
							<div class="calendar_day_num">' . $day_in_month . ' - '.$this->day_names[$day_num-1].'</div>';
			
			foreach ($events as $rows)
			{
				
				if(	(($rows['month_num'] == $query_month) && $rows['day_num'] == $day_in_month)
					|| (($rows[$this->db_cols_end[1]] == $query_month) && $rows[$this->db_cols_end[0]] == $day_in_month))
				{
					
					$key_prefix = '';
					
					if($rows['month_num'] == $query_month && $rows['day_num'] == $day_in_month)
					{
						$key_prefix = '';
					}
					if(($rows[$this->db_cols_end[1]] == $month || $rows[$this->db_cols_end[1]] == $query_month) && $rows[$this->db_cols_end[0]] == $day_in_month)
					{
						$key_prefix = $this->event_end_prefix;
					}
					
					if(isset($rows[$key_prefix.'event_img']) && !empty($rows[$key_prefix.'event_img']))
						$event_img = 'background-image:'.$rows[$key_prefix.'event_image'];
					else
						$event_img = '';
					
					if(isset($rows[$key_prefix.'time']) && !empty($rows[$key_prefix.'time']) && $rows[$key_prefix.'time'] != NULL)
						$time = ' - ' . $rows[$key_prefix.'time'] . ' Uhr';
					else
						$time = '';
					
					$html .= '
						<a id="'.$rows['id'].'" class="calendar_event"><div class="calendar_day_event_title">
							'.$rows[$key_prefix.'event_name'].'
						</div></a>
								<div class="calendar_day_event_desc">
									'.$rows[$key_prefix.'event_desc']. $time .'
								</div>
					';
				}
			}
			$html .= '</li>';
		}
		
		return $html;
	}
	
	
	public function getEventEndColumns()
	{
		$data = ARRAY();
		
		foreach($this->db_cols as $key)
		{
			if(is_array($key))
			{
				foreach ($key as $value)
				{
					if($this->event_end_prefix == substr($value, $this->prefix_scan_start, $this->prefix_scan_end))
					array_push($data, $value);
				}
			}
			else
			{
				if($this->event_end_prefix == substr($key, $this->prefix_scan_start, $this->prefix_scan_end))
				array_push($data, $key);
			}
		}
		
		return $data;
	}
	
	
	public function getDayNames($month, $year)
	{
		$days_in_month = cal_days_in_month($this->calendar_type, $month, $year);
		$names = ARRAY();
		
		for($i = 0; $i <= $days_in_month; $i++)
		{
			$names[] = $this->day_names[(date("N", strtotime($i.'.'.$month.'.'.$year))-1)];
		}
		
		return $names;
		
	}
  }
?>