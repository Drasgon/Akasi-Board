$(document).ready(function() {
	var unslider = $('.banner').unslider({
					speed: 500,               //  The speed to animate each slide (in milliseconds)
					delay: 7500,              //  The delay between slide animations (in milliseconds)
					complete: function() {
						
						$(window).scroll();
						
					},  //  A function that gets called after every slide animation
					keys: true,               //  Enable keyboard (left, right) arrow shortcuts
					dots: true,               //  Display dot navigation
					fluid: true,              //  Support responsive design. May break non-responsive designs
					pause: false
				});

	$(".mobile_nav_btn").on("click", function() {
		$("#mobile_nav").fadeToggle("slow");
	});

	$(".cookie_tos .close").on("click", function() {
		$(".cookie_tos").animate({bottom:'-100px'}, {queue: false, duration: 500});
	});
});



jQuery(document).ready(function ($) {
    "use strict";
    
    /* activate pause for lightbulb video if scrolled out of viewport */
    $(window).scroll(function() {
        $('video').each(function(){
            if ($(this).is(":in-viewport( 400 )")) {
                $(this)[0].play();
				//console.log("play");
            } else {
                $(this)[0].pause();
				//console.log("pause");
            }
        });
    });
	
	setInterval(function(){
			$(window).scroll();
	}, 1E3);
});

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}



function backgroundChanged(value)
{
	// Runtime variables
	var bg_path = 'img/bg/video/';
	//var bg_tag = '<source src="'+bg_path+'__%BG__.__%BGTYPE__" type="video/__%BGTYPE__">';
	//var html = '';
	
	// BG Options
	var bgs = [
		'ark_dreamscene1',
		'ark_dreamscene2'
	];
	// BG Types
	var bg_types = [
		'mp4',
		'webm'
	];
	
	if(value >= 2)
		var video = getRandomInt(0, 1);
	else
		var video = value;
	
	console.log(video);
	
	document.getElementById("bg_mp4").src = bg_path + bgs[video] + '.' + bg_types[0];
	document.getElementById("bg_webm").src = bg_path + bgs[video] + '.' + bg_types[1];
	document.getElementById("background_video").load();
	
		// Loop through all BG Types for every BG Option
		/*for(i=0; i < bg_types.length; i++)
		{
			var repl 	=	''; // Reset placeholder
			repl	=	bg_tag.replaceAll("__%BG__", bgs[value]); // Replace placeholder with BG Option of current parent loop
			repl	=	repl.replaceAll("__%BGTYPE__", bg_types[i]); // Replace placeholder with BG Type of current loop
				
			html += repl; // Add data to output string
		}*/
		
	//$("#background_video").html = html;
		
	
	//console.log(html); // Output data in console - Dev
	$.cookie("bg_index", value); // Set cookie to remember the new value
}