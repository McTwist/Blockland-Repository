@extends('layouts.master')

@section('mainbox', 'main')

@section('content')

	<h2>{{ $category->name }}</h2>
	<div id="addons">

		@forelse($category->repositories as $repo)
			<a href="{{ $repo->route }}"><div>{{ $repo->name }}</div></a>
		@empty
			Empty category
		@endforelse

	</div>

@endsection
