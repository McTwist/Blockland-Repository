@extends('layouts.master')

@section('title', 'Register')

@section('content')
	{{ Form::open(array('route' => 'user.register', 'method' => 'post', 'class' => 'form-horizontal')) }}

	<fieldset class="blr-fieldset">
		<legend>Register</legend>
		<div class="container-fluid">
			<div class="row">
				<!-- Username -->
				<div class="row form-group">
					<div class="text-xs-left col-sm-12 text-sm-left col-md-2 col-md-offset-2 hug-md-right">
						{{ Form::label('username', 'Username:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-sm-12 col-md-4">
						{{ Form::text('username', old('username'), ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<!-- Email -->
				<div class="row form-group">
					<div class="text-xs-left col-sm-12 text-sm-left col-md-2 col-md-offset-2 hug-md-right">
						{{ Form::label('email', 'Email:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-sm-12 col-md-4">
						{{ Form::email('email', old('email'), ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<!-- Password -->
				<div class="row form-group">
					<div class="text-xs-left col-sm-12 text-sm-left col-md-2 col-md-offset-2 hug-md-right">
						{{ Form::label('password', 'Password:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-sm-12 col-md-4">
						{{ Form::password('password', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<!-- Password confirmation -->
				<div class="row form-group">
					<div class="text-xs-left col-sm-12 text-sm-left col-md-2 col-md-offset-2 hug-md-right">
						{{ Form::label('password_confirmation', 'Confirm Password:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-sm-12 col-md-4">
						{{ Form::password('password_confirmation', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<!-- Register button -->
				<div class="row form-group">
					<div class="col-sm-12 col-md-2 col-md-offset-6">
						{{ Form::submit('Register Account', ['class' => 'btn blr-btn btn-blr-default width-xs-full width-sm-auto float-md-right']) }}
					</div>
				</div>
				@if (count($errors) > 0)
					<div class="col-xs-12">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif
			</div>
		</div>
	</fieldset>
	{{ Form::close() }}

@endsection
