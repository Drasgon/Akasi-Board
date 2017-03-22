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

	require_once(dirname(__FILE__).'/../../security/callstack_validation.php');

	if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);

	if (!isset($admin) || $admin == NULL)
		$admin = new Admin($db, $connection);
	
	if(!$main->checkSessionAccess('MOD'))
		exit();

	$main->UseFile('./system/modules/AdminPage/processors/admin_threads.php');
?>

<?php

	$lastForumName = '';

	$list = '
	<form method="POST" action="?page=Admin&Tab=Threads&action=moveThread">
		<h2>Ein Thema verschieben</h2>
		<br>
		<p>Ein Thema wählen</p>
		<select name="moveThreadID">';

		$getThreads = $db->query('SELECT id, title, main_forum_id FROM '.$db->table_thread.' WHERE id ORDER BY main_forum_id ASC, title ASC');
		while($threads = mysqli_fetch_object($getThreads))
		{
			$id = $threads->id;
			$title = $threads->title;
			$main_forum_id = $threads->main_forum_id;
			
			$getThreadBoard = $db->query('SELECT title FROM '.$db->table_boards.' WHERE id = '.$main_forum_id);
				if($threadBoard = mysqli_fetch_object($getThreadBoard))
				{				
					$forumName = $threadBoard->title;
					
					// If a new Forum was accessed
					if($lastForumName != $forumName)
					{
						// Create a "Title in this list"
						$list .= '
							<option></option>
							<option class="threadMoveListTitle">
							<><>----------<><> '.mb_strtoupper($forumName, 'UTF-8').' <><>----------<><>
							</option>
							<option></option>
						';
					}
					
					$lastForumName = $forumName;
				}
				
				
				$list .= '
					<option value="'.$id.'">
						<span class="threadMoveListForumName">('.$lastForumName.')</span> - '.$title.'
					</option>
				';
			
		}
		
		$list .= '
		</select>
		<br>
		<br>
		<br>
		<p>Einen Bereich wählen</p>
		<select name="moveThreadIDTarget">';

		$getBoards = $db->query('SELECT id, title FROM '.$db->table_boards.' WHERE id ORDER BY title');
		while($boards = mysqli_fetch_object($getBoards))
		{
			$id = $boards->id;
			$title = $boards->title;
			
			$list .= '
				<option value="'.$id.'">
					'.$title.'
				</option>
			';
		}
	
	$list .= '</select>
		<br>
		<br>
		<input type="submit">
	</form>';
	
	echo $list;
?>