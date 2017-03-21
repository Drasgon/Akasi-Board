<?php

	if (isset($_GET['page']) && !empty($_GET['page']))
		$current_page = $_GET['page'];
	else
		$current_page = 'index';

	$page_data = ARRAY(
		'index' => 'Home',
		'members' => 'Mitglieder',
		'media' => 'Media',
		'aboutus' => 'Über Uns',
		'contact' => 'Impressum',
		'forum' => 'Forum'
	);

	$html = '';
	
	$html .= '
		<title>'.$page_data[$current_page].' - Bane of the Legion</title>
	';
	
	echo $html;
?>