$(function(){
  'use strict';
  var options = {
	blacklist: '.no-smoothstate',
	scroll: false,
    prefetch: false,
	cache: false,
	debug: false,
    cacheLength: 0,
	pageCacheSize: 0,
    onStart: {
      duration: 250, // Duration of our animation
      render: function ($container) {
        // Add your CSS animation reversing class
        $container.addClass('is-exiting');

        // Restart your animation
        smoothState.restartCSSAnimations();
		
		// $('body').animate({ 'scrollTop': $(".main").offset().top - 39 });
		$('body').animate({
              scrollTop: 0
            });
      }
    },
    onReady: {
      duration: 0,
      render: function ($container, $newContent) {
        // Remove your CSS animation reversing class
        $container.removeClass('is-exiting');

        // Inject the new content
        $container.html($newContent);

      }
    },
	onAfter: function() {
		// Unfortunately, we have to put all functions and plugins in here, in order to make them work
		// since SmoothState prevents the document.ready event to fire.
		
		$(document).unbind("click");
		
		iterateFuncs();
		// login();
		
		
	}
  },
  smoothState = $('#page').smoothState(options).data('smoothState');
});