<?php

	$page_data = ARRAY(
		'index' => 'Home',
		'rules' => 'Regelwerk',
		'contact' => 'Impressum',
		'media' => 'Media',
	);

	if ((isset($_GET['page']) && !empty($_GET['page'])) && array_key_exists($_GET['page'], $page_data))
		$current_page = $_GET['page'];
	else
		$current_page = 'index';

	$html = '';
	
	$html .= '
		<title>'.$page_data[$current_page].' - '.$GLOBAL["appName"].'</title>
	';
	
	echo $html;
?>