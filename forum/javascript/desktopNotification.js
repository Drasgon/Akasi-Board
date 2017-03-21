	
	systemNotes = false;
	
	function checkPermission()
	{
		if (Notification.permission === "granted")
			systemNotes = true;
	}

	// request permission on page load
	document.addEventListener('DOMContentLoaded', function () {
	  if (Notification.permission !== "granted")
		Notification.requestPermission();
	});

	function systemNotification(title, img, msg, url) {
	  if (!Notification) {
		alert('Desktop notifications not available in your browser. Try Chromium.'); 
		return;
	  }

	  if (Notification.permission !== "granted")
		Notification.requestPermission();
	  else {
		var notification = new Notification(title, {
		  icon: img,
		  body: msg,
		});

		notification.onclick = function () {
		  window.open(url);      
		};
		
	  }

	}