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



	English Data for Akasi Board ©
	Copyright 2014, Alexander Bretzke - All rights reserved
*/

global $langGlobal;

setlocale(LC_ALL, null);
setlocale(LC_ALL, 'en_GB@euro', 'en_GB', 'en_en');

$langGlobal['sPWelcome']         = 'Welcome';
$langGlobal['sPLogout']          = 'Logout';
$langGlobal['sPProfileEdit']     = 'Edit profile';
$langGlobal['sPMessages']        = 'Messages';
$langGlobal['sPControlCenter']   = 'Control center';
$langGlobal['sPAdmin']           = 'Administration';
$langGlobal['sPNotlogin_status'] = 'You are not logged in';
$langGlobal['sPLogin']           = 'Log In';
$langGlobal['sPRegister']        = 'Register';
$langGlobal['sPHelp']            = 'Help';

$langGlobal['data_missed'] = 'You did not provide all neccessary informations!';
$langGlobal['permission_denied'] = 'You do not have sufficient rights to access this page!';

$langGlobal['function_in_development']			= 'This function is currently under development.';

$langGlobal['max_users_reached_string_first'] = 'The number of allowed active users ( ';
$langGlobal['max_users_reached_string_sec']   = ' ) was reached or exceeded. Other logins are currently not possible. Please try again later.';
$langGlobal['username_illegal_chars']         = 'Username contains illegal characters.';
$langGlobal['invalid_pass_or_username']       = 'Invalid username or password';
$langGlobal['account_not_validated']          = 'This account did not get unlocked by the administration yet.';
$langGlobal['account_ban_temp_first']         = 'This account is suspended until ';
$langGlobal['account_ban_temp_time_first']	  = 'at ';
$langGlobal['account_ban_temp_second']        = '.';
$langGlobal['account_ban_perm']               = 'This account is suspended permanently.';

$langGlobal['ip_ban_temp']					  = 'You have been blocked for more logins due to too many failed login attempts. Please try again later.';
$langGlobal['login_critical_error']           = 'Critical login error. Please try again later. If the problem persists, please contact the Administration.';
$langGlobal['login_success']                  = 'You have been logged in successfully!';

$langGlobal['portal_lang_welcome']          = 'Willkommen, falls dies Ihr erster Besuch auf dieser Seite ist, klicken Sie <a href="?page=Register">hier</a>, um Instruktionen zur Nutzung dieser Website zu erhalten. Sie sollten sich zudem über das <a href="?page=Register">Registrierungformular</a> registrieren um vollständigen Zugang zu allen Funktionen dieser Seite zu erhalten. Klicken sie <a href="?page=Login">hier</a> um sich mit einem bereits bestehendem Account einzuloggen.';
$langGlobal['portal_lang_registered_at']    = 'Registered ';
$langGlobal['portal_lang_profile_views']    = 'Profile views: ';
$langGlobal['portal_lang_profile_posts']    = 'Posts: ';
$langGlobal['portal_lang_profile_activity'] = 'Activity: ';
$langGlobal['portal_lang_profile_ip']       = 'IP-Adress: ';
$langGlobal['about_you']					= 'About you';
$langGlobal['account_verification_success']	= 'Your account was verified successfully!';
$langGlobal['portal_lang_new_members']		= 'New members';
$langGlobal['portal_lang_news']				= 'News';
$langGlobal['portal_lang_friends']			= 'Friends';
$langGlobal['portal_lang_functions']		= 'Functions';

$langGlobal['gallery_lang_welcome']			= 'Willkommen in der Galerie! Hier können Sie Bilder mit anderen Nutzern teilen, diese Bewerten, Kommentieren und vieles mehr!';

$langGlobal['string_informations'] 			= 'Informations';

$langGlobal['no_thread_display']			= 'There are no threads available for display';
$langGlobal['most_recent_replies']			= 'The most recent replies';
$langGlobal['thread']						= 'Thread';
$langGlobal['thread_rating']				= 'Rating';
$langGlobal['thread_replies']				= 'Replies';
$langGlobal['thread_views']					= 'Views';
$langGlobal['thread_last_reply']			= 'Last reply';

$langGlobal['reply']				= 'Reply';

$langGlobal['gallery_uploaded_by']	= 'by';

$langGlobal['notes']			= 'Notes';
$langGlobal['notes_posted']			= ' has posted something in: ';
$langGlobal['notes_pm']				= ' sent you an private message';
$langGlobal['notes_gallery']		= ' uploaded something in the gallery';
$langGlobal['notes_profile_view']	= ' just viewed your profile';

$langGlobal['portal']  			= 'Portal';
$langGlobal['forum']   			= 'Forum';
$langGlobal['members'] 			= 'Members';
$langGlobal['gallery_string'] 	= 'Gallery';
$langGlobal['wiki']    			= 'Wiki';

$langGlobal['privacyPolicy'] = 'Privacy Policy';
$langGlobal['termsofuse']    = 'Terms of use';
$langGlobal['contact']       = 'Contact';
?>