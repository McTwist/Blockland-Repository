
@extends('layouts.master')

@section('stylesheets', '')

@section('mainbox', 'main')

@section('content')

	<div id="addons">

		@forelse($addons as $addon)
			<a href="/addon/{{ $addon->slug }}"><div>{{ $addon->name }}</div></a>
		@empty
			Unknown category
		@endforelse

	</div>

@endsection

@section('footer')
	@include('partials.footer')
@endsection
