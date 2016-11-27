function clearPopup() {
	$('#popup-box-wrapper').hide();
	$('#popup-box-container').html(null);
}

function showPopup(html, popupBoxID) {
	var container = $('#popup-box-container');
	var popup = $('#' + popupBoxID);
	container.html(html);
	$('#popup-box-wrapper').show();

	$(document).on("mouseup.clickOFF touchend.clickOFF", function (e) {
		console.log("xclc");

		if (container.is(e.target))
		{
			clearPopup();
			$(document).off("mouseup.clickOFF touchend.clickOFF");
		}
	});
}

function showUploadPopup() {
	$.ajax({
		type: "GET",
		url: '/addon/upload',
		success: function (data) {
			showPopup(data, 'uploadBox');
		}
	});
}
