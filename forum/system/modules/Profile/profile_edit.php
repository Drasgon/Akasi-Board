<?php
	if (!isset($db) || $db == NULL)
	{
		$db = new Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);

if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
	$getData = $db->query("SELECT id, location, hobbies, about, msngr_skype, msngr_icq, sn_facebook, sn_twitter, sn_googleplus, sn_tumblr FROM $db->table_profile WHERE id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
	$data = mysqli_fetch_object($getData);
	
		(!isset($data->id) || empty($data->id)) ? $id = '' : $id = $data->id;
		(!isset($data->location) || empty($data->location)) ? $location_prof = '' : $location_prof = $data->location;
		(!isset($data->hobbies) || empty($data->hobbies)) ? $hobbies = '' : $hobbies = $data->hobbies;
		(!isset($data->about) || empty($data->about)) ? $about = '' : $about = $data->about;
		(!isset($data->msngr_skype) || empty($data->msngr_skype)) ? $msngr_skype = '' : $msngr_skype = $data->msngr_skype;
		(!isset($data->msngr_icq) || empty($data->msngr_icq)) ? $msngr_icq = '' : $msngr_icq = $data->msngr_icq;
		(!isset($data->sn_facebook) || empty($data->sn_facebook)) ? $sn_facebook = '' : $sn_facebook = $data->sn_facebook;
		(!isset($data->sn_twitter) || empty($data->sn_twitter)) ? $sn_twitter = '' : $sn_twitter = $data->sn_twitter;
		(!isset($data->sn_googleplus) || empty($data->sn_googleplus)) ? $sn_googleplus = '' : $sn_googleplus = $data->sn_googleplus;
		(!isset($data->sn_tumblr) || empty($data->sn_tumblr)) ? $sn_tumblr = '' : $sn_tumblr = $data->sn_tumblr;
		
		$accountData = $main->getUserdata($data->id);
		
		$character_name = $accountData['character_name'];
		$character_realm = $accountData['character_realm'];
	

	if(isset($_GET['form']) && $_GET['form']  == 'submit') {
		$main->useFile('./system/controller/processors/profile_edit_processor.php');
		$func_result = edit_profile();
	}

if(!isset($_GET['form']) || $_GET['form']  != 'submit') {
$profileEdit = '';
$profileEdit .='

<div class="mainHeadline">
  <div class="headlineContainer">
    <h2>
		<div class="icons" id="profiledit"></div>
      <b>Profil bearbeiten</b>
    </h2>
  </div></div>

<form class="profileEdit_main" method="POST" action="?page=Profile&Tab=Edit&form=submit">
	<div class="Container_reg">
      <div class="formField_label">
        <label for="character_name">
        Charaktername
        </label>
      </div>
      <div class="reg_containerInput">
        <input type="text" name="character_name" value="'.$character_name.'" id="character_name" class="registerInputField" autocomplete="off" placeholder="Charaktername">
      </div>	  
    </div>
	
	<div class="Container_reg">
      <div class="formField_label">
        <label for="character_realm">
        Charakter Realm
        </label>
      </div>
      <div class="reg_containerInput">
        <input type="text" name="character_realm" value="'.$character_realm.'" id="character_realm" class="registerInputField" autocomplete="off" placeholder="Charakter Realm">
      </div>	  
    </div>
  <div class="Container_reg">
    <div class="formField_label">
      <label for="location">
      Wohnort
      </label>
    </div>
    <div class="reg_containerInput">
      <input type="text" name="location" value="'.$location_prof.'" id="location" class="registerInputField" placeholder="Wohnort">
    </div>
  </div>
  <div class="Container_reg">
    <div class="formField_label">
      <label for="hobbies">
      Hobbys
      </label>
    </div>
    <div class="reg_containerInput">
      <input type="text" name="hobbies" value="'.$hobbies.'" id="hobbies" class="registerInputField" placeholder="Hobbies">
    </div>
  </div>
    <div class="Container_reg">
    <div class="formField_label">
      <label for="about">
      Über mich
      </label>
    </div>
    <div class="reg_containerInput">
      <textarea type="hidden" name="about" id="about" class="registerInputField">
	  '.$about.'
	  </textarea>
    </div>
  </div>
  
  
  <fieldset class="registerFielset">
    <legend>Messenger</legend>
    <div class="Container_reg">
	<div class="formField_label">
        <label for="msngr_skype">
        Skype
        </label>
      </div>
      <div class="reg_containerInput">
        <input type="text" name="msngr_skype" value="'.$msngr_skype.'" id="msngr_skype" class="registerInputField" autocomplete="off" placeholder="Skype">
      </div>
    </div>
    <div class="Container_reg">
      <div class="formField_label">
        <label for="msngr_icq">
        ICQ
        </label>
      </div>
      <div class="reg_containerInput">
        <input type="text" name="msngr_icq" value="'.$msngr_icq.'" id="msngr_icq" class="registerInputField" autocomplete="off" placeholder="ICQ">
      </div>
    </div>
	
  </fieldset>
  <fieldset class="registerFielset">
    <legend>Soziale Netzwerke</legend>
	
    <div class="Container_reg">
      <div class="formField_label">
        <label for="sn_facebook">
        Facebook
        </label>
      </div>
      <div class="reg_containerInput">
        <input type="text" name="sn_facebook" value="'.$sn_facebook.'" id="sn_facebook" class="registerInputField" autocomplete="off" placeholder="Facebook">
      </div>
    </div>
	
    <div class="Container_reg">
      <div class="formField_label">
        <label for="sn_twitter">
        Twitter
        </label>
      </div>
      <div class="reg_containerInput">
        <input type="text" name="sn_twitter" value="'.$sn_twitter.'" id="sn_twitter" class="registerInputField" autocomplete="off" placeholder="Twitter">
      </div>	  
    </div>
	
    <div class="Container_reg">
      <div class="formField_label">
        <label for="sn_googleplus">
        Google+
        </label>
      </div>
      <div class="reg_containerInput">
        <input type="text" name="sn_googleplus" value="'.$sn_googleplus.'" id="sn_googleplus" class="registerInputField" autocomplete="off" placeholder="Google+">
      </div>	  
    </div>
	
    <div class="Container_reg">
      <div class="formField_label">
        <label for="sn_tumblr">
        Tumblr
        </label>
      </div>
      <div class="reg_containerInput">
        <input type="text" name="sn_tumblr" value="'.$sn_tumblr.'" id="sn_tumblr" class="registerInputField" autocomplete="off" placeholder="Tumblr">
      </div>	  
    </div>
  </fieldset>

  <div class="submitPreForm">
    <input type="submit" name="submit" value="Absenden">
    <input type="reset" value="Zurücksetzen">
  </div>
</form>';

echo $profileEdit;
} else {

if(!$func_result) {

require('./system/interface/errorpage_cc.php');
$errorMsg = 'Es ist ein interner Serverfehler beim Aktualisieren der Profildaten aufgetreten. Bitte versuchen Sie es später erneut.<br>Sollte das Problem bestehen bleiben, kontaktieren Sie bitte die Administration.';
throwError($errorMsg);

} else {

include('./system/interface/successpage.php');
$changeSuccess = 'Ihr Profil wurde erfolgreich bearbeitet.';
throwSuccess($changeSuccess);

}

}
} else {
require('./system/interface/errorpage_cc.php');
$errorMsg = 'Sie verfügen nicht übr die erforderlichen Zugriffsrechte um diese Seite zu besuchen.';
throwError($errorMsg);
}
?>
		<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
	    <script>tinymce.init({ 
					skin_url: "css/tinymce",
					skin: "charcoal",
					language_url : "lang/tinymce/de.js",
					language: "de",
					selector:"#about",
					plugins: [
					"autoresize advlist autolink lists link image charmap print preview hr anchor pagebreak",
					"searchreplace wordcount visualblocks visualchars code fullscreen",
					"insertdatetime media nonbreaking save table contextmenu directionality",
					"emoticons template paste textcolor colorpicker textpattern imagetools codesample"
				  ],
					toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
					  toolbar2: "print preview media | forecolor backcolor fontsizeselect emoticons | codesample",
					  image_advtab: true,
					autoresize_min_height: 350,
					autoresize_max_height: 550
					
				});
		</script>
