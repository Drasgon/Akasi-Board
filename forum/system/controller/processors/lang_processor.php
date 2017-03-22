<?php
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

	
function useLanguage($lang_id = NULL)
{

	$default_lang = 'deDE';

    switch ($lang_id) {
        
        case 1:
            $include_file = './lang/deDE.php';
            break;
        case 2:
            $include_file = './lang/enGB.php';
            break;
        default:
            $include_file = './lang/'.$default_lang.'.php';
            break;
            
    }
	
	return $include_file;
}

if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
if (!isset($_SESSION['language'])) {
    $get_lang = $db->query("SELECT language FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))") or die(mysqli_error($connection));
    while ($lang_data = mysqli_fetch_object($get_lang)) {
        $lang = $lang_data->language;
    }
    
    $_SESSION['language'] = $lang;

	$includer = useLanguage($lang);
	$main->useFile($includer);
    
} else {
	$lang = $_SESSION['language'];

	$includer = useLanguage($lang);
	$main->useFile($includer);
}

} else {
	$includer = useLanguage();
	$main->useFile($includer);
	
}
?>