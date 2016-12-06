@extends('layouts.master')

@section('title', 'Reset Password')

@section('content')
	<div class="container-fluid">
		{{ Form::open(['route' => 'password.reset', 'method' => 'post', 'class' => 'form-horizontal']) }}
		<fieldset class="blr-fieldset">
			<legend>Reset Password</legend>
			{{ Form::hidden('token', $token) }}
			<div class="row">
				{{-- Email --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-3 hug-sm-right col-md-2 col-md-offset-2">
						{{ Form::label('email', 'Account Email:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::email('email','', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Password --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-3 hug-sm-right col-md-2 col-md-offset-2">
						{{ Form::label('password', 'Password:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::password('password', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Password confirmation --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-3 hug-sm-right col-md-2 col-md-offset-2">
						{{ Form::label('password_confirmation', 'Confirm Password:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::password('password_confirmation', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Reset button --}}
				<div class="row form-group">
					<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
						{{ Form::submit('Reset Password', ['class' => 'btn blr-btn btn-blr-default width-xs-full width-sm-auto float-sm-right']) }}
					</div>
				</div>
			</div>

			@if (count($errors) > 0)
				<div class="row">
					<div class="col-xs-12">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				</div>
			@endif
		</fieldset>
		{{ Form::close() }}
	</div>
@endsection
