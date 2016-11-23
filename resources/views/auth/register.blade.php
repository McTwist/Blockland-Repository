@extends('layouts.master')

@section('content')
	
	{{ Form::open(array('route' => 'user.register', 'method' => 'post')) }}
		<div>
			{{ Form::label('username', 'Name:') }}
			{{ Form::text('username', old('username')) }}
		</div>
		<div>
			{{ Form::label('email', 'Email:') }}
			{{ Form::email('email', old('email')) }}
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
		@if (count($errors) > 0)
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		@endif
	{{ Form::close() }}

@endsection
