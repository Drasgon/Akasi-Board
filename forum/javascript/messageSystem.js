var x = 0;
var refreshId = setInterval(function() {
    function getData(data) {
        if (data != "") {
            console.log(data);
            var arrayData = jQuery.parseJSON(data);
            var id = arrayData["id"];
            var thread_id = arrayData["thread_id"];
            var author = arrayData["author"];
            var authorAva = arrayData["authorAvatar"];
            var text = arrayData["text"];
            var threadTitle = arrayData["threadTitle"];
            x++;
			
/*             $.playSound("./sounds/notify");
            $("#refresh").append('<div class="newMsg" id="newMsg' + x + '"><span id="closeNewMsgFrame' + x + '" onclick="$(this).parent().remove();">X</span><img src="' + authorAva +
                '" width="60px" height="60px" class="msgImg"><a href="?page=Thread&threadID=' + thread_id + '"><div class="newMsgInner"><p>' + author + " hat im Thema " + threadTitle + " gepostet</p><p><p>" + text + "</p> vor einigen Sekunden</p></div></a></div>").hide().fadeIn(1E3).delay(15E3).fadeOut(3E3).hide(3E3);
            setTimeout(function() {
                if ($("#newMsg" + x + "").length > 0) $("#newMsg" + x + "").remove()
            }, 1E4) */
			
			
			systemNotification(author + " hat im Thema " + threadTitle + " gepostet", authorAva, text, '?page=Index&threadID=' + thread_id);
        }
    }
    $.post("./system/controller/ajax/notification.php", getData)
}, 17E3);
$.ajaxSetup({
    cache: false
});