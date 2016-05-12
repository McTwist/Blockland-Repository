@extends('layouts.master')

@section('content')

	<div id="categories">
		@foreach($categories as $category)
			@if($category->icon !== null)
				<a href="/category/{{$category->id}}" style="background-image: url('/img/{{$category->icon}}');"><div>{{$category->name}}</div></a>
			@else
				<a href="/category/{{$category->id}}"><div>{{$category->name}}</div></a>
			@endif
		@endforeach
	</div>

@endsection

@section('footer')
	@include('partials.footer')
@endsection
