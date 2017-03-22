<?php
	
	$html = '';
	
	$html .= '
	<div class="home_column">
		<h1 class="fancy_font">How to connect</h1>
			<div class="howtoconnect">
				<span>
					Via IP verbinden: <a href="steam://open/servers">Anzeige -> Server</a> -> Favoriten -> Server Hinzufügen -> 176.57.171.3:27016
				</span>
				<br>
				<span>
					<a href="steam://connect/176.57.171.3:27016">Oder verbindet euch direkt mit dem Server</a>
				</span>
			</div>
	</div>
	
	<div class="home_column">
		<h1 class="fancy_font">Server Status</h1>
			<div class="serverstatus">
				<a href="http://arkservers.net/server/176.57.171.3:27016" target="_blank"><img src="http://arkservers.net/banner/176.57.171.3:27016/banner.png" alt=""></a>
			</div>
	</div>
	
	<div class="home_column">
		<h1 class="fancy_font">TS Status</h1>
			<div class="teamspeak_status clearfix">
				<span id="its559481"><a href="http://www.teamspeak3.com/">teamspeak</a> Hosting by TeamSpeak3.com</span><script type="text/javascript" src="http://view.light-speed.com/teamspeak3.php?IP=ts56.nitrado.net&PORT=12700&QUERY=10011&UID=559481&display=block&font=11px&background=transparent&server_info_background=transparent&server_info_text=%230c6666&server_name_background=transparent&server_name_text=%2300ccff&info_background=transparent&channel_background=transparent&channel_text=%23d99329&username_background=transparent&username_text=%23e6e612"></script>
			</div>
	</div>
	
	<div class="home_column">
		<h1 class="fancy_font">Spenden</h1>
		<div class="pp_donate">
			<p>
				Auch wir haben Laufzeitgebühren für unseren Service. Daher freuen wir uns über jegliche Spenden per PayPal!
			</p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_donations">
				<input type="hidden" name="business" value="Alexander_Bretzke@gmx.de">
				<input type="hidden" name="lc" value="DE">
				<input type="hidden" name="item_name" value="Jurassic-ARK">
				<input type="hidden" name="no_note" value="0">
				<input type="hidden" name="currency_code" value="EUR">
				<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
				<input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
				<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
	</div>';
	
	echo $html;
?>