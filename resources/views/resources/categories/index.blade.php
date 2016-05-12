@extends('layouts.master')

@section('content')

	<section class="categories">
		<h2>Categories</h2>

		@foreach($categories as $category)
			@include('resources.categories.teaser', compact('category'))
		@endforeach
	</section>

@endsection