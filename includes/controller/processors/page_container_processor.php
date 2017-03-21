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

/*  <---- Portal ---->  */
(!isset($_GET['page']) || $_GET['page'] == "index") ? 
include('includes/page/home.php') : '';
/*  <---- Portal END ---->  */



/*  <---- Board ---->  */
(isset($_GET['page']) && $_GET['page'] == "members") ? 
include('includes/page/members.php') : '';
/*  <---- Board END ---->  */



/*  <---- Thread ---->  */
(isset($_GET['page']) && $_GET['page'] == "media") ? 
include('includes/page/media.php') : '';
/*  <---- Thread END ---->  */



/*  <---- Forum ---->  */
(isset($_GET['page']) && $_GET['page'] == "forum") ? 
include('includes/modules/wip_page.php') : '';
/*  <---- Forum END ---->  */



/*  <---- Members ---->  */
(isset($_GET['page']) && $_GET['page'] == "aboutus") ? 
include('includes/page/aboutus.php') : '';
/*  <---- Members END ---->  */



/*  <---- Gallery ---->  */
(isset($_GET['page']) && $_GET['page'] == "contact") ? 
include('includes/page/contact.php') : '';
/*  <---- Gallery END ---->  */

?>