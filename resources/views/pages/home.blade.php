@extends('layouts.master')

@section('content')

	<div class="categories">
		@foreach($categories as $category)
			@include('models.categories.teaser', compact('category'))
		@endforeach
	</div>

@endsection

@section('footer')
	@include('partials.footer')
@endsection
