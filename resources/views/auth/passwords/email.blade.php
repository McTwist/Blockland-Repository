@extends('layouts.master')

@section('title', 'Forgotten Password')

@section('content')

	{{ Form::open(['route' => 'password.email', 'method' => 'post', 'class' => 'form-horizontal']) }}
	<fieldset class="blr-fieldset">
		<legend>Reset Password</legend>
		<hr>
		<div class="container-fluid">
			<div class="row">
				<!-- Email -->
				<div class="row form-group">
					<div class="col-sm-12 text-sm-left col-md-2 col-md-offset-2 hug-md-right">
						{{ Form::label('email', 'Account Email:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-sm-12 col-md-4">
						{{ Form::email('email','', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<!-- Reset button -->
				<div class="col-sm-12 form-group">
					{{ Form::submit('Send Password Reset Link', ['class' => 'btn blr-btn btn-blr-default']) }}
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
		</div>
	</fieldset>
	{{ Form::close() }}

@endsection
