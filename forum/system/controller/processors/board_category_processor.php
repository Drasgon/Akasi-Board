<?php
include('../../classes/akb_mysqli.class.php');
include('../../classes/akb_main.class.php');

if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

// Set $catID as ID copy of the clicked category.
    $catID = mysqli_real_escape_string($GLOBALS['connection'], $_POST['boardCat_row']);
    
    // Check if a row for the clicked category already exists.
    $checkforRows = $db->query("SELECT user_id FROM $db->table_hiddenboards WHERE user_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "') AND cat_id=('" . $catID . "'))");
    
    // If at least one row was found, update state.
    if (mysqli_num_rows($checkforRows) == 1) {
        
        // Set state to 1 if it was 0 OR 0 if it was 1.
        $db->query("UPDATE $db->table_hiddenboards SET state=CASE WHEN state<1 THEN '1' ELSE '0' END WHERE user_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')) AND cat_id=('" . $catID . "')");
        
        // If no row was found
    } else {
        
        // Perform creating a new row for the category.
        $db->query("INSERT INTO $db->table_hiddenboards (user_id,cat_id, state) VALUES ((SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')),('" . $catID . "'), '0')");
    }
?>