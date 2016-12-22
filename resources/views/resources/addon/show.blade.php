@extends('layouts.master')

@section('title', $addon->name . ' by ' . $addon->authors->implode('name', ', '))

@section('mainbox', 'view')

@section('content')
	{!! Breadcrumbs::render('repo', $addon) !!}
	{{-- Negative margin is needed so the download button can have a top margin for xs size. --}}
	<div class="container-fluid mar-xs-top-neg">
		<div class="row">
			@if(Auth::user() && $addon->IsOwner(Auth::user()))
				<div class="col-xs-12 col-sm-7 hug-xs-left nopad-xs-right mar-xs-top">
					<div class="col-xs-12 nopad-xs-both title">{{ $addon->name }}</div>
					<div class="col-xs-12 nopad-xs-both subtitle">by {{ $addon->authors->implode('name', ', ') }}</div>
					<div class="col-xs-12 nopad-xs-both summary">{{ $addon->summary }}</div>
				</div>
				<div class="col-xs-12 nopad-xs-right text-xs-center mar-xs-top col-sm-2">
					{{-- TODO: Dynamic add-on states. --}}
					<div class="col-xs-6 col-sm-12 addon-state state-public"></div>
					<div class="col-xs-6  mar-xs-top-half col-sm-12"><a
								href="{{ route('addon.edit', $addon->slug) }}">Edit</a>
					</div>
				</div>
				<div class="col-xs-12 mar-xs-top col-sm-3">
					<div class="col-xs-12 nopad-xs-both mar-xs-btm-half">
						<a class="btn blr-btn btn-blr-default width-xs-full"
						   href="{{ $addon->download_link }}">Download</a>
					</div>
					<div class="col-xs-12 filesize">{{ $addon->size_bin }}</div>
				</div>
			@else
				<div class="col-xs-12 mar-xs-top col-sm-10 hug-xs-left">
					<div class="col-xs-12 nopad-xs-both title">{{ $addon->name }}</div>
					<div class="col-xs-12 nopad-xs-both subtitle">by {{ $addon->authors->implode('name', ', ') }}</div>
					<div class="col-xs-12 nopad-xs-both summary">{{ $addon->summary }}</div>
				</div>
				<div class="col-xs-12 mar-xs-top col-sm-2 nopad-sm-left">
					<div class="col-xs-12 nopad-xs-both mar-xs-btm-half">
						<a class="btn blr-btn btn-blr-default width-xs-full"
						   href="{{ $addon->download_link }}">Download</a>
					</div>
					<div class="col-xs-12 filesize">{{ $addon->size_bin }}</div>
				</div>
			@endif
		</div>
		<hr class="mar-xs-btm-none">
		<div class="row inforow">
			{{-- TODO: Figure out nice separators like in the concept that work at xs scale. --}}
			<div class="col-xs-6 mar-xs-top-half col-sm-3">
				<div class="col-xs-12 info-key">Version</div>
				<div class="col-xs-12 info-value">{{ $addon->version_name }}</div>
			</div>
			<div class="col-xs-6 mar-xs-top-half col-sm-3">
				<div class="col-xs-12 info-key">Downloads</div>
				<div class="col-xs-12 info-value">{{ $addon->downloads }}</div>
			</div>
			<div class="col-xs-6 mar-xs-top-half col-sm-3">
				<div class="col-xs-12 info-key">Updated</div>
				<div class="col-xs-12 info-value">{{ date('Y-m-d', $addon->updated_at->getTimestamp()) }}</div>
			</div>
			<div class="col-xs-6 mar-xs-top-half col-sm-3">
				<div class="col-xs-12 info-key">Created</div>
				<div class="col-xs-12 info-value">{{ date('Y-m-d', $addon->created_at->getTimestamp()) }}</div>
			</div>
		</div>
		<hr class="mar-xs-top-half">
		<div class="row">
			<div class="col-xs-12 nopad-xs-both description">
				{!! $addon->description_html or '<p>No description.</p>' !!}
			</div>
			<div class="col-xs-12 nopad-xs-both text-xs-right mar-xs-top footnote">{{ $addon->filename }} uploaded by <a
						href="{{ route('user.show', $addon->uploader->id) }}">{{ $addon->uploader->displayname }}</a>
			</div>
		</div>
	</div>
@endsection
