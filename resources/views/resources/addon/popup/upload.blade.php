<link rel="stylesheet" type="text/css" href="/css/upload.css">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/min/dropzone.min.js"></script>
<script type="text/javascript" src="/js/upload.js"></script>

<div id="uploadBox" class="popup-box">
	{{ Form::open(['route' => 'addon.popup.upload', 'method' => 'put', 'id' => 'uploadAddon', 'files' => true, 'enctype' => 'multipart/form-data']) }}
	<fieldset class="blr-fieldset">
		<legend>Upload Add-On</legend>
		<hr>
		<div class="container-fluid">
			<div class="row">
				<div class="file col-xs-12">
					<!-- Choose File button -->
					{{-- TODO: Fix the Javascript. You need to click the button twice to load a file + you get an error in console, but it works. --}}
					{{ Form::button('Choose File', ['onclick' => 'initDropzone()', 'id' => 'dropClick', 'class' => 'btn blr-btn btn-blr-default center-block fileContainer']) }}
					<div id="uploadError" class="col-sm-12"></div>
				</div>
			</div>
		</div>
	</fieldset>
	{{ Form::close() }}
</div>
