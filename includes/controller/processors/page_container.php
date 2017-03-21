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


global $totalQueries, $totalQueryTime, $totalGenerateTime;

if(isset($_GET['AJAX_CALL']) && $_GET['AJAX_CALL'] == TRUE)
{
	echo '
			<div class="main_content_inner">';
			include('page_container_processor.php');
	echo '
			</div>';
}
else
{
	echo '
		<div class="main_content">
			<div class="main_content_inner">';
			include('page_container_processor.php');
	echo '
		</div>';
}
			
	echo '
		<footer>
			<div class="footer_inner">
					<span class="fancy_font">System Version: 0.0.3a Development Release - Database Version: 0.1a | Copyright 2015, Alexander Bretzke - All rights reserved</span><br>
					<span class="fancy_font">Query execution time: '.(round($totalQueryTime * 1000, 3)).' ms - Queries fired: '.$totalQueries.'</span><br>
					<span class="fancy_font">Page generated in: '.(round((microtime(TRUE) - $totalGenerateTime) * 1000, 3)).' ms</span>
			</div>
		</footer>
	</div>
';
?>