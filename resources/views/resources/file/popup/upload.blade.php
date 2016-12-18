<script type="text/javascript">
	$(function () {
		$.getScript('//cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/min/dropzone.min.js', function () {
			$.getScript('/js/upload.js');
		});
	});
</script>
<div class="row">
	<div class="col-xs-12 nopad-xs-both col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
		<div class="popup-box">
			<div class="container-fluid">
				{{ Form::open(['route' => 'file.upload', 'method' => 'put', 'id' => 'uploadAddon', 'files' => true, 'enctype' => 'multipart/form-data']) }}
				<fieldset class="blr-fieldset">
					{{-- A special case. The center aligned text looks better. --}}
					<legend style="text-align: center !important;">Upload Add-On</legend>
					<div class="row">
						<div class="file col-xs-12 nopad-xs-both">
							{{-- Choose File button --}}
							{{ Form::button('Choose File', ['id' => 'dropClick', 'class' => 'btn blr-btn btn-blr-default center-block width-xs-full width-sm-auto']) }}
						</div>
						<div id="uploadError" class="col-sm-12"></div>
					</div>
				</fieldset>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>

