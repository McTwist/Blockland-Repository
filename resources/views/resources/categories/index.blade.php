@extends('layouts.master')

@section('stylesheets')

	<link rel="stylesheet" type="text/css" href="/css/front.css">

@append

@section('content')

	<section class="categories">
		<h2>Categories</h2>

		@foreach($categories as $category)
			@include('resources.categories.teaser', compact('category'))
		@endforeach
	</section>

@endsection
