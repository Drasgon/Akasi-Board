<?php

	$page_data = ARRAY(
		'index' => 'Home',
		'members' => 'Mitglieder',
		'calendar' => 'Kalender',
		'aboutus' => 'Über Uns',
		'contact' => 'Impressum',
		'forum' => 'Forum',
	);

	if ((isset($_GET['page']) && !empty($_GET['page'])) && array_key_exists($_GET['page'], $page_data))
		$current_page = $_GET['page'];
	else
		$current_page = 'index';

	$html = '';
	
	$html .= '
		<title>'.$page_data[$current_page].' - Bane of the Legion</title>
	';
	
	echo $html;
?>