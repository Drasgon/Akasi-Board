<?php
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);
	
	
	$default_template = $main->serverConfig('default_css_template');
	
	function getLayout($id)
	{
		switch($id) {
		case 1:
			$layout_file = '<link rel=stylesheet href="./css/akb-style-1.css" media="screen">';
		break;
		case 2:
			$layout_file = '<link rel=stylesheet href="./css/akb-style-2.css" media="screen">';
		break;
		case 3:
			$layout_file = '<link rel=stylesheet href="./css/akb-widescreen-test.css" media="screen">';
		break;
		default:
			$layout_file = '<link rel=stylesheet href="./css/akb-style-'.$default_template.'.css" media="screen">';
		break;
		}
		
		return $layout_file;
	}
	
if(!isset($_SESSION['STATUS']) || $_SESSION['STATUS'] == false) {

	$layout_file = getLayout($default_template);
	
} else {

	$getActualTemplate = $db->query('SELECT '.$db->table_accdata.'.design_template FROM '.$db->table_sessions.'
									INNER JOIN '.$db->table_accdata.' ON '.$db->table_sessions.'.id = '.$db->table_accdata.'.account_id
									WHERE sid="'.$_SESSION['ID'].'"') or die(mysql_error());
		while($actualTemplate = mysqli_fetch_object($getActualTemplate)) {
				$template = $actualTemplate->design_template;
			}
			
			
	
	$layout_file = getLayout($template);
}
	
	echo $layout_file;
?>