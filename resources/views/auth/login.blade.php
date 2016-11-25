@extends('layouts.master')

@section('stylesheets')

    <link rel="stylesheet" type="text/css" href="/css/login.css">

@endsection

@section('content')

    <div id="login-box" class="box">
        {{ Form::open(array('route' => 'user.login', 'method' => 'post', 'class' => 'form-horizontal')) }}
        <fieldset class="blr-fieldset">
            <legend>Login</legend>
            <hr>
            <!-- Username -->
            <div class="form-group">
                {{ Form::label('username', 'Username:', ['class' => 'control-label nopad-l nopad-r col-xs-3']) }}
                <div class="col-xs-9">
                    {{ Form::text('username', old('username'), ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                {{ Form::label('password', 'Password:', ['class' => 'control-label nopad-l nopad-r col-xs-3']) }}
                <div class="col-xs-9">
                    {{ Form::password('password', ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
                </div>
            </div>

            <div class="col-xs-12 nopad-r nopad-l login-spacer">
                <!-- Forgot password & Remember me -->
                <div class="col-xs-6 nopad-l">
                    <a href="{{ route('password.email') }}" class="pull-left uppercase">Forgot password?</a>
                </div>

                <!-- Remember me -->
                <div class="col-xs-6 nopad-r">
                    {{ Form::checkbox('remember', 'remember', true, ['id' => 'remember_chk']) }}
                    {{ Form::label('remember_chk', 'Remember me', ['class' => 'checkbox-inline blr-checkbox pull-right']) }}
                </div>
            </div>

            <!-- Register -->
            <div class="col-xs-6 nopad-l">
                <a href="{{ route('user.register') }}" class="pull-left uppercase">Register an account</a>
            </div>

            <!-- Close -->
            <div class="col-xs-3">
                {{ Form::button('Close', ['class' => 'btn blr-btn btn-blr-close pull-right']) }}
            </div>

            <!-- Log in -->
            <div class="col-xs-3 nopad-r">
                {{ Form::submit('Log In', ['class' => 'btn blr-btn btn-blr-submit pull-right']) }}
            </div>
            @if (count($errors) > 0)
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </fieldset>
        {{ Form::close() }}
    </div>

@endsection
