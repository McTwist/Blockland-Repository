@extends('layouts.master')

@section('content')
	<section class="categories">
		<div class="row">
			<div class="col-xs-12 col-sm-10 col-sm-offset-1">
				@foreach($categories as $category)
					@include('resources.categories.teaser', compact('category'))
				@endforeach
			</div>
		</div>
	</section>
@endsection
