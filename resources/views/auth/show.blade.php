@extends('layouts.master')

@section('title', $user->username)

@section('content')

	@if(Auth::id() === $user->id)

		<a href="{{ route('user.edit') }}">Edit</a>

	@endif

	Username: {{ $user->username }}

@endsection
