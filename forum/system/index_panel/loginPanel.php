    <form class="user_panel" method="POST" name="form" id="form" action="?form=UserLogin#loginFrame" autocomplete="off">
      <div class="loginCon">
        <input name="username" type="text" class="quickLogin_User" title="Benutzername" placeholder="Benutzername" tabindex="1" id="username" autocomplete="off" required="">
        <input name="password" type="password" class="quickLogin_Pass" placeholder="Passwort" title="Passwort" tabindex="2" id="password" autocomplete="off" required="">
        <input type="image" src="./images/graphics/fast_login-submit.png" class="inputImage" title="Absenden" tabindex="3" id="submit">
      </div>
      <p>
      </p>
      <section>
        <label class="staylogin_status">
          <div class="checkboxThree">
            <input type="checkbox" value="0" id="checkboxThreeInput" name="Staylogin_status[]">
            <label for="checkboxThreeInput">
            </label>
          </div>
          <span>
            Dauerhaft angemeldet bleiben
          </span>
        </label>
      </section>
      <p>
      </p>
      <span id="response_failed" class="responseFailed">
        <?php
if ($_GET['form'] == UserLogin) {
$main->useFile('./system/controller/loginFunction.php');
login();
}
?>
  </span>
  <span id="response_loading" class="responseLoading">
  </span>
  <span id="response_success" class="responseSuccess">
    <div>
      <span class="LoginOptions">
        <a href="#" class="RedirectToReg" id="RedirectToReg">
          Noch keinen Account? Registrieren
        </a>
      </span>
    </div>
    
  </span>
  </form>