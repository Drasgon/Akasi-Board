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
*/

/*
	Process the actual URI parameter to include the 
	correct files, for building the rest of the page
*/

include('page_title.php');
include('breadcrumb_processor.php');

if(isset($_GET['page']) && !empty($_GET['page']))
{
	switch($_GET['page'])
	{
		/*  <---- Portal ---->  */
		case 'index':
			include('includes/page/home.php');
		break;
		
		
		/*  <---- Members ---->  */
		case 'members':
			include('includes/page/members.php');
		break;

		
		/*  <---- Calendar ---->  */
		case 'calendar':
			include('includes/page/calendar.php');
		break;
		
		
		/*  <---- Forum ---->  */
			// The forum is accessed via link to another directory, so just abort and display the portal.
			//include('includes/modules/wip_page.php');
		
		
		/*  <---- Aboutus ---->  */
		case 'aboutus':
			include('includes/page/aboutus.php');
		break;
		
		
		/*  <---- Contact ---->  */
		case 'contact':
			include('includes/page/contact.php');
		break;
		
		/*  <---- Boos Calculator ---->  */
		case 'bosscalc':
			include('includes/page/bosshealthcalc.php');
		break;
		
		
		/*  <---- Portal as default ---->  */
		default:
			include('includes/page/home.php');
		break;
	}
}
else
	include('includes/page/home.php');
?>