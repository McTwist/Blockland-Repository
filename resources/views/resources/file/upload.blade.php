@extends('layouts.master')

@section('title', 'Upload Add-On')

@section('stylesheets')
	<link rel="stylesheet" type="text/css" href="/css/upload.css">
@append

@section('scripts')
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/min/dropzone.min.js"></script>
	<script type="text/javascript" src="/js/upload.js"></script>
@append

@section('content')
	<div class="container-fluid">
		{{ Form::open(['route' => 'file.upload', 'method' => 'put', 'id' => 'uploadAddon', 'files' => true, 'enctype' => 'multipart/form-data']) }}
		<fieldset class="blr-fieldset">
			<legend>Upload Add-On</legend>
			<div class="row">
				<div class="file col-xs-12 nopad-xs-both">
					{{-- Choose File button --}}
					{{ Form::button('Choose File', ['id' => 'dropClick', 'class' => 'btn blr-btn btn-blr-default center-block width-xs-full width-sm-auto']) }}
				</div>
				<div id="uploadError" class="col-xs-12"></div>
			</div>
		</fieldset>
		{{ Form::close() }}
	</div>
@endsection