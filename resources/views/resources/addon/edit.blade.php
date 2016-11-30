@extends('layouts.master')

@section('title', 'Edit ' . $addon->name)

@section('stylesheets')
	
	<link rel="stylesheet" type="text/css" href="/css/upload.css">

@append

@section('mainbox', 'edit')

@section('content')

	<span class="title">Edit Add-On</span>
	<hr>

	{{ Form::open(['route' => ['addon.update', $addon->slug], 'method' => 'put']) }}
		<div class="upload_category">
			{{ Form::Label('category', 'Category:') }}
			<div class="selectContainer">
				{{ Form::select('category', $categories, $addon->category_id, ['disabled']) }}
			</div>
		</div>
		<div class="upload_title">
			{{ Form::Label('title', 'Title:') }}
			{{ Form::text('title', $addon->name) }}
		</div>
		<div class="upload_summary">
			{{ Form::Label('summary', 'Summary:') }}
			{{ Form::text('summary', $addon->summary) }}
		</div>
		<div class="upload_developers">
			{{ Form::Label('developers', 'Developers:') }}
			{{ Form::text('developers', $addon->authors) }}
		</div>
		<div class="upload_description">
			{{ Form::Label('description', 'Description:') }}
			{{ Form::textarea('description', $addon->description) }}
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
			{{ Form::submit('Update') }}
		</div>
	{{ Form::close() }}

	{{ Form::open(['route' => ['addon.destroy', $addon->slug], 'method' => 'delete']) }}
		{{ Form::submit('Delete Add-On') }}
	{{ Form::close() }}	

@endsection
