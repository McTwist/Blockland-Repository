@extends('layouts.master')

@section('content')

	<div class="categories">
		@foreach($categories as $category)
			@include('resources.categories.teaser', compact('category'))
		@endforeach
	</div>

@endsection
