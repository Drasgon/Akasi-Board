$(document).ready(function(){
    var bg = $("#LogoutOverlay");
	var panel = $("#logoutPanel");

$("#openLogout").click(function(event){
	bg.fadeIn(500);
	panel.fadeIn(500);
});
$(".close").click(function(event){
	bg.fadeOut(300);
});
$(document).mouseup(function (e)
{
    if (!panel.is(e.target) // if the target of the click isn't the container...
        && panel.has(e.target).length === 0) // ... nor a descendant of the container
    {
        bg.fadeOut(300);
    }
});
$( document ).on('keydown',function(e) {
    if ( e.keyCode === 27 ) { // ESC
		bg.fadeOut(300);
    }
});
})