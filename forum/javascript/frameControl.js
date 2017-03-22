/*
*
Copyright (c) 2015, Alexander Bretzke

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*

This "controller" is supposed to control different modules
in the "Akasi Board" software.
You won't use it for other projects or simply without permission
of the author.
*/
console.log("Cookies Enabled: " + navigator.cookieEnabled);

var functions = [
	"logoutFrame",
	"loginFrame",
	"loginFrameDrag",
	"clearAllChatTimers",
	"registerVariousEvents",
	"chatMsgField",
	"imageZoom",
	"boardCategoryToggle",
	"clearThreadSaveInterval",
	"clearPostSaveInterval",
	"galleryPageSwitch",
	"galleryInformationSelect",
	"fixDiv"
];

function iterateFuncs()
{
	for(var value of functions)
	{
		if($.isFunction(window[value]))
		{
			// console.log("Calling: " + value);
			window[value]();
		}
	}
}

$(document).ready(function() {
	iterateFuncs();
	
	// login();
});


  function fixDiv() {
	  console.log("FIX");
	$(window).on('scroll', function () {
			
		
		var $cache = $('.infoline_header');
		if ($(window).scrollTop() > 365)
		{
			$('.infoline_header').addClass("infoline_header_fixed");
			
			  $cache.css({
				'position': 'fixed',
				'top': '0px'
			  });
		}
		else
		{
			$('.infoline_header').removeClass("infoline_header_fixed");
			
			  $cache.css({
				'position': 'absolute',
				'top': '260px'
			  });
			  
			  /*$('.infoline_header').animate({
					background: "rgba(167, 160, 33, 0.49)"
				});*/
		}
	});
  }

function logoutFrame()
{
		var b = $("#LogoutOverlay"),
			a = $("#logoutPanel");
		$(".logout_servicepanel a:first-child").click(function(c) {
			b.fadeIn(500);
			a.fadeIn(500);
			$("html, body").css({
				overflow: "show",
				height: "100%"
			})
		});
		$(".close").click(function(a) {
			b.fadeOut(300)
		});
		$(document).mouseup(function(c) {
			a.is(c.target) || 0 !== a.has(c.target).length || (b.fadeOut(300), $("html, body").css({
				overflow: "",
				height: ""
			}))
		});
		$(document).on("keydown", function(a) {
			27 === a.keyCode && (b.fadeOut(300), $("html, body").css({
					overflow: "",
					height: ""
				}),
				$(".image_fullscreen").fadeOut("fast", function() {
					$(this).hide().remove()
				}))
		})
}

var top_element = $(".infoline_header");
if ("" != top_element) var offset_top = top_element.offset().top,
    top_element_height = top_element.outerHeight(!0),
    filler = '<div id="replace_filler"></div>';
jQuery.fn.animateAuto = function(b, a, c) {
    var d, e, f;
    return this.each(function(h, g) {
        g = jQuery(g);
        d = g.clone().css({
            height: "auto",
            width: "auto"
        }).appendTo("body");
        e = d.css("height");
        f = d.css("width");
        d.remove();
        "height" === b ? g.animate({
            height: e
        }, a, c) : "width" === b ? g.animate({
            width: f
        }, a, c) : "both" === b && g.animate({
            width: f,
            height: e
        }, a, c)
    })
};

function scrollToAnchor(aid){
    var aTag = $("a[name='"+ aid +"']");
    $('html,body').animate({scrollTop: aTag.offset().top},'slow');
}

function readURL(b, a) {
    if (b.files && b.files[0]) {
        var c = new FileReader;
        c.onload = function(c) {
            a.attr("src", c.target.result)
        };
        c.readAsDataURL(b.files[0])
    }
}

function chatMsgField()
{
	// $(".chat_msgField").niceScroll();
}

function loginFrame() {
	
    var b = 0;
    var a = 0;
console.log('Framecall');
          $(document).on("click", ".infoline_userOptions li:first-child a", function(c) {
			console.log('FrameEvent');
            c.stopPropagation();
            c.preventDefault();
            b++;
            1 === b ? a = setTimeout(function() {
				console.log($(".user_panel"));
                $(".user_panel").stop(!0).fadeToggle(300, function() {
                    $(this).trigger("mouseup")
                });
                b = 0
            }, 600) : (clearTimeout(a), document.location.href = $(this).attr("href"), b = 0)
        }).on("dblclick", function(a) {
            a.preventDefault()
        })

    "loginFrame" == document.URL.split("#")[1] &&
        $(".user_panel").show();
		
	$(document).on('click', '.add_post_btn', function(a){
		$('.post-addContainer-fast').slideDown(500);
		scrollToAnchor('replyAdd');
	});
}

function loginFrameDrag()
{	
	var top_pos = 1 * readCookie("akb_loginpanel_y");
	var left_pos = 1 * readCookie("akb_loginpanel_x");
	
	if(top_pos === 0)
		top_pos = top_pos + "!important";
	if(left_pos === 0)
		left_pos = left_pos + "!important";
	
    $("#form").css({
        top: top_pos,
        left: left_pos
    }).draggable({
        stop: function(b, a) {
            createCookie("akb_loginpanel_x", a.position.left, 100);
            createCookie("akb_loginpanel_y", a.position.top, 100)
        }
    }, {
        scroll: !1
    }, {
        appendTo: "body"
    }, {
        containment: "window"
    })
}
$(function() {
    $(".uploadForm").draggable({
        scroll: !1,
        handle: ".uploadForm_header",
        grid: [5, 5]
    }, {
        appendTo: ".main"
    }, {
        containment: ".main"
    })
});

