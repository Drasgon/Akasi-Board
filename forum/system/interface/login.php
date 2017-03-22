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

// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
    echo '<meta http-equiv="refresh" content="0;url=/">';
} else {
    

    $login_page_container = '';
    

        
        $login_page_container .= '

<div class="mainHeadline">
   <div class="headlineContainer">
      <h2>
         <b>Anmelden</b>
      </h2>
   </div>
</div>';
        
			
            echo '<p class="error loginError_external">';

        	if (isset($_GET['action']) && $_GET['action'] == 'formsubmit')
				$main->useFile('./system/controller/sessions/login.php');

        $login_page_container .= '</p>
		
<form class="registerMain login_page no-smoothstate" method="POST" action="?page=Login&action=formsubmit">
  <div class="login_container">
   <div class="Container_reg">
   <div class="login_input_con">
      <div class="formField_label">
         <label for="username">
         Benutzername
         </label>
      </div>
      <div class="reg_containerInput">
         <input type="text" name="username" value="" id="username" class="registerInputField">
      </div>
	</div>
	<div class="login_input_con">
      <div class="formField_label">
         <label for="password">
         Passwort
         </label>
      </div>
      <div class="reg_containerInput">
         <input type="password" name="password" value="" id="password" class="registerInputField">
      </div>
	</div>
   </div>
   <section>
      <label class="staylogin_status">
         <div class="checkboxThree">
				<input type="checkbox" id="stay_logged_in" name="StayLoggedIn">
			<label for="stay_logged_in">
				Dauerhaft angemeldet bleiben
			</label>
      </div>
      
      </label>
   </section>
   <div class="submitPreForm">
      <input type="submit" name="accept" value="Absenden">
      <input type="reset" value="Zurücksetzen">
   </div>
  </div>
</form>';
       
    
    if (isset($login_data) && $login_data['successStatus'] == true) {
        require('./system/interface/successpage_login.php');
        $success_msg = '<div class="login_success_frame">';
        $success_msg .= throwSuccess_login("Sie wurden erfolgreich eingeloggt!", "?page=Index");
        $success_msg .= '</div>';
        
        echo $success_msg;
    }
    
    echo $login_page_container;
    
}

?>