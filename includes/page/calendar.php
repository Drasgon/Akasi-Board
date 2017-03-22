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
	// Use required libraries
	include('includes/classes/botl.mysqli.class.php');
	include('includes/classes/botl.error.class.php');
	include('includes/classes/botl.calendar.class.php');
	
	// Initialize classes
	if (!isset($error) || $error == NULL)
	{
		$error = NEW ErrorHandler();
	}
	if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($calendar) || $calendar == NULL)
	{
		$calendar = NEW Calendar($db, $connection);
	}

	// Set timezone
	date_default_timezone_set("Europe/Berlin");

	// Runtime variables
	$calendar_type = CAL_GREGORIAN;
	$current_month = date("n");
	$days_in_next_month_buffer = 4;
	$month = (!isset($_GET['month']) 
			|| (isset($_GET['month']) && (empty($_GET['month']) || $_GET['month'] <= 0 || $_GET['month'] > 12))
			) ? $current_month : mysqli_real_escape_string($connection, $_GET['month']);
	$months_per_year = 12;
	$year = date("Y");
	// Get all events for this (and next) month
	$events = $calendar->getCalendarData($year, $month);
	$event_ends = $calendar->getEventEndColumns();
	
	$header_months = '<ul class="calendar_months">';
	$calendar_html = '<div class="calendar">
					<ul>';
	$calendar_end = '
					</ul>
				</div>';
	
		// ------------------------------------------------------------------------------------------------------------------------------
	
		// Display months
		for($i = 1; $i <= $months_per_year; $i++)
		{
			if($i == $month)
				$mark = 'class="selected"';
			else
				$mark = '';
			
			$header_months .= '<a href="?page=calendar&month='.$i.'"><li '.$mark.'>' . $calendar->month_names[$i] . '</li></a>';
		}
		
		// ------------------------------------------------------------------------------------------------------------------------------
		
		// Add template for every day
		
		$calendar_html .= $calendar->getCalendarMonthTemplate($events, $year, $month);
		
		/*$days_in_month = cal_days_in_month($calendar_type, $month, $year);
		$days_in_next_month = cal_days_in_month($calendar_type, $month+1, $year);
		$day_names = $calendar->getDayNames($month, $year);
		
		$current_day_num = date("j");
		
		for($a = 1; $a <= $days_in_month; $a++)
		{
			
			$passed = ($current_day_num > $a && $current_month == $month || $current_month > $month) ? 'passed' : '' ;
			$extra_class = ($current_day_num == $a && $current_month == $month) ? 'class="current '.$passed.'"' : 'class="'.$passed.'"' ;
			
			
			$calendar_html .= '<li '.$extra_class.'>
							<div class="calendar_day_num">' . $a . ' - '.$day_names[$a].'</div>';
			
			foreach ($events as $rows)
			{
				
				if($rows['month_num'] == $month && $rows['day_num'] == $a)
				{
					
					if(isset($rows['event_img']) && !empty($rows['event_img']))
						$event_img = 'background-image:'.$rows["event_image"];
					else
						$event_img = '';
					
					if(isset($rows['time']) && !empty($rows['time']) && $rows['time'] != NULL)
						$time = ' - ' . $rows['time'] . ' Uhr';
					else
						$time = '';
					
					$calendar_html .= '
						<a id="'.$rows["id"].'" class="calendar_event"><div class="calendar_day_event_title">
							'.$rows["event_name"].'
						</div></a>
								<div class="calendar_day_event_desc">
									'.$rows["event_desc"]. $time .'
								</div>
					';
				}
			}
			$calendar_html .= '</li>';
		}*/
		
		
		
		// ------------------------------------------------------------------------------------------------------------------------------
	
	$calendar_html .= $calendar_end;
	
	echo $header_months . '</ul>';
	echo $calendar_html;
?>