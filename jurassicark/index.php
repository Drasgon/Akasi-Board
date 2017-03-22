<?php
/*
Copyright (C) 2016  Alexander Bretzke

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

// Start output buffering for working session cookies
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
    ob_start("ob_gzhandler");
else
    ob_start();

date_default_timezone_set('Europe/Berlin');
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');
	
$GLOBAL['appName'] = 'Jurassic ARK';
	
$totalGenerateTime = microtime(TRUE);

	echo '
		<html>
			<head>
				<meta name="Content-Type" content="text/html; charset=utf-8">
				<meta http-equiv="content-type" content="text/html; charset=utf-8">
				<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
				<meta http-equiv="Pragma" content="no-cache" />
				<meta http-equiv="Expires" content="3600" />';
				
				include('includes/controller/processors/title_processor.php');
				
				$totalQueries = 0;
				$totalQueryTime = 0;
				$globalLink;
				
	echo '
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<meta http-equiv="Content-Language" content="at, de, ch">
				<meta name="language" content="deutsch, de, at, ch">
				<link rel="shortcut icon" href="favicon.ico">
				<link defer rel="stylesheet" href="css/style.css">
				<link defer rel="stylesheet" href="css/mobile.css">
				<!-- Prevent page being included in iframe -->
				<script type="text/JavaScript">
					if(self != top)
						top.location.replace(self.location.href);
				</script>
				<script src="//code.jquery.com/jquery-latest.min.js"></script>
				<script async src="//unslider.com/unslider.min.js"></script>
				<script async src="js/jquery.cookie.js"></script>
				<script async src="js/isInViewport.min.js"></script>
				<script async src="js/frame-control.js"></script>
			</head>';
			

			
	echo '
			<body>
				<div class="background" id="background" data-stellar-background-ratio="0.2">
					
					<img src="img/bg/ark_bg_edit.jpg" />
				</div>';
				
				// ONLY SHOW BANNER ON HOME PAGE
				if((isset($_GET['page']) && $_GET['page'] == 'index') || !isset($_GET['page']))
				{
					echo '
						<div class="banner">
							<ul>
								<li class="banner_slider_description">
									<span>
										Viel RAWR, jetzt auch in freier Wildbahn erhältlich!
									</span>
									<img src="img/gfx/slider/img/4.jpg" />
								</li>
								<li class="banner_slider_description">
									<span>
										Die Ernährunspyramide eines Sauriers ist ziemlich lang
									</span>
									<img src="img/gfx/slider/img/2.jpg" />
								</li>
								<li class="banner_slider_description">
									<span>
										Wald?<br>WALD!<br>REDWOOD!
									</span>
									<img src="img/gfx/slider/img/3.jpg" />
								</li>
							</ul>
						</div>';
				}
				
				/*if(!isset($_COOKIE['tos']) || (isset($_COOKIE['tos']) && $_COOKIE['tos'] != '1'))
				{
					echo '
						<div class="cookie_tos">
							<div>
								Diese Seite nutzt Cookies, um ihre Browsing Erfahrung zu verbessern. Durch die weitere Nutzung dieser Seiten stimmen Sie unseren <a href="forum/?page=tos">Nutzungsbestimmungen</a> zu.<span class="close">X</span>
							</div>
						</div>';
						
					setcookie("tos", "1", time() + (3600*24), "/", NULL);
				}*/
				
	echo '
				<div id="loader"></div>';
					include('includes/controller/processors/page_header.php');
					include('includes/controller/processors/page_container.php');
					
	echo '
			</body>
			<style type="text/css" rel="stylesheet">
					@import url(https://fonts.googleapis.com/css?family=Lato:400,300italic,400italic,700);
			</style>
		</html>';
		
	// End of the output buffering. Also the end of all content. Cookies can't be set after this.
	ob_end_flush();
?>