<?php
/*
Copyright (C) 2015  Alexander Bretzke

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



	German Data for Akasi Board ©
	Copyright 2015, Alexander Bretzke - All rights reserved
*/

global $langGlobal;

setlocale(LC_ALL, null);
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

$langGlobal['sPWelcome']         = 'Willkommen';
$langGlobal['sPLogout']          = 'Abmelden';
$langGlobal['sPProfileEdit']     = 'Profil bearbeiten';
$langGlobal['sPMessages']        = 'Nachrichten';
$langGlobal['sPControlCenter']   = 'Kontrollzentrum';
$langGlobal['sPAdmin']           = 'Administration';
$langGlobal['sPNotlogin_status'] = 'Sie sind nicht eingeloggt';
$langGlobal['sPLogin']           = 'Einloggen';
$langGlobal['sPRegister']        = 'Registrieren';
$langGlobal['sPHelp']            = 'Hilfe';

$langGlobal['data_missed'] = 'Sie haben nicht alle notwendigen Informationen angegeben!';
$langGlobal['permission_denied'] = 'Sie besitzen nicht die erforderlichen Rechte um auf diese Seite zugreifen zu können!';


$langGlobal['function_in_development']			= 'Diese Funktion befindet sich derzeit in Entwicklung';

$langGlobal['max_users_reached_string_first'] = 'Die maximal zugelassene Anzahl gleichzeitig aktiver Nutzer ( ';
$langGlobal['max_users_reached_string_sec']   = ' ) wurde erreicht oder überschritten. Weitere Logins sind zurzeit nicht möglich, versuchen Sie es später erneut.';
$langGlobal['username_illegal_chars']         = 'Username enthält ungültige Zeichen.';
$langGlobal['invalid_pass_or_username']       = 'Ungültiger Username oder Passwort';
$langGlobal['account_not_validated']          = 'Dieser Account wurde noch nicht von der Administration freigeschaltet.';
$langGlobal['account_ban_temp_first']         = 'Dieser Account ist bis ';
$langGlobal['account_ban_temp_time_first']	  = 'zum ';
$langGlobal['account_ban_temp_second']        = ' gesperrt.';
$langGlobal['account_ban_perm']               = 'Dieser Account ist dauerhaft gesperrt.';

$langGlobal['ip_ban_temp']					  = 'Sie wurden aufgrund zu vieler fehlgeschlagener Login Versuche für weitere gesperrt. Versuchen Sie es später erneut.';
$langGlobal['login_critical_error']           = 'Kritischer Loginfehler. Versuchen Sie es später erneut. Sollte das Problem bestehen bleiben, wenden Sie sich an die Administration.';
$langGlobal['login_success']                  = 'Sie wurden erfolgreich eingeloggt!';

$langGlobal['portal_lang_welcome']          = 'Willkommen, falls dies Ihr erster Besuch auf dieser Seite ist, klicken Sie <a href="?page=Register">hier</a>, um Instruktionen zur Nutzung dieser Website zu erhalten. Sie sollten sich zudem über das <a href="?page=Register">Registrierungformular</a> registrieren um vollständigen Zugang zu allen Funktionen dieser Seite zu erhalten. Klicken sie <a href="?page=Login">hier</a> um sich mit einem bereits bestehendem Account einzuloggen.';
$langGlobal['portal_lang_registered_at']    = 'Registriert am ';
$langGlobal['portal_lang_profile_views']    = 'Profilaufrufe: ';
$langGlobal['portal_lang_profile_posts']    = 'Beiträge: ';
$langGlobal['portal_lang_profile_activity'] = 'Aktivität: ';
$langGlobal['portal_lang_profile_ip']       = 'IP-Adresse: ';
$langGlobal['about_you']					= 'Über Sie';
$langGlobal['account_verification_success']	= 'Ihr Benutzerkonto wurde erfolgreich verifiziert!';
$langGlobal['portal_lang_new_members']		= 'Neue Mitglieder';
$langGlobal['portal_lang_news']				= 'Neuigkeiten';
$langGlobal['portal_lang_friends']			= 'Freunde';
$langGlobal['portal_lang_functions']		= 'Funktionen';

$langGlobal['gallery_lang_welcome']			= 'Willkommen in der Galerie! Hier können Sie Bilder mit anderen Nutzern teilen, diese Bewerten, Kommentieren und vieles mehr!';

$langGlobal['string_informations'] 			= 'Informationen';

$langGlobal['no_thread_display']			= 'Es stehen keine Themen zur Anzeige zur Verfügung.';
$langGlobal['most_recent_replies']			= 'Die neuesten beiträge';
$langGlobal['thread']						= 'Thema';
$langGlobal['thread_rating']				= 'Bewertung';
$langGlobal['thread_replies']				= 'Antworten';
$langGlobal['thread_views']					= 'Aufrufe';
$langGlobal['thread_last_reply']			= 'Letzte Antwort';

$langGlobal['reply']				= 'Antworten';

$langGlobal['gallery_uploaded_by']	= 'von';

$langGlobal['notes']			= 'Benachrichtigungen';
$langGlobal['notes_posted']			= ' hat etwas gepostet: ';
$langGlobal['notes_pm']				= ' hat dir eine Nachricht geschickt';
$langGlobal['notes_gallery']		= ' hat etwas in der Galerie hochgeladen';
$langGlobal['notes_profile_view']	= ' hat sich dein Profil angeschaut';

$langGlobal['portal']  			= 'Portal';
$langGlobal['forum']   			= 'Forum';
$langGlobal['members'] 			= 'Mitglieder';
$langGlobal['gallery_string'] 	= 'Galerie';
$langGlobal['wiki']    			= 'Wiki';

$langGlobal['privacyPolicy'] = 'Datenschutzbestimmungen';
$langGlobal['termsofuse']    = 'Nutzungsbestimmungen';
$langGlobal['contact']       = 'Kontakt';
?>