Dropzone.autoDiscover = false;

$(function () {
	var paramName = "file";
	// Note: It was tested with the internal jQuery functionality, but somehow
	// it didn't work as expected, so using this manually instead
	// See: https://github.com/enyo/dropzone/issues/328
	var dropzone = new Dropzone('form#uploadAddon', {
		method: "post",
		paramName: paramName,
		acceptedFiles: ".zip",
		uploadMultiple: false,
		maxFiles: 1,
		maxFileSize: 50, // If you breach this you have put in too many ogg files
		clickable: '#dropClick',
		headers: {
			'X-CSRF-Token': $('input[name=_token]').val()
		},
		previewTemplate: '<div style="display: none;"></div>',
		init: function () {
			this.on("sending", function (file, xhr, formData) {
				btn = $('#dropClick');
				btn.data('old', btn.text());
				btn.text('Uploading...').prop('disabled', true);
			});
			this.on("uploadprogress", function (file) {
				// TODO: Display a progress of some sort
			});
			this.on("success", function (file, response) {
				// Reset state and display error message
				function error(msg) {
					btn = $('#dropClick');
					btn.text(btn.data('old')).prop('disabled', false);
					$('#uploadError').text(msg);
				}
				// Handle response
				if (response) {
					if (response.url) {
						window.location = response.url;
					}
					else if (response.error) {
						error(response.error);
					}
				}
				else {
					error('Unknown internal error');
				}
			});
		},
		fallback: function () {
			$('#dropClick').append($('<input type="file" name="' + paramName + '" accept=".zip, application/zip">').change(function () {
				// No fancy. Just submit the form
				$('form#uploadAddon').submit();
			}));
		}
	});
});
