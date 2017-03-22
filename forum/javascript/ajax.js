/*
*
Copyright (c) 2014, Alexander Bretzke

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*

This ajax controller is supposed to control different modules
in the "Akasi Board" software.
You won't use it for other projects or simply without permission
of the author.
*/
function clearResponse() {
    $("span#response_failed").fadeOut("slow").remove()
}

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) {
            return sParameterName[1];
        }
    }
}

function login()
{
    var b, e, f;
    $(document).on('submit', '#loginform', function(a) {
	
        a.stopPropagation();
        a.preventDefault();
		
		
		$(document).off('submit');
			
            if ("" == $("#username").val() && "" == $("#password").val()) 
				return $("#response_failed").html("Bitte geben sie einen Nutzernamen sowie das Passwort ein!").slideDown("fast");
			if("" == $("#username").val())
				$("#response_failed").html("Bitte geben sie einen Usernamen ein!").slideDown("fast");
            if(13 < $("#username").val().length)
				$("#username").val(""), $("#response_failed").html("Fehlerhafte Eingaben!");
			if("" == $("#password").val())
				$("#response_failed").html("Bitte geben sie ihr Passwort ein!").slideDown("fast");
			if(34 < $("#password").val().length)
				$("#password").val(""), $("#response_failed").html("Fehlerhafte Eingaben!").slideDown("fast");
            if (34 < $("#password").val().length && 13 < $("#username").val().length)
				return $("#response_failed").html("Fehlerhafte Eingaben!").slideDown("fast");
			if("" != $("#username").val() && "" != $("#password").val() && 13 >= $("#username").val().length && 34 >= $("#password").val().length)
			{
				

                $("#username").val(), $("#password").val(), $("#submit").attr("disabled", "disabled"), $("#response_failed").slideUp(350, function() {
                    $(this).html("")
                });
					b = $(".quickLogin_User").val();
					e = $(".quickLogin_Pass").val();
					f = $("#checkboxThreeInput").is(":checked") ? 1 : 0,
					
					$.ajax({
						xhrFields: {
							withCredentials: true
						},
						type: "POST",
						data: "username=" + b + "&password=" + e + "&StayLoggedIn=" + f,
						cache: false,
						url: "./system/controller/sessions/login.php",
						beforeSend: function() {
							$("#response_failed").slideUp("fast");
							$("#response_loading").append("<img src='./images/loaders/loader_large.gif' width='50' height='50' class='login_response_loader' />");
							$(".login_response_loader").hide().fadeIn(350);
							$("#username").attr("disabled", "disabled");
							$("#password").attr("disabled", "disabled");
						},
						success: function(a) {
							-1 === a.indexOf("<meta") && $(".login_response_loader").fadeOut("slow", function() {
								$(".login_response_loader").remove();
								$("#response_failed").html("").append(a).slideDown(350);
								$("#username").val("");
								$("#password").val("");
								$("#username").removeAttr("disabled");
								$("#password").removeAttr("disabled");
								setTimeout(function() {
									$("#submit").removeAttr("disabled")
								}, 4E3)
							}); if (a.indexOf("<meta") > 0)
								{
									$("#response_success").append(a).hide().slideToggle(350).css({"color" : "green", "margin-top" : "35px"});
									$(".staylogin_status, .LoginOptions").fadeOut(250);
								}
						}
					});
			}
    });
}

