<?php
	$locale_data = array();
			 
	//Get locales from Linux terminal command locale
	$locales = shell_exec('locale -a');
	 
	$locales = explode("n" , $locales);
	 
	foreach($locales as $c => $l)
	{
		if(strlen($l))
		{
			$parts = explode('.' , $l);
			$lc = $parts[0];
			 
			list($lcode , $ccode) = explode('_' , $lc);
			 
			$lcode = strtolower($lcode);
			 
			$language = $language_codes[$lcode];
			$country = $country_codes[$ccode];
			 
			if(strlen($language) and strlen($country))
			{
				$locale_data[$l] = "$language - $country - {$parts[1]}";
			}
		}
	}
	 
	print_r($locale_data);
?>