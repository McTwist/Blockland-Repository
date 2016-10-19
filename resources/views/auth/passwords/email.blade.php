@extends('layouts.master')

@section('content')

	{{ Form::open(['route' => 'password.email', 'method' => 'post']) }}
		<div>
			{{ Form::label('email', 'Email:') }}
			{{ Form::email('email') }}
		</div>
		<div>
			{{ Form::submit('Send Password Reset Link') }}
		</div>
		@if (count($errors) > 0)
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		@endif
	{{ Form::close() }}

@endsection
