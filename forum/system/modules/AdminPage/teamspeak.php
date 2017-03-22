<?php
require_once(dirname(__FILE__).'/../../security/callstack_validation.php');

if (!isset($db) || $db == NULL)
{
	$db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
	$main = new Board($db, $connection);

if (!isset($admin) || $admin == NULL)
	$admin = new Admin($db, $connection);

if(!$main->checkSessionAccess('MOD'))
	exit();
?>

<script type="text/JavaScript">
	$( document ).ready(function() {
		
		var input;
			
		$("#serveroptions input").click(function () {
			
			if (!confirm('Sind Sie sicher?')) return false;
			
			//alert($(this).attr('id'));
			input = $(this).attr('id');
			
			$.ajax({
			  url: "./system/modules/AdminPage/processors/admin_teamspeak.php",
			  type: "POST",
			  data: "actionID=" + input
			})
			  .done(function( data ) {
				console.log(data);
			  });
		});
		
	});
</script>

<div id="serveroptions">
	<p class="warning_admin">
		WARNUNG<br>
		<br>
		Diese Optionen sind Scharf und werden in jedem Falle den Zielserver beeinflussen!<br>
		Es wird zu Vorsicht auf dieser Seite geraten!
	</p>

	<input type="submit" value="Server starten" id="1">
	<input type="submit" value="Server stoppen" id="2">
	<input type="submit" value="Server neustarten" id="3">
</div>