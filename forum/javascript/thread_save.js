function GetURLParameter(sParam)
{

    var sPageURL = window.location.search.substring(1);

    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++)
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam)
        {
            return sParameterName[1];
        }
    }
}


function makeid()
{
    var tokenID = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 50; i++ )
        tokenID += possible.charAt(Math.floor(Math.random() * possible.length));

    return tokenID;
}

if(GetURLParameter('token') === undefined) { 
	var tokenID = makeid();
} else {
	var tokenID = GetURLParameter('token');
}

i = 0;
a = 0;

function saveContent() {

    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }

    var inptThread_title = $("#threadTitleInput").val();
    var inptThread_content = escape($("#threadAddArea").val());
	var inptThread_content_trimmed = $.trim(inptThread_content).length;
	
	var boardID	= GetURLParameter('boardview');
	

	if ($.trim(inptThread_title).length >= 2 && inptThread_content_trimmed >= 30) {
	i++;
	if(i == 1) {
	a = 0;
		$('.responseFailed').remove();
		$( '<span id="ThreadAddResponse_Success saving_activated" class="responseSuccess">Speicherung aktiviert.</span>' ).insertBefore( ".submitPost" ).hide().fadeIn(2000);
	}
    $.ajax({
        type: "POST",
        url: "./system/controller/processors/thread_save_processor.php?ajaxSend=saveThread",
        data: "postTitle=" + inptThread_title + '&postContent=' + inptThread_content + '&boardID=' + boardID + '&val_token=' + tokenID,
        cache: false,
		
	success: function(data) {
		console.log("Returned: \n \n \r"+data+"\n \n \r Local Text: \n \n \r"+inptThread_content);
	}
    });
	} else {
	a++;
	i = 0;
	if(a == 1) {
	$('.responseSuccess').remove();
	$( '<span id="ThreadAddResponse_Success saving_deactivated" class="responseFailed" title="Die Speicherung wird aktiviert, sobald die Mindestanforderungen erfüllt sind.">Speicherung deaktiviert.</span>' ).insertBefore( ".submitPost" ).hide().fadeIn(2000);
		}
	}
};

window.setInterval(function(){
saveContent();
}, 15000);