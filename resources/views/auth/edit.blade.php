@extends('layouts.master')

@section('title', 'Account Settings')

@section('mainbox', 'edit')

@section('content')
	<div class="container-fluid">
		{{ Form::open(['route' => ['user.update'], 'method' => 'put', 'class' => 'form-horizontal']) }}
		<fieldset class="blr-fieldset">
			<legend>Account Settings</legend>
			<div class="row">
				{{-- Username --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 col-sm-offset-1 hug-sm-right col-md-offset-2">
						{{ Form::label('username', 'Username:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::text('username', $user->username, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Email --}}
				<div class="row form-group mar-xs-btm-double">
					<div class="col-xs-12 text-xs-left col-sm-2 col-sm-offset-1 hug-sm-right col-md-offset-2">
						{{ Form::label('email', 'Email:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::email('email', '', ['placeholder' => obfuscate_email($user->email), 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- TODO: Password change page. --}}

				<h2 class="mar-xs-btm text-xs-center">Linked Blockland Account</h2>

				<div class="row">
					<div class="col-xs-12">
						<p class="text-xs-justify">When changing your linked Blockland account, please open the game on
							the same machine you are
							viewing this page on and wait for it to authenticate before updating your profile.</p>
					</div>
				</div>

				{{-- Blockland ID --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 col-sm-offset-1 hug-sm-right col-md-offset-2">
						{{ Form::label('id', 'Blockland ID:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::number('id', $user->bl_id, ['id' => 'blockland_id', 'min'=>'1', 'max'=>'999999', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Blockland name --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-3 hug-sm-right col-md-2 col-md-offset-2">
						{{ Form::label('name', 'Blockland Name:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::text('name', $user->bl_name, ['id' => 'blockland_name', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Update button --}}
				<div class="row form-group">
					<div class="col-xs-12 col-sm-3 col-sm-offset-6 col-md-offset-5">
						{{ Form::submit('Update Account', ['class' => 'btn blr-btn btn-blr-default width-xs-full width-sm-auto float-sm-right']) }}
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
	</div>
@endsection
