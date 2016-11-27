<link rel="stylesheet" type="text/css" href="/css/login.css">
<div id="login-box" class="popup-box">
	{{ Form::open(array('route' => 'user.login', 'method' => 'post', 'class' => 'form-horizontal')) }}
	<fieldset class="blr-fieldset">
		<legend>Login</legend>
		<hr>
		<div class="container-fluid">
			<div class="row login-spacer-neg">
				<!-- Username -->
				<div class="row form-group">
					<div class="col-sm-12 text-sm-left col-md-2">
						{{ Form::label('username', 'Username:', ['class' => 'control-label']) }}
					</div>
					<div class="col-sm-12 col-md-10">
						{{ Form::text('username', old('username'), ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<!-- Password -->
				<div class="row form-group">
					<div class="col-sm-12 text-sm-left col-md-2">
						{{ Form::label('password', 'Password:', ['class' => 'control-label']) }}
					</div>
					<div class="col-sm-12 col-md-10">
						{{ Form::password('password', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>
			</div>
			<div class="row">
				<!-- Elements in order for sm size! -->
				<div class="col-sm-12 col-md-6 col-md-push-6">
					<div class="row">
						<!-- TODO: Some position tweaking required. -->
						<!-- Remember me -->
						<div class="col-sm-6 hug-sm-right col-md-12 login-spacer">
							{{ Form::checkbox('remember', 'remember', true, ['id' => 'remember_chk']) }}
							{{ Form::label('remember_chk', 'Remember me', ['class' => 'checkbox-inline blr-checkbox']) }}
						</div>

						<!-- Close -->
						<div class="col-sm-3 hug-sm-right col-md-6 login-spacer">
							{{ Form::button('Close', ['onclick' => 'clearPopup()', 'class' => 'btn blr-btn btn-blr-close']) }}
						</div>

						<!-- Log in -->
						<div class="col-sm-3 hug-sm-right col-md-6 login-spacer">
							{{ Form::submit('Log In', ['class' => 'btn blr-btn btn-blr-default']) }}
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-md-6 col-md-pull-6">
					<div class="row">
						<!-- Forgot password -->
						<div class="col-sm-6 col-md-12 hug-md-left login-spacer">
							<a href="{{ route('password.email') }}" class="uppercase">Forgot password?</a>
						</div>

						<!-- Register -->
						<div class="col-sm-6 col-md-12 hug-md-left login-spacer">
							<a href="{{ route('user.register') }}" class="pull-left uppercase">Register an
								account</a>
						</div>
					</div>
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