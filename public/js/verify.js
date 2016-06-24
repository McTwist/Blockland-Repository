
function verify_ip() {
	$.ajax('/auth/ip', {
		method: 'GET',
		data: {
			'id': $('#blockland_id').val(),
			'name': $('#blockland_name').val()
		},
		dataType: 'json',
		success: function(data) {
			switch (data.code) {
			case 'VERIFIED':
				$('#blockland_msg').val(data.msg);
				break;
			}
		}
	});
}
