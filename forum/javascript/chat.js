/*
*
Copyright (c) 2014, Alexander Bretzke

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*

This chat controller is supposed to control different modules
in the "Akasi Board" software.
You won't use it for other projects or simply without permission
of the author.
*/

var msgSend_processing = !1,
	msgPull_processing = !1,
	changeInProgress = !1,
	chatLock = !1,
	control = 0,
	pullInterval = setInterval("messagePullHandler()", 7E3),
	activeChat = "",
	private_chat = !1,
	timer, timer_const, sidebar_timer, input = "#chatMsg_add",
	submitInputColor = "#838383",
	baseInputColor = $(input).css("background-color");
	
	
	
$(document).ready(function() {
	var b = $.cookie("chat_notify_volume");
	$.ionSound({
		sounds: ["notify"],
		path: "./sounds/",
		multiPlay: !0,
		volume: b
	});
	$("#chat_notify_volume").change(function() {
		var a = $(this).val();
		$.cookie("chat_notify_volume", a)
	});
	$("#chat_notify_volume").mouseup(function() {
		var a = $.cookie("chat_notify_volume");
		$.ionSound({
			sounds: ["notify"],
			path: "./sounds/",
			multiPlay: !0,
			volume: a
		});
		$.ionSound.play("notify")
	})
});
$(".chat_msgField").bind("mousewheel DOMMouseScroll", function(b) {
	var a = b.originalEvent;
	this.scrollTop += 30 * (0 > (a.wheelDelta || -a.detail) ? 1 : -1);
	b.preventDefault()
});


function checkInput()
{
	var value = $("#chatMsg_add").val();

	if( value == '' ) {
      setObjectLock("#portal_chat input[type='submit']", true);
   } else {
	  setObjectLock("#portal_chat input[type='submit']", false);
   }
}

function ClearChat() {
	$(".chatRow").fadeOut(500).remove()
}

function animateColor(b, a, c) {
	$(c).animate({
		backgroundColor: a
	}, 200, function() {
		$(c).animate({
			backgroundColor: b
		}, 200)
	})
}

