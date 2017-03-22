/*
*
Copyright (c) 2016, Alexander Bretzke

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*

This ajax controller is supposed to control different modules
in the "Akasi Board" software.
You won't use it for other projects or simply without permission
of the author.
*/
function GetURLParameter(b) {
    for (var c = window.location.search.substring(1).split("&"), d = 0; d < c.length; d++) {
        var e = c[d].split("=");
        if (e[0] == b) return e[1]
    }
}

function makeid() {
    for (var b = "", c = 0; 50 > c; c++) b += "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789".charAt(Math.floor(62 * Math.random()));
    return b
}
var tokenID = void 0 === GetURLParameter("token") ? makeid() : GetURLParameter("token");
a = i = 0;

function saveContent() {
    var b = tinyMCE.activeEditor.getContent(),
        c = GetURLParameter("threadID");
		console.log(b);
    30 <= $.trim(b).length ? (i++, 1 == i && (a = 0, $(".responseFailed").remove(), $('<span id="ThreadAddResponse_Success saving_activated" class="responseSuccess replyAdd_save">Speichern aktiv</span>').insertBefore(".submitPost").hide().fadeIn(2E3)), $.ajax({
        type: "POST",
        url: "./system/controller/processors/post_save_processor.php?ajaxSend=savePost",
        data: "postContent=" +
            b + "&threadID=" + c + "&val_token=" + tokenID,
        cache: !1
    })) : (a++, i = 0, 1 == a && ($(".responseSuccess").remove(), $('<span id="ThreadAddResponse_Success saving_deactivated" class="responseFailed replyAdd_save" title="Das Speichern wird aktiviert, sobald die Mindestanforderungen erf\u00fcllt sind.">Speichern inaktiv</span>').insertBefore(".submitPost").hide().fadeIn(2E3)))
}

var postSaveInterval;

function clearPostSaveInterval()
{
	clearInterval(postSaveInterval);
}

postSaveInterval = window.setInterval(function() {
    saveContent()
}, 7E3);