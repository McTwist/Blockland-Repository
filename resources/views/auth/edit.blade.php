

@extends('layouts.master')

@section('stylesheets')

@append

@section('scripts')

	<script type="text/javascript" src="/js/verify.js"></script>

@append

@section('mainbox', 'edit')

@section('content')

	<span class="title">Edit User</span>
	<hr>

	{{ Form::open(['route' => ['user.update'], 'method' => 'put']) }}
		<div class="user_name">
			{{ Form::label('username', 'Username:') }}
			{{ Form::text('username', $user->username) }}
		</div>
		<div class="user_email">
			{{ Form::label('email', 'Email:') }}
			{{ Form::email('email', '', ['placeholder' => obfuscate_email($user->email)]) }}
		</div>
		<div class="user_bl_id">
			{{ Form::label('bl_id', 'Blockland ID:') }}
			{{ Form::number('bl_id', $user->bl_id, ['id' => 'blockland_id', 'style' => 'width: 80px']) }}
			{{ Form::label('bl_name', 'Blockland Name:') }}
			{{ Form::text('bl_name', $user->bl_name, ['id' => 'blockland_name', 'style' => 'width: 100px']) }}
			{{ Form::button('Verify', ['onclick' => 'verify_ip()']) }}
			{{ Form::text('verify', '', ['readonly', 'id' => 'blockland_msg']) }}
		</div>
		<hr class="over">
		<br>
		<div class="upload">
			{{ Form::submit('Update') }}
		</div>
	{{ Form::close() }}

@endsection
