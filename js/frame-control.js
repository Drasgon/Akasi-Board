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
				console.log("play");
            } else {
                $(this)[0].pause();
				console.log("pause");
            }
        });
    });
	
	setInterval(function(){
			$(window).scroll();
	}, 1E3);
});