<?php
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);
	
if (!isset($survey) || $survey == NULL)
{
	$main->useFile('./system/classes/akb_survey.class.php', 1);
    $survey = new Survey($db, $connection, $main);
}

	$survey->initializeSurvey($main->serverConfig('active_survey'));
	
	$portalRedirect = '<meta http-equiv="refresh" content="0;url=?page=Portal">';

$choice = (isset($_POST['akb_survey_choice']) && is_numeric($_POST['akb_survey_choice']) && $_POST['akb_survey_choice'] >= 0) ? mysqli_real_escape_string($connection, $_POST['akb_survey_choice']) : 'notAvailable';

if(isset($_SESSION['USERACCESS']) && $_SESSION['USERACCESS'] == true)
{
	if($choice != 'notAvailable')
	{
		$survey->registerUserVote($main->getUserId(), $choice);
		
		
	}
	
	// When we are absolutely sure the user wants to remove the choice
	if(isset($_GET['action']) && $_GET['action'] == 'submitToSurveyReverse' && isset($_POST['verify']) && $_POST['verify'] == '12098')
	{
		$survey->removeUserVote();
	}
	
}

	echo $portalRedirect;
	
?>