

@extends('layouts.master')

@section('stylesheets')

@append

@section('mainbox', 'edit')

@section('content')

	<span class="title">Edit User</span>
	<hr>

	{{ Form::open(['route' => ['user.update'], 'method' => 'put']) }}
		<div class="user_name">
			{{ Form::Label('username', 'Username:') }}
			{{ Form::text('username', $user->username) }}
		</div>
		<div class="user_email">
			{{ Form::label('email', 'Email:') }}
			{{ Form::email('email', '', ['placeholder' => obfuscate_email($user->email)]) }}
		</div>
		{{--<div class="user_bl_id">
			{{ Form::Label('bl_id', 'Blockland ID:') }}
			{{ Form::number('bl_id', '') }}
			{{ Form::button('Verify', ['onclick' => 'alert("YES")']) }}
			{{ Form::text('bl_name', '', ['readonly']) }}
		</div>--}}
		<hr class="over">
		<br>
		<div class="upload">
			{{ Form::submit('Update') }}
		</div>
	{{ Form::close() }}

@endsection
