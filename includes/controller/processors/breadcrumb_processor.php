<?php

	if (isset($_GET['page']) && !empty($_GET['page']))
		$current_page = $_GET['page'];
	else
		$current_page = 'index';

	$page_data = ARRAY(
		'index' => ARRAY('iconhome', 'Home'),
		'members' => ARRAY('iconmembers', 'Mitglieder'),
		'media' => ARRAY('iconmedia', 'Media'),
		'aboutus' => ARRAY('iconinfo', 'Über Uns'),
		'contact' => ARRAY('iconcontact', 'Impressum'),
		'forum' => ARRAY('iconforum', 'Forum'),
	);

	$html = '';
	
	$html .= '
		<div class="page_header">
			<div class="icon_big" id="'.$page_data[$current_page][0].'"></div>
			<span class="fancy_font">'.$page_data[$current_page][1].'</span>
		</div>
	';
	
	echo $html;
?>