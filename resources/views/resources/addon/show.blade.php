@extends('layouts.master')

@section('title', $addon->name . ' by ' . $addon->authors)

@section('stylesheets')
	<link rel="stylesheet" type="text/css" href="/css/view.css">
@append

@section('mainbox', 'view')

@section('content')
	<div class="row">
		@if(Auth::user() && $addon->IsOwner(Auth::user()) && false)
			<div class="col-xs-12 col-sm-7 hug-xs-left nopad-xs-right mar-top">
				<div class="col-xs-12 nopad-xs-right title">{{ $addon->name }}</div>
				<div class="col-xs-12 nopad-xs-right subtitle">by {{ $addon->authors }}</div>
				<div class="col-xs-12 nopad-xs-right summary">{{ $addon->summary }}</div>
			</div>
			<div class="col-xs-12 nopad-xs-right text-xs-center col-sm-2 mar-top">
				{{-- TODO: Dynamic add-on states. --}}
				<div class="col-xs-6 col-sm-12 addon-state state-public"></div>
				<div class="col-xs-6 col-sm-12 mar-top-half"><a href="{{ route('addon.edit', $addon->slug) }}">Edit</a>
				</div>
			</div>
			<div class="col-xs-12 col-sm-3 mar-top">
				<div class="col-xs-12 nopad-xs-both mar-bottom-half">
					<a class="btn blr-btn btn-blr-default width-xs-full"
					   href="{{ $addon->download_link }}">Download</a>
				</div>
				<div class="col-xs-12 filesize">{{ $addon->size_bin }}</div>
			</div>
		@else
			<div class="col-xs-12 col-sm-10 hug-xs-left mar-top">
				<div class="col-xs-12 nopad-xs-right title">{{ $addon->name }}</div>
				<div class="col-xs-12 nopad-xs-right subtitle">by {{ $addon->authors }}</div>
				<div class="col-xs-12 nopad-xs-right summary">{{ $addon->summary }}</div>
			</div>
			<div class="col-xs-12 col-sm-2 nopad-sm-left mar-top">
				<div class="col-xs-12 nopad-xs-both mar-bottom-half">
					<a class="btn blr-btn btn-blr-default width-xs-full"
					   href="{{ $addon->download_link }}">Download</a>
				</div>
				<div class="col-xs-12 filesize">{{ $addon->size_bin }}</div>
			</div>
		@endif
	</div>
	<hr class="mar-bottom-none">
	<div class="row inforow">
		{{-- TODO: Figure out nice separators like in the concept that work at xs scale. --}}
		<div class="col-xs-6 col-sm-3 infobox">
			<div class="col-xs-12 info-key">Version</div>
			<div class="col-xs-12 info-value">{{ $addon->version_name }}</div>
		</div>
		<div class="col-xs-6 col-sm-3 infobox">
			<div class="col-xs-12 info-key">Downloads</div>
			<div class="col-xs-12 info-value">{{ $addon->downloads }}</div>
		</div>
		<div class="col-xs-6 col-sm-3 infobox">
			<div class="col-xs-12 info-key">Updated</div>
			<div class="col-xs-12 info-value">{{ date('Y-m-d', $addon->updated_at->getTimestamp()) }}</div>
		</div>
		<div class="col-xs-6 col-sm-3 infobox">
			<div class="col-xs-12 info-key">Created</div>
			<div class="col-xs-12 info-value">{{ date('Y-m-d', $addon->created_at->getTimestamp()) }}</div>
		</div>
	</div>
	<hr class="mar-top-half">
	<div class="row">
		<div class="col-xs-12 description">
			{!! $addon->description_html or '<p>No description.</p>' !!}
		</div>
		<div class="col-xs-12 text-xs-right footnote mar-top">{{ $addon->filename }} uploaded by <a
					href="{{ route('user.show', $addon->uploader->id) }}">{{ $addon->uploader->username }}</a>
		</div>
	</div>

@endsection
