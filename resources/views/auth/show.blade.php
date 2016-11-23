
@extends('layouts.master')

@section('content')

	@if(Auth::id() === $user->id)

		<a href="{{ route('user.edit') }}">Edit</a>

	@endif

	Username: {{ $user->username }}

@endsection
