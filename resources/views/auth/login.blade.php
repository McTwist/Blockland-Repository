@extends('layouts.master')

@section('title', 'Login')

@section('stylesheets')
	<link rel="stylesheet" type="text/css" href="/css/login.css">
@endsection

@section('content')
	{{-- Login page is a special case where the navigation bar is not present --}}
	<div class="container-fluid" style="margin-top: 1.145em">
		{{ Form::open(array('route' => 'user.login', 'method' => 'post', 'class' => 'form-horizontal')) }}
		<fieldset class="blr-fieldset">
			<legend>Login</legend>
			<div class="row">
				{{-- Username --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 col-sm-offset-1 hug-sm-right col-md-1 col-md-offset-3">
						{{ Form::label('username', 'Username:', ['class' => 'control-label']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::text('username', old('username'), ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Password --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 col-sm-offset-1 hug-sm-right col-md-1 col-md-offset-3">
						{{ Form::label('password', 'Password:', ['class' => 'control-label']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::password('password', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>
			</div>

			<div class="row extend-xs-double">
				<div class="col-xs-12 nopad-xs-both col-sm-4 col-sm-push-5 col-md-2 col-md-push-6">
					{{-- Bootstrap has a mobile-first ideology.
					The elements are ordered according to their xs size placements.
					CSS is used to move the elements to their laptop/desktop (sm/md) locations. --}}

					{{-- Remember me --}}
					<div class="col-xs-12 mar-xs-top">
						{{ Form::checkbox('remember', 'remember', true, ['id' => 'remember_chk']) }}
						{{ Form::label('remember_chk', 'Remember me', ['class' => 'float-sm-right --no-select']) }}
					</div>

					{{-- Log in --}}
					<div class="col-xs-12 mar-xs-top col-sm-6 col-sm-offset-6 col-md-12 col-md-offset-0">
						{{ Form::submit('Log In', ['class' => 'btn blr-btn btn-blr-default width-xs-full float-sm-right width-md-auto']) }}
					</div>
				</div>

				<div class="col-xs-12 nopad-xs-both col-sm-4 col-sm-pull-3 col-md-3 col-md-pull-0 col-md-push-1">
					{{-- Forgot password --}}
					<div class="col-xs-12 mar-xs-top text-xs-center text-sm-left">
						<a href="{{ route('password.email') }}" class="blacklink uppercase ">Forgot password?</a>
					</div>

					{{-- Register --}}
					<div class="col-xs-12 mar-xs-top text-xs-center text-sm-left">
						<a href="{{ route('user.register') }}" class="blacklink uppercase">Register an account</a>
					</div>
				</div>
			</div>

			{{-- TODO: Prettier error messages. --}}
			@if (count($errors) > 0)
				<div class="col-xs-12">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
		</fieldset>
		{{ Form::close() }}
	</div>
@endsection

