@extends('layouts.master')

@section('stylesheets')
	
	<link rel="stylesheet" type="text/css" href="/css/upload.css">

@endsection

@section('mainbox', 'upload')

@section('content')

	<span class="title">Upload Add-On</span>
	<hr>

	{{ Form::open(array('route' => 'addon.store', 'method' => 'post')) }}
		<div class="upload_category">
			{{ Form::Label('category', 'Category:') }}
			<div class="selectContainer">
				{{ Form::select('category', array(1 => 'List of categories')) }}
			</div>
		</div>
		<div class="upload_title">
			{{ Form::Label('title', 'Title:') }}
			{{ Form::text('title', $title) }}
		</div>
		<div class="upload_summary">
			{{ Form::Label('summary', 'Summary:') }}
			{{ Form::text('summary', $summary) }}
		</div>
		<div class="upload_developers">
			{{ Form::Label('developers', 'Developers:') }}
			{{ Form::text('developers', $developers) }}
		</div>
		<div class="upload_description">
			{{ Form::Label('description', 'Description:') }}
			{{ Form::textarea('description', $description) }}
		</div>
		<hr class="over">
		<span class="title2">Screenshots</span>
		<br>
		<div class="screenshots">
			<label class="fileContainer noselect">
				Browse
				<input type="file" name="screenshot" value="Browse">
			</label>
		</div>
		<div class="upload">
			{{ Form::submit('Upload') }}
		</div>
	{{ Form::close() }}

@endsection
