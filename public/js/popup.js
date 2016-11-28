function clearPopup() {
	$('#popup-box-wrapper').hide();
	$('#popup-box-container').html(null);
}

function showPopup(html, elementID) {
	var container = $('#popup-box-container');
	var popup = $('#' + elementID);
	container.html(html);
	$('#popup-box-wrapper').show();

	$(document).on("mouseup.popupClickRelease touchend.popupTouchEnd", function (e) {
		if (container.is(e.target)) {
			clearPopup();
			$(document).off("mouseup.popupClickRelease touchend.popupTouchEnd");
		}
	});
}

function showView(url, elementID) {
	$.ajax({
		type: 'GET',
		url: url,
		success: function (data) {
			if (data !== '') {
				showPopup(data, elementID);
			}
			// If nothing was returned, do nothing.
		}
	});
}

function showUploadPopup() {
	showView('/addon/upload', 'uploadBox');
}

function showLoginPopup() {
	showView('/user/login', 'login-box');
}
