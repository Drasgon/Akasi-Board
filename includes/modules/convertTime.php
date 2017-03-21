<?php
function convertTime($time)
	{
			if (date('Y-m-d', $time) == date('Y-m-d')) {
                $timeConverted = strftime('<span class="timeRange">Heute</span>, %H:%M', $time);
            } elseif (date('Y-m-d', $time) == date('Y-m-d', strtotime("Yesterday"))) {
                $timeConverted = strftime('<span class="timeRange">Gestern</span>, %H:%M', $time);
            } elseif (date('Y-m-d', $time) < date('Y-m-d', strtotime("Yesterday"))) {
                $timeConverted = strftime("%A, %d %B %Y %H:%M", $time);
            }
			
		return utf8_encode($timeConverted);
	}
?>