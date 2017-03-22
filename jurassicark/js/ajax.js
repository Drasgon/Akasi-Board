var title, matches, pageTitle, searchIn;

function disableObject(id)
{
	$(id).attr("disabled", "true");
}

function enableObject(id)
{
	$(id).removeAttr("disabled");
}

function updateTitle(ajax, data)
{
	if(typeof ajax == true) searchIn = "#pageContent_inner";
	else if(typeof ajax != true && typeof ajax === 'undefined')
		searchIn = "head";
	if(typeof data === 'undefined') data = $(searchIn).html();
	
	matches = data.match(/<title>(.*?)<\/title>/);
	pageTitle = matches[1];
	document.title = pageTitle;
}

//util function to check if an element has a scrollbar present
jQuery.fn.hasScrollBar = function(direction)
{
  if (direction == 'vertical')
  {
    return this.get(0).scrollHeight > this.innerHeight();
  }
  else if (direction == 'horizontal')
  {
    return this.get(0).scrollWidth > this.innerWidth();
  }
  return false;
 
}

var is_in_progress = false;

$(document).ready(function(){

	function fadeSystemMessage(type)
	{
		if(type == "fadeIn")
			$(".message_overlay").hide().fadeIn("slow");
		if(type == "fadeOut")
			$(".message_overlay").fadeOut("slow", function() {$(this).hide().remove()});
	}

	function systemMessage(msg)
	{
		$(".page").prepend('<div class="message_overlay"><div><span>'+msg+'</span></div></div>');
		fadeSystemMessage("fadeIn");
	}
	
	// Hide modal if user clicks right next to it
	$(document).click(function (e) {
		 var $tgt = $(e.target);
		if (!$tgt.closest($(".message_overlay div")).length) {
			fadeSystemMessage("fadeOut");
		}   

	});

	updateTitle();
    $('body').fadeIn(700);
	
	disableObject('.writePost_submit');
	
/*
 *
 *	Ajax page loader BEGIN
 *
*/

var container = $(".main_content");
var loader_src = '<div id="loader"></div>';
var xhr, attrHref;
var actual_content;
var minPostLength = 10;



	function changePage(data, brwsrBtns)
	{
	
	if(typeof xhr  !== "undefined") xhr.abort();
	if(typeof brwsrBtns  === "undefined") brwsrBtns = false;
	
		// Strip unneccessary Characters
		attrHref = data.replace('/?', '');
		data = attrHref.replace("?", "");
		
		console.log(attrHref);
		
		if(!is_in_progress)
		{
		
			  xhr = $.ajax({
					url: "ajax.php",
					type: "GET",
					data: data + "&AJAX_CALL=1",
					cache: false,
					beforeSend: function() {
						is_in_progress = true;
						actual_content = container.html();
						
						// If new URL does not equal to the old one AND the browser back and forward buttons weren't used
						if(attrHref != window.location && brwsrBtns == false)
						{
								
								if (attrHref.indexOf('#') != -1) {
									attrHref = attrHref.replace("#", '');
									attrHref = attrHref + "#"
								}
							
									window.history.pushState({path:attrHref},'',attrHref);
								
								
						}
					},
					success: function(html) {

							title = $(container).html(html).find("#siteTitle").attr("value");
						updateTitle(true, html);
						is_in_progress = false;

					},
					statusCode: {
						500:function() {
							alert("Something went wrong");
						}
					},
					error: function(errorData) {
								title = $(container).html(actual_content).hide().fadeIn("fast").find("#siteTitle").attr("value");
						updateTitle(true, actual_content);
						is_in_progress = false;
						container.html(actual_content);
					}
					
				});
		
		}
		
	}
	
	$('body').on('click', 'a, #linkElem', function(e) {
		e.preventDefault();
		
		changePage($(this).attr("href"));
	});
	


var lastURL;

// On URL change
$(window).bind('popstate', function() {
	// If new URL does not equal to the old one
	if(lastURL != window.location["search"])
	{
		// Request the page, that belongs to the URL
		// @replace: strip questionmarks, for a working function
		changePage(window.location["search"].replace("?", ""), true);
	}
	// Update old URL
	lastURL = window.location["search"];
});

});

/*
 *
 *	Ajax page loader END
 *
*/