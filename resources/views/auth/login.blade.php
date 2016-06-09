@extends('layouts.master')

@section('stylesheets')
	
	<link rel="stylesheet" type="text/css" href="/css/login.css">

@endsection

@section('content')
	
	<div id="login" class="box">
		{{ Form::open(array('route' => 'user.login', 'method' => 'post')) }}
			<span class="title">Login</span>
			<hr>
			<div class="username">
				{{ Form::label('username', 'Username:') }}
				{{ Form::text('username') }}
			</div>
			<div class="password">
				{{ Form::label('password', 'Password:') }}
				{{ Form::password('password') }}
			</div>
			<div class="flowcontrol">
				<div class="forgot">
					<a href="#">Forgot password?</a>
				</div>
				<div class="remember">
					{{ Form::checkbox('remember', 'remember', true, array('id' => 'remember_chk')) }}
					{{ Form::label('remember_chk', 'Remember Me') }}
				</div>
			</div>
			<div>
				<div class="register">
					<a href="{{ route('user.register') }}">Register account</a>
				</div>
				<div class="close">
					<a href="#">Close</a>
				</div>
				<div class="login">
					{{ Form::submit('Log In') }}
				</div>
			</div>
			@if (count($errors) > 0)
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			@endif
		{{ Form::close() }}
	</div>

	@endif

@endsection
