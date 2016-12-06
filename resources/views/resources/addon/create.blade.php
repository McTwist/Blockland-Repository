@extends('layouts.master')

@section('title', 'Upload Add-On')

@section('mainbox', 'upload')

@section('content')
	<span class="title">Upload Add-On</span>
	<hr>

	{{ Form::open(array('route' => 'addon.store', 'method' => 'post')) }}
		<div class="upload_category">
			{{ Form::Label('category', 'Category:') }}
			<div class="selectContainer">
				{{ Form::select('category', $categories, $category) }}
			</div>
		</div>
		<div class="upload_title">
			{{ Form::Label('title', 'Title:') }}
			{{ Form::text('title', $title, ['required']) }}
		</div>
		<div class="upload_summary">
			{{ Form::Label('summary', 'Summary:') }}
			{{ Form::text('summary', $summary, ['required']) }}
		</div>
		<div class="upload_developers">
			{{ Form::Label('developers', 'Developers:') }}
			{{ Form::text('developers', $developers, ['required']) }}
		</div>
		<div class="upload_description">
			{{ Form::Label('description', 'Description:') }}
			{{ Form::textarea('description', $description, ['required']) }}
		</div>
		<div class="upload_channel">
			{{ Form::Label('channel', 'Channel:') }}
			{{ Form::text('channel', $channel) }}
		</div>
		<div class="upload_version">
			{{ Form::Label('version', 'Version:') }}
			{{ Form::text('version', $version) }}
		</div>
		<div class="upload_error">
			@foreach($error as $err)
				<div>{{ $err['message'] }}</div>
			@endforeach
		</div>
		<hr class="over">
		<span class="title2">Screenshots</span>
		<br>
		<div class="screenshots">
			<label class="noselect">
				Browse
				<input type="file" name="screenshot" value="Browse">
			</label>
		</div>
		<div class="upload">
			{{ Form::submit('Upload') }}
		</div>
	{{ Form::close() }}
@endsection
