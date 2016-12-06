@extends('layouts.master')

@section('title', 'Register Account')

@section('content')
	<div class="container-fluid">
		{{ Form::open(array('route' => 'user.register', 'method' => 'post', 'class' => 'form-horizontal')) }}
		<fieldset class="blr-fieldset">
			<legend>Register Account</legend>
			<div class="row">
				<!-- Username -->
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-3 hug-sm-right col-md-2 col-md-offset-2">
						{{ Form::label('username', 'Username:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::text('username', old('username'), ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<!-- Email -->
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-3 hug-sm-right col-md-2 col-md-offset-2">
						{{ Form::label('email', 'Email:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::email('email', old('email'), ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<!-- Password -->
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-3 hug-sm-right col-md-2 col-md-offset-2">
						{{ Form::label('password', 'Password:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::password('password', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<!-- Password confirmation -->
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-3 hug-sm-right col-md-2 col-md-offset-2">
						{{ Form::label('password_confirmation', 'Confirm Password:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::password('password_confirmation', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<!-- Register button -->
				<div class="row form-group">
					<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
						{{ Form::submit('Register Account', ['class' => 'btn blr-btn btn-blr-default width-xs-full float-sm-right width-sm-auto']) }}
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
		</fieldset>
		{{ Form::close() }}
	</div>
@endsection
