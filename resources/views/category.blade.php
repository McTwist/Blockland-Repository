
@extends('layouts.master')

@section('stylesheets', '')

@section('mainbox', 'main')

@section('content')

	<div id="addons">

		@forelse($addons as $addon)
			<a href="{{ route('addon.show', $addon->slug) }}"><div>{{ $addon->name }}</div></a>
		@empty
			Unknown category
		@endforelse

	</div>

@endsection