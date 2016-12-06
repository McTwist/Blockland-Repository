@extends('layouts.master')

@section('title', 'Forgotten Password')

@section('content')
	<div class="container-fluid">
		{{ Form::open(['route' => 'password.email', 'method' => 'post', 'class' => 'form-horizontal']) }}
		<fieldset class="blr-fieldset">
			<legend>Forgotten Password</legend>
			<div class="row">
				{{-- Email --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 col-sm-offset-1 hug-sm-right col-md-offset-2">
						{{ Form::label('email', 'Account Email:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::email('email','', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Reset button --}}
				<div class="row form-group">
					<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
						{{ Form::submit('Send Password Reset Link', ['class' => 'btn blr-btn btn-blr-default width-xs-full width-sm-auto float-sm-right']) }}
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
