@extends('layouts.master')

@section('mainbox', 'main')

@section('content')

	<h2>{{ $category->name }}</h2>
	<div id="addons">

		@forelse($category->addons()->get() as $addon)
			<a href="{{ route('addon.show', $addon->slug) }}"><div>{{ $addon->name }}</div></a>
		@empty
			Empty category
		@endforelse

	</div>

@endsection
