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

if (!isset($db) || $db == NULL)
{
	$db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
	$main = new Board($db, $connection);

$main->useFile('./system/classes/akb_admin.class.php');
$main->useFile('./system/interface/errorpage.php', 1);
$admin = new Admin($db, $connection);

	ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(-1);
	
	if(!$main->checkSessionAccess('MOD'))
	{
		throwError('Sie haben leider nicht die notwendigen Zugriffsrechte, um diese Seite zu besuchen.');

		return;
	}


	// STRUCTURE: DisplayName => ((LinkName[AdditionalNameCase, *, * . . .]), id)
	$links = ARRAY(
		'Accounts' => ARRAY(ARRAY('Accounts', 'Verify'), 'navMembers'),
		'Chat' => ARRAY(ARRAY('Chat'), 'profiledit'),
		'Board' => ARRAY(ARRAY('Board'), 'navForum'),
		'Themen' => ARRAY(ARRAY('Threads'), 'thread_def'),
		'Sitzungen' => ARRAY(ARRAY('Sessions'), 'refresh'),
		'Funktionen' => ARRAY(ARRAY('Functions'), 'accountpanel'),
		'TeamSpeak 3' => ARRAY(ARRAY('TeamSpeak'), 'adminicon'),
	);
?>

<link rel=stylesheet href="./css/akb-style-admin.css" media="screen">

<div class="adminPanel">
	<div class="adminSidebar">
		<div class="adminSidebar_header">
			<div class="graphics" id="adminSidebar_header_icon"></div>
		</div>
		<div class="adminSidebar_list">
			<ul>
				<?php
					$links_html = '';
					$class_name = 'class="active"';

					foreach($links as $key => $value)
					{
						
						
						if(isset($_GET['page']) && $_GET['page'] == 'Admin' && isset($_GET['Tab']) && in_array($_GET['Tab'], $value[0]))
							$class = $class_name;
						else
							$class = '';
						
						$links_html .= '
							<li id="'.$value[1].'" '.$class.'>
								<a href="?page=Admin&Tab='.$value[0][0].'">
									<p>
										'.$key.'
									</p>
								</a>
							</li>
						';
					}
					
					echo $links_html;
				?>
			</ul>
		</div>
	</div>
	<div class="adminContainer">
		<?php
			if(isset($_GET['page']) && $_GET['page'] == 'Admin')
			{
				$tab = (isset($_GET['Tab'])) ? $_GET['Tab'] : $links['Accounts'][0][0];
				
				switch($tab)
				{
					case 'Verify':
					case 'Accounts':
					
							$main->useFile('./system/modules/AdminPage/accounts.php');
					break;
					case 'Functions':
							$main->useFile('./system/modules/AdminPage/functions.php');
					break;
					case 'TeamSpeak':
							$main->useFile('./system/modules/AdminPage/teamspeak.php');
					break;
					case 'Threads':
							$main->useFile('./system/modules/AdminPage/threads.php');
					break;
				}
			}
		?>
	</div>
</div>