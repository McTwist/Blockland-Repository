@extends('layouts.master')

@section('mainbox', 'main')

@section('content')

	<h2>{{ $category->name }}</h2>
	<div id="addons">

		@forelse($category->repositories()->get() as $repo)
			<a href="{{ route('addon.show', $repo->slug) }}"><div>{{ $repo->name }}</div></a>
		@empty
			Empty category
		@endforelse

	</div>

@endsection
