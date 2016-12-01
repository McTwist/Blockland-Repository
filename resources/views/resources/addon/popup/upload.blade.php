<link rel="stylesheet" type="text/css" href="/css/upload.css">
<script type="text/javascript">
$(function() {
	$.getScript('//cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/min/dropzone.min.js', function() {
		$.getScript('/js/upload-popup.js', function() {
			initDropzone();
		});
	});
});
</script>

<div id="uploadBox" class="popup-box">
	{{ Form::open(['route' => 'addon.popup.upload', 'method' => 'put', 'id' => 'uploadAddon', 'files' => true, 'enctype' => 'multipart/form-data']) }}
	<fieldset class="blr-fieldset">
		<legend>Upload Add-On</legend>
		<hr>
		<div class="container-fluid">
			<div class="row">
				<div class="file col-xs-12">
					{{-- Choose File button --}}
					{{ Form::button('Choose File', ['id' => 'dropClick', 'class' => 'btn blr-btn btn-blr-default center-block fileContainer']) }}
				</div>
				<div id="uploadError" class="col-sm-12"></div>
			</div>
		</div>
	</fieldset>
	{{ Form::close() }}
</div>

