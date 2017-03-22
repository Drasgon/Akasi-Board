<?php
// Re initialize the DB
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {

$main->useFile('./system/interface/errorpage_cc.php');
$main->useFile('./system/interface/successpage.php');

$changeSuccess = 'Ihre Änderung(en) wurde(n) erfolgreich übernommen!';

$activeClass_Account 	 = (isset($_GET['page']) && $_GET['page'] == 'Account' && isset($_GET['Tab']) && $_GET['Tab'] == 'account') ? 'class="navigation_top_listActive"' : '';
$activeClass_CC = (isset($_GET['page']) && $_GET['page'] == 'Account' && isset($_GET['Tab']) && $_GET['Tab'] == 'controlCenter' || $_GET['Tab'] == 'Edit') ? 'class="navigation_top_listActive"' : '';
$activeClass_Lists	 = (isset($_GET['page']) && $_GET['page'] == 'Account' && isset($_GET['Tab']) && $_GET['Tab'] == 'lists') ? 'class="navigation_top_listActive"' : '';
?>
<div class="account_settings_main">
<div class="account_settingsNavigation">
    <div class="navigation_top smoothTransitionMedium">
      <ul>
		
        		<li <?php echo $activeClass_CC ?>>
					<a href="?page=Account&Tab=controlCenter">
						Kontrollzentrum
					</a>
			<div class="navigation_sub">
				<ul>
					<a href="?page=Account&Tab=controlCenter&subPage=displaySettings">
					<li>
					Anzeigeeinstellungen
					</li>
					<a href="?page=Account&Tab=controlCenter&subPage=mail">
					<li>
					E-Mail bearbeiten
					</li>
					</a>
					<a href="?page=Account&Tab=controlCenter&subPage=password">
					<li>
					Passwort ändern
					</li>
					</a>
					<a href="?page=Account&Tab=controlCenter&subPage=security">
					<li>
					Sicherheit
					</li>
					</a>
					<a href="?page=Account&Tab=controlCenter&subPage=lastActivity">
					<li>
					Letzte Account Aktivitäten
					</li>
					</a>
				</ul>
			</div>
        </li>
		
		<a href="?page=Account&Tab=account">
		<li <?php echo $activeClass_Account ?>>
		Account
			    <div class="navigation_sub">
					<ul>
						<a href="?page=Account&Tab=account&subPage=username">
						<li>
						Usernamen bearbeiten
						</li>
						</a>
						</a>
						<a href="?page=Account&Tab=account&subPage=avatar">
						<li>
						Avatar ändern
						</li>
						</a>
						<a href="?page=Account&Tab=account&subPage=signature">
						<li>
						Signatur bearbeiten
						</li>
						</a>
						<a href="?page=Account&Tab=account&subPage=title">
						<li>
						Titel bearbeiten
						</li>
						</a>
						<a href="?page=Account&Tab=account&subPage=gender">
						<li>
						Geschlecht ändern
						</li>
						</a>
						<a href="?page=Account&Tab=account&subPage=rank">
						<li>
						Rang Status ansehen
						</li>
						</a>
						<a href="?page=Account&Tab=account&subPage=lastPosts">
						<li>
						Meine letzten Beiträge
						</li>
						</a>
					</ul>
				</div>
        </li>
		</a>
		<a href="?page=Account&Tab=lists">
		<li <?php echo $activeClass_Lists ?>>
		Listen
			<div class="navigation_sub">
				<ul>
					<a href="?page=Account&Tab=lists&subPage=friends">
					<li>
					Freundesliste
					</li>
					</a>
					<a href="?page=Account&Tab=lists&subPage=blockedUsers">
					<li>
					Blockierte Nutzer
					</li>
					</a>
				</ul>
			</div>
        </li>
		</a>
      </ul>
    </div>
  </div>



<?php
$page 	 = (isset($_GET['page']) && !empty($_GET['page'])) ? $_GET['page'] : '';
$tab	 = (isset($_GET['Tab']) && !empty($_GET['Tab'])) ? $_GET['Tab'] : '';
$subPage = (isset($_GET['subPage']) && !empty($_GET['subPage'])) ? $_GET['subPage'] : '';

if($page == 'Account') {
	
	if($tab == 'controlCenter' || $tab == 'Edit') {
		switch($subPage) {
		
		case 'displaySettings':
			require('./system/account_settings/generalSettings.php');
			break;
		case 'mail':
			require('./system/account_settings/changeMail.php');
			break;
		case 'password':
			require('./system/account_settings/changePassword.php');
			break;
		case 'security':
			require('./system/account_settings/securitySettings.php');
			break;
		case 'lastActivity':
			require('./system/account_settings/lastActivity.php');
			break;
		default:
			require('./system/account_settings/generalSettings.php');
			break;
	}
  }
  
  if($tab == 'account') {
		switch($subPage) {
		
		case 'username':
			require('./system/account_settings/changeUsername.php');
			break;
		case 'avatar':
			require('./system/account_settings/changeAvatar.php');
			break;
		case 'signature':
			require('./system/account_settings/changeSignature.php');
			break;
		case 'title':
			require('./system/account_settings/changeTitle.php');
			break;
		case 'rank':
			require('./system/account_settings/rankDetails.php');
			break;
		case 'lastPosts':
			require('./system/account_settings/lastPosts.php');
			break;
		case 'gender':
			require('./system/account_settings/changeGender.php');
			break;
		default:
			require('./system/account_settings/changeAvatar.php');
			break;
	}
  }
  
	if($tab == 'lists') {
		switch($subPage) {
		
		case 'friends':
			require('./system/account_settings/friendlist.php');
			break;
		case 'blockedUsers':
			require('./system/account_settings/blocklist.php');
			break;
		default:
			require('./system/account_settings/friendlist.php');
			break;
	}
  }
}
?>
</div>
<?php
} else {
	$errorMsg = 'Bitte loggen Sie sich ein, um fortzufahren.';
	throwError($errorMsg);
}
?>