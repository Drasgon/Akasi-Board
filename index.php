<?php
/*
Copyright (C) 2015  Alexander Bretzke

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
	
$totalGenerateTime = microtime(TRUE);

	echo '
		<html>
			<head>
				<meta name="Content-Type" content="text/html; charset=utf-8">
				<meta http-equiv="content-type" content="text/html; charset=utf-8">
				<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
				<meta http-equiv="Pragma" content="no-cache" />
				<meta http-equiv="Expires" content="0" />';
				
				include('includes/controller/processors/title_processor.php');
				
				$totalQueries = 0;
				$totalQueryTime = 0;
				$globalLink;
				
	echo '
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<meta http-equiv="Content-Language" content="at, de, ch">
				<meta name="language" content="deutsch, de, at, ch">
				<link defer rel="stylesheet" href="css/style.css">
				<link defer rel="stylesheet" href="css/mobile.css">
				<link rel="shortcut icon" href="favicon.ico">
				<script src="//code.jquery.com/jquery-latest.min.js"></script>
				<script src="//unslider.com/unslider.min.js"></script>
				<script src="js/isInViewport.min.js"></script>
				<script src="js/frame-control.js"></script>
			</head>';
			
	if(!isset($_COOKIE['firstVisit']) || (isset($_COOKIE['firstVisit']) && empty($_COOKIE['firstVisit'])))
	{
		echo '
			<script>
				$(document).ready(function(){
					
						var container = $(".fullscreen_overlay");
						container.show();
				
						$("#logo_video").on("ended",function(){
							  //Actions when video pause selected
							  container.fadeOut(500);
						});
				});
			</script>
			<div class="fullscreen_overlay">
				<video autoplay poster="img/bg/sv-ol.jpg" id="logo_video">
					  <source src="img/bg/video/intro_logo.mp4" type="video/mp4">
					  <source src="img/bg/video/intro_logo.webm" type="video/webm">
					Your browser does not support the video tag.
				</video>
			</div>
		';
		
		setcookie("firstVisit", time(), time() + (3600*24), "/", NULL);
	}
			
	echo '
			<body>
				<div class="background" id="background" data-stellar-background-ratio="0.2">
					<video autoplay loop poster="img/bg/sv-ol.jpg">
					  <source src="img/bg/video/sv-ol.mp4" type="video/mp4">
					  <source src="img/bg/video/sv-ol.webm" type="video/webm">
					Your browser does not support the video tag.
					</video>
				</div>';
				
				// ONLY SHOW BANNER ON HOME PAGE
				if((isset($_GET['page']) && $_GET['page'] == 'index') || !isset($_GET['page']))
				{
					echo '
						<div class="banner">
							<ul>
								<li class="banner_slider_description">
									<span>
										Genug von der Garnison?<br>
										Wir auch!
									</span>
									<video autoplay loop muted class="banner_video">
									  <source src="img/gfx/slider/1.mp4" type="video/mp4">
									  <source src="img/gfx/slider/1.webm" type="video/webm">
									</video>
								</li>
								<li class="banner_slider_description">
									<span>
										Deswegen sind wir gerne in Azeroth, oder aber auch anderen Universen unterwegs!
									</span>
									<video autoplay loop muted class="banner_video">
									  <source src="img/gfx/slider/2.mp4" type="video/mp4">
									  <source src="img/gfx/slider/2.webm" type="video/webm">
									</video>
								</li>
								<li class="banner_slider_description">
									<span>
										Vor allem dieser Platz lässt uns jedes mal vor Freude strahlen.
									</span>
									<video autoplay loop muted class="banner_video">
									  <source src="img/gfx/slider/3.mp4" type="video/mp4">
									  <source src="img/gfx/slider/3.webm" type="video/webm">
									</video>
								</li>
							</ul>
						</div>';
				}
				
				if(!isset($_COOKIE['tos']) || (isset($_COOKIE['tos']) && $_COOKIE['tos'] != '1'))
				{
					echo '
						<div class="cookie_tos">
							<div>
								Diese Seite nutzt Cookies, um ihre Browsing Erfahrung zu verbessern. Durch die weitere Nutzung dieser Seiten stimmen Sie unseren <a href="forum/?page=tos">Nutzungsbestimmungen</a> zu.<span class="close">X</span>
							</div>
						</div>';
						
					setcookie("tos", "1", time() + (3600*24), "/", NULL);
				}
				
	echo '
				<div id="loader"></div>';
					include('includes/controller/processors/page_header.php');
					include('includes/controller/processors/page_container.php');
					
	echo '
			</body>
		</html>';
		
	// End of the output buffering. Also the end of all content. Cookies can't be set after this.
	ob_end_flush();
?>