function randomNumber(min, max)
{
	return Math.floor(Math.random()*(max-min+1)+min);
}

function imageZoom()
{
	$('.img-zoom').on({
	  mouseenter: function () {
		  console.log("mouseenter");
		$(this).css(
		  'transform', 'scale(1.2) rotateZ('+ randomNumber(-3, 4) +'deg)'
		);
	  },
	  mouseout: function () {
		  console.log("mouseout");
		$(this).css(
		  'transform', 'scale(1) rotateZ(0deg)'
		);
	  }
	});
}

function registerVariousEvents()
{	
    var b;
    $(".userFrame").hover(function() {
        $(".userFrame_attached").stop().fadeOut(300, function() {
            $(this).remove()
        });
        var a = $(this).offset(),
            c = $(this),
            d = $(this).offset().left,
            e = $(window).width() - (c.offset().left + c.outerWidth()),
            f = c.outerWidth();
        $(window).width();
        secondID = $(this).attr("id");
        b || (b = window.setTimeout(function() {
            b = null;
            0 == $("#userFrame_attached_" + secondID).length && (res = "loadData=" + secondID, $.ajax({
                type: "POST",
                data: res,
                url: "./system/controller/ajax/user_information.php?ajaxSend=getuserinfo"
            }).done(function(b) {
                $(".portal_base").append(b);
                b = $(".userFrame_attached");
                elementPos = d < e ? d + f + 10 : d - b.outerWidth() - 10;
                $(".userFrame_attached").fadeIn("slow").offset({
                    top: a.top,
                    left: elementPos
                });
				imageZoom();
            }).fail(function(a) {}))
        }, 600))
    }, function() {
        b ? (window.clearTimeout(b), b = null) : $(".userFrame_attached").mouseenter(function() {
            $(".userFrame_attached").stop().fadeIn()
        }).mouseleave(function() {
            $(".userFrame_attached").delay(300).fadeOut(1E3, function() {
                $(this).remove()
            })
        })
    });
    $("#portal_userlist_display").parent().mouseenter(function() {
        $("#portal_userlist_display").stop().slideToggle(400);
        $("#portal_userlist_display").prev().stop().slideToggle(400)
    }).mouseleave(function() {
        $("#portal_userlist_display").stop().slideToggle(400);
        $("#portal_userlist_display").prev().stop().slideToggle(400)
    });
    $("#delete_saved_threads").click(function() {
        confirm("Sind Sie sicher, dass Sie ihre gespeicherten Themen l\u00f6schen m\u00f6chten?") && $.ajax({
            type: "POST",
            data: "deletecon=all",
            url: "./system/controller/processors/thread_save_processor.php?ajaxSend=deleteAllSavedThreads",
            beforeSend: function() {},
            success: function(a) {
                console.log(a);
                $("#saved_posts_container").slideToggle(500).fadeOut(1E3).remove(1E3)
            }
        })
    });
    $("#delete_saved_posts").click(function() {
        confirm("Sind Sie sicher, dass Sie ihre gespeicherten Beitr\u00e4ge l\u00f6schen m\u00f6chten?") && $.ajax({
            type: "POST",
            data: "deletecon=all",
            cache: !1,
            url: "./system/controller/processors/post_save_processor.php?ajaxSend=deleteAllSavedPosts",
            beforeSend: function() {},
            success: function(a) {
                console.log(a);
                $("#saved_posts_container").slideToggle(500).fadeOut(1E3).remove(1E3)
            }
        })
    });
    $(".quote_btn").click(function() {
        pos =
            $(this).offset();
        form = $(".quote_form");
        form_width = form.width();
        button_width = $(this).width();
        form.slideToggle(200).offset({
            top: pos.top + 30,
            left: pos.left - form_width + button_width + 20
        })
    });
    $(".MsgMainCon img, .signature img, #uploadPreview").click(function() {
        img_src = $(this).attr("src");
        $("body").append('<div class="image_fullscreen"><table style="width:100%; height:100%;"><tbody><tr><td align="center" style="vertical-align:middle"><img src="' + img_src + '" id="image_fullscreen_content"></td></tr></tbody></table></div>');
        $(".image_fullscreen").hide().fadeIn("fast");
        $(".image_fullscreen").click(function(a) {
            $(a.target).is("#image_fullscreen_content") || $(".image_fullscreen").fadeOut("fast", function() {
                $(this).hide().remove()
            })
        })
    });
    $("#submitImage").click(function() {
        $(".uploadForm").is(":visible") ? $(".uploadForm").fadeOut() : $(".uploadForm").fadeIn()
    });
    $(window).scroll(function(a) {
        /*$(this).scrollTop() > offset_top && "fixed" != top_element.css("position") && (top_element.css({
                position: "fixed",
                top: "0px"
            }), $(filler).insertBefore(top_element),
            $("#replace_filler").css("height", top_element_height));*/
        $(this).scrollTop() < offset_top && "fixed" == top_element.css("position") && (top_element.css({
            position: "static",
            top: "0px"
        }), $("#replace_filler").remove())
    });
    $("#vote").click(function() {
        $("#vote_panel").fadeToggle("fast")
    });
    setInterval(function() {
            var a = new Date,
                b = a.getHours(),
                d = a.getMinutes(),
                e = a.getSeconds(),
                f = a.getMonth() + 1;
            10 > e && (e = "0" + e);
            10 > d && (d = "0" + d);
            10 > b && (b = "0" + b);
            a = a.getDate() + ". " + f + ".  " + a.getFullYear() + " - " + b + ":" + d + ":" + e;
            $("#serverTime").html(a)
        },
        1E3)
}