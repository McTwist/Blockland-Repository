
$(function() {
	var timer = false;
	var request = false;
	// Loading handling
	function showLoading() {
		$('.loader').show();
		$('.checkmark, .crossmark').hide();
	}
	function hideLoading() {
		$('.loader').hide();
	}
	var $error = $('<li id="bl_id-error">').appendTo('#error-list').hide();
	// Aquire event
	$('#blockland_id, #blockland_name').keyup(function() {
		$('#blockland_id, #blockland_name').removeClass('blr-error');
		// Stop timer
		if (timer !== false)
			cleartimeout(timer);
		// Stop request
		if (request !== false)
			request.abort();
		// Invalid values
		if (!$('#blockland_id').val() || !$.isNumeric($('#blockland_id').val()) || !$('#blockland_name').val())
			return hideLoading();
		// Start the timer before verifying
		timer = setTimeout(function() {
			showLoading();
			$.when(request = $.ajax('/auth/ip', {
				method: 'GET',
				data: {
					'id': $('#blockland_id').val(),
					'name': $('#blockland_name').val()
				},
				dataType: 'json',
				success: function(data) {
					if (!data)
					{
						$('#bl_id-error').text('Unknown internal error').show();
						return;
					}
					switch (data.code) {
					case 'VERIFIED':
						$('#bl_id-error').hide();
						$('.checkmark').show();
						return;
					case 'NO_SERVER':
					case 'INVALID_IP':
						break;
					case 'ALREADY_VERIFIED':
					case 'INVALID_NAMEID':
					default:
						$('#blockland_id, #blockland_name').addClass('blr-error');
						break;
					}
					$('#bl_id-error').text(data.msg).show();
					$('.crossmark').show();
				}
			})).then(function() {
				request = false;
				hideLoading();
			});
			timer = false;
		}, 1000);
	});
	// Make sure everything is closed manually
	$(window).unload(function() {
		if (timer !== false)
			cleartimeout(timer);
		if (request !== false)
			request.abort();
	});
});
