
@extends('layouts.master')

@section('stylesheets')

	<link rel="stylesheet" type="text/css" href="/css/view.css">

@append

@section('mainbox', 'view')

@section('content')

	<div id="title">
		<span class="title">{{ $addon->name }}</span>
		<span class="subtitle">by {{ $addon->authors() }}</span>
		<span class="summary">{{ $addon->summary() }}</span>
	</div>
	<div id="download">
		<a href="{{ $addon->download_link() }}" class="button">Download</a>
		<span class="bytes">{{ $addon->size_bin() }}</span>
		@if($addon->IsOwner(Auth::user()))
			<span class="owner"><a href="{{ route('addon.edit', $addon->slug) }}">Edit</a></span>
		@endif
	</div>
	<hr class="over">
	<div class="infobox">
		<div class="info">
			<span class="tell">Version</span>
			<span class="data">{{ $addon->version() }}</span>
		</div>
		<div class="info">
			<span class="tell">Downloads</span>
			<span class="data">{{ $addon->downloads() }}</span>
		</div>
		<div class="info">
			<span class="tell">Updated</span>
			<span class="data">{{ date('Y-m-d', $addon->updated_at->getTimestamp()) }}</span>
		</div>
		<div class="info">
			<span class="tell">Created</span>
			<span class="data">{{ date('Y-m-d', $addon->created_at->getTimestamp()) }}</span>
		</div>
	</div>

@endsection

@section('footer')

	<hr class="over">
	<div id="description">
		{{ $addon->description or 'None' }}
	</div>
	<div id="uploader">{{ $addon->filename() }} uploaded by {{ $addon->uploader() }}</div>
	
@endsection
