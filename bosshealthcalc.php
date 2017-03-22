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
	
	
	$boss_tichondrius = ARRAY(
		"BASE" => 909000000,
		"PERPLAYER" => 100000000, // HP the boss gains per additional player
		"ENRAGE" => 480,
		"SAFE_KILL" => 400
	);
	
	echo '<h2>Tichondrius HC</h2><br>
			<p>Basis HP: '.$boss_tichondrius["BASE"].'</p>
			<p>Enrage Timer: '.$boss_tichondrius["ENRAGE"].' Sekunden</p>
			<p>Safe Kill: '.$boss_tichondrius["SAFE_KILL"].' Sekunden</p>
			<p>Basis HP: '.$boss_tichondrius["BASE"].'</p>
			<ul>';
		for($i = 0; $i <= 20; $i++)
		{
			$num_dd = round($i / 1.55) + 6;
			$result = $boss_tichondrius["BASE"] + ($boss_tichondrius["PERPLAYER"] * $i);
			$players = 10 + $i;
			echo '<li class="playerList">'.$players.' Spieler: '.nice_number($result).' HP<br>
						Raid DPS benötigt: '.nice_number($result / $boss_tichondrius["ENRAGE"]).'<br>
						DPS pro Spieler bei '.$num_dd.' DDs:<span class="dps"> '.nice_number(($result / $boss_tichondrius["ENRAGE"]) / $num_dd).'</span></li><br>';
			
		}
	echo '</ul>';
?>