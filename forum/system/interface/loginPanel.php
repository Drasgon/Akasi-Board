    <form class="user_panel" method="POST" name="form" id="form" action="?form=UserLogin#loginFrame" autocomplete="off">
      <div class="loginCon">
        <input name="username" type="text" class="quickLogin_User" title="Benutzername" placeholder="Benutzername" tabindex="1" id="username" autocomplete="off" required="" maxlength="13">
        <input name="password" type="password" class="quickLogin_Pass" placeholder="Passwort" title="Passwort" tabindex="2" id="password" autocomplete="off" required="" maxlength="34">
        <input type="image" src="./images/icons/login.png" class="icons" title="Absenden" tabindex="3" id="submit">
      </div>
	  
	  
	  	<div id="response_failed" class="responseFailed">
        <?php
		if (isset($_GET['form']) && $_GET['form'] == UserLogin) {
		$main->useFile('./system/controller/sessions/login.php');
		login();
		}
		?>
		</div>
	  
	<span id="response_loading" class="responseLoading"></span>
	<span id="response_success" class="responseSuccess"></span>
	  
	  
	  
      <section>
        <label class="staylogin_status">
          <div class="checkboxThree">
            <input type="checkbox" id="checkboxThreeInput" name="StayLoggedIn">
            <label for="checkboxThreeInput">
				Dauerhaft angemeldet bleiben
            </label>
          </div>
        </label>
      </section>	  
	  

    <div class="LoginOptions">

        <a href="?page=Register" class="RedirectToReg" id="RedirectToReg">
          Noch keinen Account? Registrieren
        </a>
      
    </div>
    
  </span>
  </form>