function setObjectLock(b, a) {
	$(b).attr("disabled", a)
}
if (!chatLock) {
	var privatePullHandler = function() {
			activeChat = window.activeChat;
			console.log(activeChat);
			max = $(".chatRow:last").attr("id");
			"" == max && (max = "0");
			console.log(max);
			$.ajax({
				type: "POST",
				url: "./system/controller/processors/chat_processor.php?ajaxLoad=privateMessage",
				data: "prv_id=" + activeChat + "&latestID=" + max,
				cache: !1,
				beforeSend: function() {},
				success: function(b) {
					var a = $.trim(b);
					"" != b && "" != a && ($(".chat_msgField").append(b).children(":last").hide().fadeIn(3E3), 500 >= $(".chat_msgField")[0].scrollHeight - $(".chat_msgField").scrollTop() && $(".chat_msgField").animate({
						scrollTop: $(".chat_msgField")[0].scrollHeight
					}, 1E3), b = $.cookie("chat_notify_volume"), "undefined" === typeof $.cookie("chat_notify_volume") && (b = 25), $.ionSound({
						sounds: ["notify"],
						path: "./sounds/",
						multiPlay: !0,
						volume: b
					}), $.ionSound.play("notify"));
					1 == private_chat && (timer_const = window.setTimeout(function() {
						privatePullHandler()
					}, 5E3))
				},
				error: function(b) {
					$(".chat_msgField").append(b).children(":last").hide().fadeIn(3E3)
				}
			})
		},
		messagePullHandler = function(b) {
			for (instance in CKEDITOR.instances) CKEDITOR.instances[instance].updateElement();
			max = $(".chatRow:last").attr("id");
			"" == max && (max = "0");
			emoticons = (emoticons = $("#parseEmoticons").is(":checked")) ? "true" : "false";
			$.ajax({
				type: "POST",
				data: "latestID=" + max + "&parseEmoticons=" + emoticons,
				cache: !1,
				url: "./system/controller/processors/chat_processor.php?ajaxSend=chatMessagePull",
				beforeSend: function() {
					msgPull_processing = !0
				},
				success: function(a) {
					var b = $.trim(a);
					"" != a && "" != b && (control++, "false" == a ? (chatLock = !0, setObjectLock(".chat_submit", !0), $(".portal_chat portal_container").animate({
						backgroundColor: "#CDCDCD"
					}, 200), $(input).val(""), setObjectLock(input, !0), clearInterval(pullInterval), $(".chat_msgField").append('<div class="chat_systemWarning_main"><h3 class="chat_systemWarning">   Sie m\u00fcssen sich erneut einloggen, um den Chat zu nutzen!   </h3><div class="chat_systemWarning_right"></div><div class="chat_systemWarning_left"></div></div>').animate({
						scrollTop: $(".chat_msgField")[0].scrollHeight
					}, 1E3)) : (1 >= control && ($(".chat_msgField").append(a).children(".chatRow"), $(".chat_msgField").scrollTop($(".chat_msgField")[0].scrollHeight)
					), 1 < control && (500 >= $(".chat_msgField")[0].scrollHeight - $(".chat_msgField").scrollTop() && $(".chat_msgField").animate({
						scrollTop: $(".chat_msgField")[0].scrollHeight
					}, 1E3), $(".chat_msgField").append(a).children(":last").hide().fadeIn(1E3), a = $.cookie("chat_notify_volume"), "undefined" === typeof $.cookie("chat_notify_volume") && (a = 25), $.ionSound({
						sounds: ["notify"],
						path: "./sounds/",
						multiPlay: !0,
						volume: a
					}), $.ionSound.play("notify"))))
				}
			})
		};
	(function(b) {
		b.extend({
			playSound: function(a) {
				return b("<embed src='" + a + ".mp3' hidden='true' autostart='true' loop='false' class='playSound'><audio autoplay='autoplay' style='display:none;' controls='controls'><source src='" + a + ".mp3' /><source src='" + a + ".ogg' /></audio>").appendTo("body")
			}
		})
	})(jQuery);
	$("#portal_chat").submit(function(b) {
		b.stopPropagation();
		b.preventDefault();
		
		checkInput();
		
		
		if (0 == private_chat) {
			for (instance in CKEDITOR.instances) CKEDITOR.instances[instance].updateElement();
			res = $("#chatMsg_add").val();
			emoticons = (emoticons = $("#parseEmoticons").is(":checked")) ? "true" : "false";
			trimmed = $.trim(res);
			if ("" == res || "" == trimmed) return !1;
			"" != res && "" != trimmed && $.ajax({
				type: "POST",
				data: "chatMsgContent=" + res + "&parseEmoticons=" + emoticons,
				cache: !1,
				url: "./system/controller/processors/chat_processor.php?ajaxSend=chatMessage",
				beforeSend: function() {
					msgSend_processing = !0;
					$("#chatMsg_add").animate({
						backgroundColor: "#CDCDCD"
					}, 200);
					setObjectLock(input, !0)
				},
				success: function(a) {
					"false" == a ? (chatLock = msgSend_processing = !0, setObjectLock(".chat_submit", !0), $(".portal_chat portal_container").animate({
						backgroundColor: "#CDCDCD"
					}, 200), setObjectLock(input, !0), clearInterval(pullInterval), $(".chat_msgField").append('<h3 class="chat_systemWarning">--  Sie m\u00fcssen sich erneut einloggen, um den Chat zu nutzen! --</h3>').animate({}, 1E3), $(".chat_msgField")[0].scrollHeight) : (msgSend_processing = !1, animateColor(baseInputColor, submitInputColor, input), $(".chat_msgField").append(a).children(":last").hide().fadeIn(3E3), $(".chat_msgField").animate({
						scrollTop: $(".chat_msgField")[0].scrollHeight
					}, 1E3), $("#chatMsg_add").val("").attr("disabled", !1).focus())
				}
			});
			return !1
		}
		for (instance in CKEDITOR.instances) CKEDITOR.instances[instance].updateElement();
		res = $("#chatMsg_add").val();
		emoticons = (emoticons = $("#parseEmoticons").is(":checked")) ? "true" : "false";
		trimmed = $.trim(res);
		if ("" == res || "" == trimmed) return !1;
		"" != res && "" != trimmed && $.ajax({
			type: "POST",
			data: "chatMsgContent=" + res + "&parseEmoticons=" + emoticons + "&prv_id=" + window.activeChat,
			cache: !1,
			url: "./system/controller/processors/chat_processor.php?ajaxSend=chatPrivateMessage",
			beforeSend: function() {
				msgSend_processing = !0;
				$("#chatMsg_add").animate({
					backgroundColor: "#CDCDCD"
				}, 200);
				setObjectLock(input, !0);
				console.log(window.activeChat)
			},
			success: function(a) {
				"false" == a ? (chatLock = msgSend_processing = !0, setObjectLock(".chat_submit", !0), $(".portal_chat portal_container").animate({
					backgroundColor: "#CDCDCD"
				}, 200), $(input).val(""), setObjectLock(input, !0), clearInterval(pullInterval), $(".chat_msgField").append('<h3 class="chat_systemWarning">--  Sie m\u00fcssen sich erneut einloggen, um den Chat zu nutzen! --</h3>').animate({}, 1E3), $(".chat_msgField")[0].scrollHeight) : (msgSend_processing = !1, animateColor(baseInputColor, submitInputColor, input), $(".chat_msgField").append(a).children(":last").hide().fadeIn(3E3), $(".chat_msgField").animate({
					scrollTop: $(".chat_msgField")[0].scrollHeight
				}, 1E3), $("#chatMsg_add").val("").attr("disabled", !1).focus());
				
				$("#portal_chat input[type='submit']").attr("disabled", true)
			}
		})
		
	});
	$(document).ready(function() {
		messagePullHandler();
	});
	var emoticon_timer;
	$("#parseEmoticons").change(function(b) {
		clearTimeout(emoticon_timer);
		emoticon_timer = setTimeout(function() {
			var a = $(this).attr("id");
			$.ajax({
				type: "POST",
				url: "./system/controller/processors/chat_processor.php?ajaxSend=changeEmoticonsState",
				data: "checkboxID=" + a,
				cache: !1,
				beforeSend: function() {
					$(".chat_emoticons_setting").prepend('<img id="catLoading" src="./images/loaders/loader_large.gif" width="25" height="25">').fadeIn("slow")
				},
				success: function() {
					$("#catLoading").fadeOut(1E3, function() {
						$(this).remove()
					})
				}
			})
		}, 2E3)
	});
	$(".public_chats").click(function() {
		var b = $(this).attr("id");
		$.ajax({
			type: "POST",
			url: "./system/controller/processors/chat_processor.php?ajaxSend=loadPub",
			data: "pub_id=" + b,
			cache: !1,
			beforeSend: function() {
				ClearChat()
			}
		})
	});
	$(".chat_prv").click(function() {
		clearTimeout(timer_const);
		$(".chat_prv").removeAttr("style");
		var b = $(this).attr("id");
		ClearChat();
		$(this).animate({
			backgroundColor: "#FFFFFF"
		}, 300).animate({
			backgroundColor: "#E7E9CD"
		}, 300);
		$.ajax({
			type: "POST",
			url: "./system/controller/processors/chat_processor.php?ajaxSend=loadPrv",
			data: "prv_id=" + b,
			cache: !1,
			beforeSend: function() {
				activeChat = b;
				console.log(activeChat);
				private_chat = !0;
				clearInterval(pullInterval);
				$(".chat_msgField").append('<img id="catLoading" src="./images/loaders/loader_large.gif" width="50" height="50">').fadeIn()
			},
			success: function(a) {
				$(".chat_msgField").append(a).children(".chatRow").hide().fadeIn("slow");
				$("#catLoading").slideToggle().remove();
				500 >= $(".chat_msgField")[0].scrollHeight - $(".chat_msgField").scrollTop() && $(".chat_msgField").animate({
					scrollTop: $(".chat_msgField")[0].scrollHeight
				}, 1E3);
				timer_initial = window.setTimeout(function() {
					privatePullHandler();
					clearTimeout(timer_initial)
				}, 5E3)
			}
		})
	});
	$("#toggleSidebar").click(function() {
		clearTimeout(sidebar_timer);
		var b = $(".chat_right_sidebar"),
			a;
		$(this);
		b.is(":visible") ? ($(".chat_right_sidebar").hide(), $(".chat_msgField").css("width", "100%"), $(".chat_headerOverlay").css("width", "100%"), a = "0") : ($(".chat_right_sidebar").show(), $(".chat_msgField").css("width", "70%"), $(".chat_headerOverlay").css("width", "70%"), a = "1");
		sidebar_timer = setTimeout(function() {
			$.ajax({
				type: "POST",
				url: "./system/controller/processors/chat_processor.php?save=sidebarState",
				data: "sidebarState=" + a,
				cache: !1
			})
		}, 2E3)
	})
};