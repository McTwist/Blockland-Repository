<link rel="stylesheet" type="text/css" href="/css/login.css">
<div id="login-box" class="popup-box">
	{{ Form::open(array('route' => 'user.login', 'method' => 'post', 'class' => 'form-horizontal')) }}
	<fieldset class="blr-fieldset">
		<legend>Login</legend>
		<hr>
		<div class="container-fluid">
			<div class="row login-spacer-neg">
				{{-- Username --}}
				<div class="row form-group">
					<div class="text-xs-left col-sm-12 col-md-2 hug-md-right">
						{{ Form::label('username', 'Username:', ['class' => 'control-label']) }}
					</div>
					<div class="col-sm-12 col-md-10">
						{{ Form::text('username', old('username'), ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Password --}}
				<div class="row form-group">
					<div class="text-xs-left col-sm-12 col-md-2 hug-md-right">
						{{ Form::label('password', 'Password:', ['class' => 'control-label']) }}
					</div>
					<div class="col-sm-12 col-md-10">
						{{ Form::password('password', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12 nopad-xs-both col-sm-6 col-sm-push-6 col-md-6 col-md-push-6">
					{{-- Bootstrap has a mobile-first ideology.
					The elements are ordered according to their xs size placements.
					CSS is used to move the elements to their laptop/desktop (sm/md) locations. --}}

					{{-- Remember me --}}
					<div class="col-xs-12 hug-xs-right login-spacer">
						{{ Form::checkbox('remember', 'remember', true, ['id' => 'remember_chk']) }}
						{{ Form::label('remember_chk', 'Remember me', ['class' => 'checkbox-inline blr-checkbox float-sm-right']) }}
					</div>

					{{-- Log in --}}
					<div class="col-xs-12 nopad-xs-both col-sm-6 col-sm-push-6 col-md-5 col-md-push-7 col-md-offset-0 login-spacer">
						{{ Form::submit('Log In', ['class' => 'btn blr-btn btn-blr-default width-xs-full float-sm-right width-md-auto']) }}
					</div>

					{{-- Close --}}
					<div class="col-xs-12 nopad-xs-both col-sm-6 col-sm-pull-6 col-md-5 col-md-pull-3 login-spacer">
						{{ Form::button('Close', ['onclick' => 'clearPopup()', 'class' => 'btn blr-btn btn-blr-close width-xs-full width-md-auto']) }}
					</div>
				</div>

				<div class="col-xs-12 nopad-xs-both col-sm-6 col-sm-pull-6 col-md-6 col-md-pull-6">
					{{-- Forgot password --}}
					<div class="col-xs-12 hug-sm-left login-spacer text-xs-center">
						<a class="blacklink" href="{{ route('password.email') }}" class="uppercase ">Forgot password?</a>
					</div>

					{{-- Register --}}
					<div class="col-xs-12 hug-sm-left login-spacer text-xs-center">
						<a class="blacklink" href="{{ route('user.register') }}" class="uppercase">Register an account</a>
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
		</div>
	</fieldset>
	{{ Form::close() }}
</div>