<?php 
# maintenance mode 
function maintain($mode = FALSE){ # $mode either equals TRUE or FALSE 
if($mode){ 
# if we are in maintenance, require all pages to go to the maintenance page 
if(filename($_SERVER['SCRIPT_FILENAME']) != 'maintenance_mode.php'){ ?>
<html>
  <head>
    <title>
      Error 503 - Internal Server Error
    </title>
	<link rel=stylesheet href="css/akb-maintenance.css" media="screen">
  </head>
  
  
  <body>
    <center>
      <div class="main">
  <div class="message">
    <h2 class="error">
      Error 503
    </h2>
    <img src="images/graphics/warning_detailed.png">
    <table>
      <tr>
        <h2 class="text">
          Diese Seite unterliegt derzeit Wartungsarbeiten.
        </h2>
        <span class="text">
          Versuchen Sie es später erneut.
        </span>
        <br>
        <br>
        <span size="2">
          Kontakt: <a href="mailto:admin@baneofthelegion.de">admin@baneofthelegion.de</a>
        </span>
      </tr>
    </div>
  </div>
  <center>
  </body>
      </html>
      <?php  exit; }
}else{ 
# if we are not in maintenance, don't allow link to maintenance page 
if(filename($_SERVER['SCRIPT_FILENAME']) == 'maintenance_mode.php'){ 

exit; 
}     
} 
} 
# Run maintenance mode 
maintain(); # Leave blank to not be in maintenance mode or use maintain(TRUE); 

# get the file name 
function filename($url){ 
$pos = strrpos($url,'/'); 
$str = substr($url,$pos+1); 
return $str; 
} 
?>