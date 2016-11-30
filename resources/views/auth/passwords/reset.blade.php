@extends('layouts.master')

@section('title', 'Reset Password')

@section('content')

	{{ Form::open(['route' => 'password.reset', 'method' => 'post']) }}
		{{ Form::hidden('token', $token) }}
		<div>
			{{ Form::label('email', 'Email:') }}
			{{ Form::email('email') }}
		</div>
		<div>
			{{ Form::label('password', 'Password:') }}
			{{ Form::password('password') }}
		</div>
		<div>
			{{ Form::label('password_confirmation', 'Confirm Password:') }}
			{{ Form::password('password_confirmation') }}
		</div>
		<div>
			{{ Form::submit('Reset Password') }}
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
