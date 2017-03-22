
<link rel="stylesheet" href="css/bosshealthcalc.css" />

<?php
	function nice_number($n) {
		// first strip any formatting;
		$n = (0+str_replace(",", "", $n));

		// is this a number?
		if (!is_numeric($n)) return false;

		// now filter it;
		if ($n > 1000000000000) return round(($n/1000000000000), 2).' B';
		elseif ($n > 1000000000) return round(($n/1000000000), 2).' G';
		elseif ($n > 1000000) return round(($n/1000000), 2).' M';
		elseif ($n > 1000) return round(($n/1000), 2).' K';

		return number_format($n);
	}
	
	$boss_skorpyron = ARRAY(
		"NAME"				=> "Skorpyron",
		
		// Confirmed
		"BASE_NHC" 			=> 657000000,
		"PERPLAYER_NHC" 	=> 68000000,
		"ENRAGE_NHC" 		=> 540,
		"SAFE_KILL_NHC" 	=> 500,
		
		// NOT Confirmed
		"BASE_HC" 			=> 909000000, 	
		"PERPLAYER_HC" 		=> 100000000, 	
		"ENRAGE_HC" 		=> 480, 		
		"SAFE_KILL_HC" 		=> 400, 
		
		// NOT Confirmed
		"BASE_MYTHIC" 		=> 909000000,
		"PERPLAYER_MYTHIC" 	=> 100000000,
		"ENRAGE_MYTHIC" 	=> 480,
		"SAFE_KILL_MYTHIC" 	=> 400,
		
		"IMG" 				=> "http://wow.zamimg.com/uploads/screenshots/normal/597873-skorpyron.jpg");
	$boss_anomaly = ARRAY();
	$boss_trilliax = ARRAY();
	$boss_aluriel = ARRAY(
		"NAME"				=> "Aluriel die Zauberklinge",
		
		// Confirmed
		"BASE_NHC" 			=> 814000000,
		"PERPLAYER_NHC" 	=> 86000000,
		"ENRAGE_NHC" 		=> 645,
		"SAFE_KILL_NHC" 	=> 600,
		
		// NOT Confirmed
		"BASE_HC" 			=> 1030000000,
		"PERPLAYER_HC" 		=> 100000000,
		"ENRAGE_HC" 		=> 490,
		"SAFE_KILL_HC" 		=> 450,
		
		// NOT Confirmed
		"BASE_MYTHIC" 		=> 3220000000,
		"PERPLAYER_MYTHIC" 	=> 0,
		"ENRAGE_MYTHIC" 	=> 450,
		"SAFE_KILL_MYTHIC" 	=> 400,
		
		"IMG" 				=> "http://wow.zamimg.com/uploads/screenshots/normal/601539-spellblade-aluriel.jpg");
	$boss_tichondrius = ARRAY(
		"NAME"				=> "Tichondrius",
		
		// NOT Confirmed
		"BASE_NHC" 			=> 808100000,
		"PERPLAYER_NHC" 	=> 100000000,
		"ENRAGE_NHC" 		=> 463,
		"SAFE_KILL_NHC" 	=> 410,
		
		// NOT Confirmed
		"BASE_HC" 			=> 909000000,
		"PERPLAYER_HC" 		=> 100000000,
		"ENRAGE_HC" 		=> 463,
		"SAFE_KILL_HC" 		=> 410,
		
		// NOT Confirmed
		"BASE_MYTHIC" 		=> 909000000,
		"PERPLAYER_MYTHIC" 	=> 100000000,
		"ENRAGE_MYTHIC" 	=> 463,
		"SAFE_KILL_MYTHIC" 	=> 410,
		
		"IMG" 				=> "http://wow.zamimg.com/uploads/screenshots/normal/608843-tichondrius.jpg"
	);
	$boss_krosus = ARRAY(
		"NAME"				=> "Krosus",
		
		// NOT Confirmed
		"BASE_NHC" 			=> 690000000,
		"PERPLAYER_NHC" 	=> 72000000,
		"ENRAGE_NHC" 		=> 360,
		"SAFE_KILL_NHC" 	=> 310,
		
		// NOT Confirmed
		"BASE_HC" 			=> 950000000,
		"PERPLAYER_HC" 		=> 100000000,
		"ENRAGE_HC" 		=> 360,
		"SAFE_KILL_HC" 		=> 310,
		
		// NOT Confirmed
		"BASE_MYTHIC" 		=> 3450000000,
		"PERPLAYER_MYTHIC" 	=> 0,
		"ENRAGE_MYTHIC" 	=> 360,
		"SAFE_KILL_MYTHIC" 	=> 310,
		
		"IMG" 				=> "http://wow.zamimg.com/uploads/screenshots/normal/603378-krosus.jpg");
	$boss_botanic = ARRAY();
	$boss_augur = ARRAY();
	$boss_elisande = ARRAY();
	$boss_guldan = ARRAY();
	
	
	echo '
		<ul class="bossSelect">
			<a href="?page=bosscalc&bossID=1&bossMode=NHC">
				<li>
					<span class="bossName">Skorpyron</span><br>
					<img src="http://wow.zamimg.com/uploads/screenshots/normal/597873-skorpyron.jpg">
				</li>
			</a>
			<a href="?page=bosscalc&bossID=2&bossMode=NHC">
				<li>
					<span class="bossName">Chronomatische Anomalie</span><br>
					<img src="http://wow.zamimg.com/uploads/screenshots/normal/601747-chronomatische-anomalie.jpg">
				</li>
			</a>
			<a href="?page=bosscalc&bossID=3&bossMode=NHC">
				<li>
					<span class="bossName">Trilliax</span><br>
					<img src="http://wow.zamimg.com/uploads/screenshots/normal/503115-trilliax.jpg">
				</li>
			</a>
			<a href="?page=bosscalc&bossID=4&bossMode=NHC">
				<li>
					<span class="bossName">Aluriel die Zauberklinge</span><br>
					<img src="http://wow.zamimg.com/uploads/screenshots/normal/601539-spellblade-aluriel.jpg">
				</li>
			</a>
			<a href="?page=bosscalc&bossID=5&bossMode=NHC">
				<li>
					<span class="bossName">Tichondrius</span><br>
					<img src="'.$boss_tichondrius["IMG"].'">
				</li>
			</a>
			<a href="?page=bosscalc&bossID=6&bossMode=NHC">
				<li>
					<span class="bossName">Krosus</span><br>
					<img src="http://wow.zamimg.com/uploads/screenshots/normal/603378-krosus.jpg">
				</li>
			</a>
			<a href="?page=bosscalc&bossID=7&bossMode=NHC">
				<li>
					<span class="bossName">Hochbotaniker Tel\'arn</span><br>
					<img src="http://wow.zamimg.com/uploads/screenshots/normal/603266-hochbotaniker-telarn.jpg">
				</li>
			</a>
			<a href="?page=bosscalc&bossID=8&bossMode=NHC">
				<li>
					<span class="bossName">Sternendeuter Etraeus</span><br>
					<img src="http://wow.zamimg.com/uploads/screenshots/normal/597865-sterndeuter-etraeus.jpg">
				</li>
			</a>
			<a href="?page=bosscalc&bossID=9&bossMode=NHC">
				<li>
					<span class="bossName">Großmagistrix Elisande</span><br>
					<img src="http://wow.zamimg.com/uploads/screenshots/normal/568787-elisande.jpg">
				</li>
			</a>
			<a href="?page=bosscalc&bossID=10&bossMode=NHC">
				<li>
					<span class="bossName">Gul\'dan</span><br>
					<img src="http://wow.zamimg.com/uploads/screenshots/normal/604565-guldan.jpg">
				</li>
			</a>
		</ul>
	';

	
	$active_boss = ARRAY();
	$mode = "NHC";
	
	if(isset($_GET["bossID"]) && !empty($_GET["bossID"]))
	{
		switch($_GET["bossID"])
		{
			case 1:
				$active_boss = $boss_skorpyron;
			break;
			case 2:
				$active_boss = $boss_anomaly;
			break;
			case 3:
				$active_boss = $boss_trilliax;
			break;
			case 4:
				$active_boss = $boss_aluriel;
			break;
			case 5:
				$active_boss = $boss_tichondrius;
			break;
			case 6:
				$active_boss = $boss_krosus;
			break;
			case 7:
				$active_boss = $boss_botanic;
			break;
			case 8:
				$active_boss = $boss_augur;
			break;
			case 9:
				$active_boss = $boss_elisande;
			break;
			case 10:
				$active_boss = $boss_guldan;
			break;
		}
	}
	else
	{
		echo 'Kein Boss ausgewählt!';
		return;
	}
	
	if(empty($active_boss))
	{
		echo 'Für diesen Boss liegen keine Daten vor!';
		return;
	}
	
	if(isset($_GET["bossMode"]) && ($_GET["bossMode"] == "NHC" || $_GET["bossMode"] == "HC" || $_GET["bossMode"] == "MYTHIC"))
		$mode = $_GET["bossMode"];
	
	$recommendedDps = 1.25; // +25%
	
	echo '
	<div class="bosshealth">
		<h2>'.$active_boss["NAME"].' '.$mode.'</h2><img src="'.$active_boss["IMG"].'">
			<p>Basis HP: '.nice_number($active_boss["BASE" . "_" .$mode]).'</p>
			<p>HP pro Spieler (10+): ~'.nice_number($active_boss["PERPLAYER" . "_" .$mode]).'</p>
			<p>Enrage Timer: '.$active_boss["ENRAGE" . "_" .$mode].' Sekunden</p>
			<p>Safe Kill: '.$active_boss["SAFE_KILL" . "_" .$mode].' Sekunden</p>
			<ul class="bossSelect">
				<a href="?page=bosscalc&bossID='.$_GET["bossID"].'&bossMode=NHC">
					<li>
						<span class="bossName">Normal</span>
					</li>
				</a>
				<a href="?page=bosscalc&bossID='.$_GET["bossID"].'&bossMode=HC">
					<li>
						<span class="bossName">Heroisch</span>
					</li>
				</a>
				<a href="?page=bosscalc&bossID='.$_GET["bossID"].'&bossMode=MYTHIC">
					<li>
						<span class="bossName">Mythisch</span>
					</li>
				</a>
			</ul>
			<table><thead>
				<tr>
					<th>Spieler</th>
					<th>Boss Lebenspunkte</th>
					<th>Ø Raid DPS benötigt</th>
					<th>Anzahl DDs</th>
					<th>Ø Mindest-DPS pro DD</th>
					<th>Ø Empfohlener DPS pro DD (+25%)</th>
				</tr>
			</thead>
			<tbody>';
		
		if($mode != "MYTHIC")
		{
			for($i = 0; $i <= 20; $i++)
			{
				$num_dd = round($i / 1.15) + 6;
				$result = $active_boss["BASE" . "_" .$mode] + ($active_boss["PERPLAYER" . "_" .$mode] * $i);
				$players = 10 + $i;
				echo '	<tr class="playerList">
							<td>'.$players.'</td>
							<td>'.nice_number($result).'</td>
							<td>'.nice_number($result / $active_boss["ENRAGE" . "_" .$mode]).'</td>
							<td>'.$num_dd.'</td>
							<td><span class="dps"> '.nice_number(($result / $active_boss["ENRAGE" . "_" .$mode]) / $num_dd).'</span></td>
							<td><span class="dps"> '.nice_number($recommendedDps * ($result / $active_boss["ENRAGE" . "_" .$mode]) / $num_dd).'</span></td>
						</tr>';
				
			}
		}
		else // If mode is Mythic
		{
			$num_dd = 13;
				$result = $active_boss["BASE" . "_" .$mode];
				$players = 20;
				echo '	<tr class="playerList">
							<td>'.$players.'</td>
							<td>'.nice_number($result).'</td>
							<td>'.nice_number($result / $active_boss["ENRAGE" . "_" .$mode]).'</td>
							<td>'.$num_dd.'</td>
							<td><span class="dps"> '.nice_number(($result / $active_boss["ENRAGE" . "_" .$mode]) / $num_dd).'</span></td>
							<td><span class="dps"> '.nice_number($recommendedDps * ($result / $active_boss["ENRAGE" . "_" .$mode]) / $num_dd).'</span></td>
						</tr>';
		}
	echo '</tbody>
		</table>
		</div>';
?>