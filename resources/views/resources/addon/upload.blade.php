
@section('stylesheets')
	<link rel="stylesheet" type="text/css" href="/css/upload.css">
@append

@section('scripts')
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/min/dropzone.min.js"></script>
	<script type="text/javascript" src="/js/upload.js"></script>
@append

@section('scripts.footer')
	<div id="uploadBox" style="display: none; position: absolute; top: 200px; left: 50%;">
		<div class="popup-box" style="position: relative; left: -50%;">
			<span class="title">Upload Add-On</span>
			<hr>
			<div class="file">
				{{ Form::open(array('route' => 'addon.upload', 'method' => 'put', 'id' => 'uploadAddon', 'files' => true, 'enctype' => 'multipart/form-data')) }}
					<label class="fileContainer noselect" id="dropClick">
						Choose File
					</label>
					<div id="uploadError">
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
@append