function boardCategoryToggle()
{
    var d = 1;
    setInterval(function() {
        ++d
    }, 1E3);
    $(".catHeaderOuter").click(function() {
        1.1 >= d && ($(this).animate({
            backgroundColor: "#F75E5E"
        }, 150).animate({
            backgroundColor: "#6CB0C2"
        }, 150).animate({
            backgroundColor: "#F75E5E"
        }, 150).animate({
            backgroundColor: "#6CB0C2"
        }, 150).animate({
            backgroundColor: "#F75E5E"
        }, 150).animate({
            backgroundColor: "#6CB0C2"
        }, 150, function() {
            $(this).attr("style", "")
        }), $(this).attr("style", ""));
        if (1.2 <= d) {
            d = 0;
            var a = $(this).parent().attr("class");
            if ("boardCat_row" == a) var b = $(this).parent().attr("id"),
                g = "boardCat_row=" + b;
            else a = $(this).parent().attr("class"), "portal_userActions_inner" == a && (b = $(this).parent().attr("id"), g = "portal_cat=" + b);
            a = 0 == $(this).next().has("li").length;
            console.log(a);
            var c = $(this);
            a ? confirm("M\u00f6chten Sie diese Kategorie laden?") && $.ajax({
                type: "POST",
                data: "loadCategory=" + b,
                cache: !1,
                url: "./system/controller/processors/category_processor.php?ajaxSend=catLoad",
                beforeSend: function() {
                    c.next().animate({
                        height: "45px"
                    }, 400).append('<img id="catLoading" src="./images/loaders/loader_large.gif" width="50" height="50">').hide().slideToggle()
                },
                success: function(a) {
                    c.animate({
                        backgroundColor: "#5AC967"
                    }, 100).delay(2E3).animate({
                        backgroundColor: "#6CB0C2"
                    }, 1500, function() {
                        c.attr("style", "")
                    });
                    c.next().animate({
                        height: "0px"
                    });
                    $("#catLoading").animate({
                        height: "0px"
                    }).fadeOut(500, function() {
                        c.next().append(a).hide().show(250).animateAuto("height", 500);
                        $(this).remove()
                    })
                },
                error: function(a) {
                    c.animate({
                        backgroundColor: "#F75E5E"
                    }, 150).animate({
                        backgroundColor: "#6CB0C2"
                    }, 150).animate({
                        backgroundColor: "#F75E5E"
                    }, 150).animate({
                            backgroundColor: "#6CB0C2"
                        },
                        150).animate({
                        backgroundColor: "#F75E5E"
                    }, 150).animate({
                        backgroundColor: "#6CB0C2"
                    }, 150, function() {
                        $(this).attr("style", "")
                    });
                    $("#catLoading").animate({
                        height: "0px"
                    }).fadeOut(2E3, function() {
                        $(this).remove()
                    })
                }
            }) : (console.log(b), $.ajax({
                type: "POST",
                data: g,
                url: "index.php?ajaxSend=saveStatus"
            }).done(function(a) {
                c.animate({
                    backgroundColor: "#5AC967"
                }, 100).delay(2E3).animate({
                    backgroundColor: "#6CB0C2"
                }, 1500, function() {
                    c.attr("style", "")
                });
                c.next().slideToggle()
            }).fail(function(a) {
                c.animate({
                        backgroundColor: "#F75E5E"
                    },
                    100).delay(3E3).animate({
                    backgroundColor: "#6CB0C2"
                }, 3E3, function() {
                    c.attr("style", "")
                })
            }))
        }
    });
}
	
function galleryPageSwitch()
{
    $(".galleryPages a").click(function(a) {
        a.stopPropagation();
        a.preventDefault();
        a = $(this).attr("href");
        a = a.split("galleryPage=");
        page = a[1];
        var b = $(this),
            d = $(".galleryContent").height() + 20;
        $.ajax({
            type: "POST",
            data: "galleryPage=" + page,
            url: "./system/controller/ajax/gallery_loader.php",
            beforeSend: function() {
                $(".galleryContent").children().remove();
                $(".galleryContent").css("height", d)
            },
            success: function(a) {
                var d =
                    "?page=Gallery&galleryPage=" + page;
                $(".galleryContent").removeAttr("style").html("").append(a).children().hide().fadeIn(1500);
                $("a").removeAttr("style");
                $(b).css({
                    textShadow: "#FFFFFF 0 0 35px",
                    color: "#DBD28E"
                });
                window.history.pushState("", "", d)
            }
        })
    })
}

function galleryInformationSelect()
{
    $("#imageInformation select").change(function() {
        var obj = $(this);
        var type = $(this).attr("id");
        var value = $(this).val();

        var page = getUrlParameter("Image");

        type = type.replace('Changer', '');

        console.log(type + ", value: " + value);
        $.ajax({
            type: "POST",
            data: "galleryPage=" + page + "&type=change&changeType=" + type + "&value=" + value,
            url: "./system/controller/ajax/gallery_ajax_processor.php",
            beforeSend: function() {
                obj.attr("disabled", "disabled");
            },
            success: function(data) {
                obj.removeAttr("disabled");
            }
        });

    });
}

	
var passes = 0;

function resetUploadProcess() {
    passes = 0;
    $("#uploadPreview").fadeOut("slow").removeAttr("src").hide();
    $("#imageUploadContinue").attr("disabled", "disabled");
    $("#imageUploadContinue").attr("value", "Weiter")
}

function checkUploadImage(b) {
    if ("" === $(b).val()) return resetUploadProcess(), !1;
    $("#uploadPreview").fadeIn("slow");
    $("#imageUploadContinue").removeAttr("disabled");
    $("#imageUploadContinue").attr("value", "Weiter");
    return !0
}

function updateImage(b) {
    readURL(b, $("#uploadPreview"));
    $("#uploadPreview").removeAttr("style").fadeOut("fast").hide();
    resetUploadProcess(b);
    checkUploadImage(b)
}
$("#imageUploadContinue").click(function(b) {
    passes++;
    1 >= passes && (0 === passes && $(this).attr("value", "Weiter"), 1 === passes && (checkUploadImage("#file") ? (console.log("submit"), $("#galleryUploadForm").submit()) : resetUploadProcess()));
    b.stopPropagation();
    b.preventDefault()
});