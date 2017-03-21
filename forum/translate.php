<head>
<title> Rawr - Translator </title>
</head>

<body>
<div class="main" style="display: inline-flex; width:100%;">
<form name="translator" method="POST" action="/forum/translate.php" style="padding: 100px 0 0 100px;">
<textarea type="text" name="string_to_dragon" placeholder="To Dragon" style="height:100px; width: 400px;"></textarea>
  <br>
<input type="submit" value="Rawrize" style="float:right;">
</form>

<form name="translator" method="POST" action="/forum/translate.php" style="float:right; margin-left: auto; padding: 100px 100px 0 0;">
<textarea type="text" name="string_to_human" placeholder="To Human" style="height:100px; width: 400px;"></textarea><br>
<input type="submit" value="Humanize">
</form>
</div>



<center>
<div class="ausgabe_con" style="margin-top:300px;"> Output:
<div class="Ausgabe" style="width: 700px; height: 100px; border-style:solid; border-color: black; border-width:1px; word-break:break-all; background-color: #ebebeb;">
<?php 




if (isset($_POST['string_to_dragon']))	{
$string = $_POST['string_to_dragon']; 

$chars = array( 
    'a' => 'r', 
    'b' => 'rAA', 
    'c' => 'rAW', 
    'd' => 'rWA', 
    'e' => 'rA', 
    'f' => 'rWW', 
    'g' => 'raa', 
    'h' => 'raw',
	'i' => 'rw',
	'j' => 'rwa',
	'k' => 'rAa',
	'l' => 'raA',
	'm' => 'raaa',
	'n' => 'raaA',
	'o' => 'rW',
	'p' => 'raAa',
	'q' => 'rAaa',
	'r' => 'rAw',
	's' => 'rWa',
	't' => 'rWw',
	'u' => 'ra',
	'v' => 'raW',
	'w' => 'rwA',
	'x' => 'rAAA',
	'y' => 'raAA',
	'z' => 'rwW',
	
	
	'A' => 'R', 
    'B' => 'RAA', 
    'C' => 'RAW', 
    'D' => 'RWA', 
    'E' => 'RA', 
    'F' => 'RWW', 
    'G' => 'Raa',
    'H' => 'Raw', 
	'I' => 'Rw',
	'J' => 'Rwa',
	'K' => 'RAa',
	'L' => 'RaA',
	'M' => 'Raaa',
	'N' => 'RaaA',
	'O' => 'RW',
	'P' => 'RaAa',
	'Q' => 'RAaa',
	'R' => 'RAw',
	'S' => 'RWa',
	'T' => 'RWw',
	'U' => 'Ra',
	'V' => 'RaW',
	'W' => 'RwA',
	'X' => 'RAAA',
	'Y' => 'RaAA',
	'Z' => 'RwW'
); 

echo strtr($string,$chars); 
}






if (isset($_POST['string_to_human']))	{
$string = $_POST['string_to_human']; 

$chars_d = array( 
    'r' => 'a', 
    'rAA' => 'b', 
    'rAW' => 'c', 
    'rWA' => 'd', 
    'rA' => 'e', 
    'rWW' => 'f', 
    'raa' => 'g', 
    'raw' => 'h',
	'rw' => 'i',
	'rwa' => 'j',
	'rAa' => 'k',
	'raA' => 'l',
	'raaa' => 'm',
	'raaA' => 'n',
	'rW' => 'o',
	'raAa' => 'p',
	'rAaa' => 'q',
	'rAw' => 'r',
	'rWa' => 's',
	'rWw' => 't',
	'ra' => 'u',
	'raW' => 'v',
	'rwA' => 'w',
	'rAAA' => 'x',
	'raAA' => 'y',
	'rwW' => 'z',
	
	
	'R' => 'A', 
    'RAA' => 'B', 
    'RAW' => 'C', 
    'RWA' => 'D', 
    'RA' => 'E', 
    'RWW' => 'F', 
    'Raa' => 'G',
    'Raw' => 'H', 
	'Rw' => 'I',
	'Rwa' => 'J',
	'RAa' => 'K',
	'RaA' => 'L',
	'Raaa' => 'M',
	'RaaA' => 'N',
	'RW' => 'O',
	'RaAa' => 'P',
	'RAaa' => 'Q',
	'RAw' => 'R',
	'RWa' => 'S',
	'RWw' => 'T',
	'Ra' => 'Z',
	'RaW' => 'V',
	'RwA' => 'W',
	'RAAA' => 'X',
	'RaAA' => 'Y',
	'RwW' => 'Z'
);  

echo strtr($string,$chars_d);  
}
?>
</div></div></center>

</body>