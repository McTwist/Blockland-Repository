function clearPopup() {
	$('#popup-box-wrapper').hide();
	$('#popup-box-container').html(null);
}

function showPopup(html) {
	var container = $('#popup-box-container');
	container.html(html);
	$('#popup-box-wrapper').show();

	$(document).on("mouseup.popupClickRelease touchend.popupTouchEnd", function (e) {
		if (container.is(e.target)) {
			clearPopup();
			$(document).off("mouseup.popupClickRelease touchend.popupTouchEnd");
		}
	});
}

function showView(url) {
	$.ajax({
		type: 'GET',
		url: url,
		success: function (data) {
			if (data !== '') {
				showPopup(data);
			}
			// If nothing was returned, do nothing.
		}
	});
}

// Fully automatic handling of popup links
$(function() {
	$('.show-popup').each(function() {
		$this = $(this);
		$this.click(function(e) {
			e.preventDefault();
			showView($(this).attr("href"));
		});
	});
});
