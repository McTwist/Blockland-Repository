@extends('layouts.master')

@section('stylesheets')

	<link rel="stylesheet" type="text/css" href="/css/front.css">

@append

@section('content')

	<section class="categories container-fluid nopad-l nopad-r">
		<div class="row">
			@foreach($categories as $category)
				@include('resources.categories.teaser', compact('category'))
			@endforeach
		</div>
	</section>

@endsection
