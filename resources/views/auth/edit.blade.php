@extends('layouts.master')

@section('title', 'Account Settings')

@section('mainbox', 'edit')

@section('scripts')
	<script type="text/javascript" src="/js/verify.js"></script>
@append

@section('stylesheets')
	<style type="text/css">
		.loader
		{
			display: inline-block;
			border: 6px solid #e9e9e9;
			border-radius: 50%;
			border-top: 6px solid #6b00b2;
			width: 20px;
			height: 20px;
			-webkit-animation: spin 1.2s linear infinite;
			animation: spin 1.2s linear infinite;
		}

		@-webkit-keyframes spin
		{
			0% { -webkit-transform: rotate(0deg); }
			100% { -webkit-transform: rotate(360deg); }
		}

		@keyframes spin
		{
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}

		.checkmark
		{
			display: inline-block;
			position: relative;
			top: -14px;
			width: 20px;
			height: 20px;
			-ms-transform: rotate(45deg); /* IE 9 */
			-webkit-transform: rotate(45deg); /* Chrome, Safari, Opera */
			transform: rotate(45deg);
		}
		
		.checkmark_stem
		{
			position: absolute;
			width: 4px;
			height: 16px;
			background-color: #6b00b2;
			left: 22px;
			top: 12px;
		}

		.checkmark_kick
		{
			position: absolute;
			width: 8px;
			height: 4px;
			background-color: #6b00b2;
			left: 16px;
			top: 24px;
		}

		.crossmark
		{
			display: inline-block;
			width: 20px;
			height: 20px;
			font-size: 20px;
			color: #ff0000;
		}
	</style>
@append

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

				{{-- Display name --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 col-sm-offset-1 hug-sm-right col-md-offset-2">
						{{ Form::label('displayname', 'Display name:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::text('displayname', $user->displayname, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
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
						{{ Form::number('id', $user->bl_id, ['id' => 'blockland_id', 'min' => 0, 'max' => 999999, 'autocomplete' => 'off', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Blockland name --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-3 hug-sm-right col-md-2 col-md-offset-2">
						{{ Form::label('name', 'Blockland Name:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						{{ Form::text('name', $user->bl_name, ['id' => 'blockland_name', 'autocomplete' => 'off', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Update button --}}
				<div class="row form-group">
					<div class="col-xs-12 col-sm-3 col-sm-offset-6 col-md-offset-5">
						{{-- Move this elsewhere. // Mc --}}
						<div class="loader" style="display: none;"></div>
						<div class="checkmark" style="display: none;"><div class="checkmark_stem"></div><div class="checkmark_kick"></div></div>
						<div class="crossmark" style="display: none;">&#10060;</div>
						{{ Form::submit('Update Account', ['class' => 'btn blr-btn btn-blr-default width-xs-full width-sm-auto float-sm-right']) }}
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<ul id="error-list">
							@if (count($errors) > 0)
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							@endif
						</ul>
					</div>
				</div>
			</div>
		</fieldset>
		{{ Form::close() }}
	</div>
@endsection
