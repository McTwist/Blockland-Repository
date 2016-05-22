@extends('layouts.master')

@section('content')
	
	{{ Form::open(array('route' => 'user.register', 'method' => 'post')) }}
		<div>
			{{ Form::label('username', 'Name:') }}
			{{ Form::text('username', old('username')) }}
		</div>
		<div>
			{{ Form::label('email', 'Email:') }}
			{{ Form::text('email', old('email')) }}
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
			{{ Form::submit('Register') }}
		</div>
	{{ Form::close() }}

	@if(count($errors) > 0)
		@foreach($errors as $error)
			{{ $error }}
		@endforeach
	@endif

@endsection
