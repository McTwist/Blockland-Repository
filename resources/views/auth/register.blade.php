@extends('layouts.master')

@section('content')
	{{ Form::open(array('route' => 'user.register', 'method' => 'post', 'class' => 'form-horizontal')) }}

	<fieldset class="blr-fieldset">
		<legend>Register</legend>
		<hr>
		<!-- Username -->
		<div class="form-group">
			{{ Form::label('username', 'Username:', ['class' => 'control-label control-label-blr nopad-r col-xs-4']) }}
			<div class="col-xs-4">
				{{ Form::text('username', old('username'), ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
			</div>
		</div>

		<!-- Email -->
		<div class="form-group">
			{{ Form::label('email', 'Email:', ['class' => 'control-label control-label-blr nopad-r col-xs-4']) }}
			<div class="col-xs-4">
				{{ Form::email('email', old('email'), ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
			</div>
		</div>

		<!-- Password -->
		<div class="form-group">
			{{ Form::label('password', 'Password:', ['class' => 'control-label control-label-blr nopad-r col-xs-4']) }}
			<div class="col-xs-4">
				{{ Form::password('password', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
			</div>
		</div>

		<!-- Password confirmation -->
		<div class="form-group">
			{{ Form::label('password_confirmation', 'Confirm Password:', ['class' => 'control-label control-label-blr nopad-r col-xs-4']) }}
			<div class="col-xs-4">
				{{ Form::password('password_confirmation', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
			</div>
		</div>

		<!-- Register button -->
		<div class="form-group">
			<div class="col-xs-4 col-xs-offset-4">
				{{ Form::submit('Register', ['class' => 'btn  blr-btn btn-blr-submit pull-right']) }}
			</div>
		</div>
		@if (count($errors) > 0)
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		@endif
	</fieldset>
	{{ Form::close() }}

@endsection
