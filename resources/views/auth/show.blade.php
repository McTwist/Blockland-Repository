@extends('layouts.master')

@section('title', $user->username)

@section('content')
	<div class="row pad-top">
		<div class="col-xs-12 col-sm-8">
			<p class="title">{{ $user->username }}</p>
		</div>
		@if(Auth::id() === $user->id)
			<div class="col-xs-12 col-sm-4">
				<a class="btn blr-btn btn-blr-default width-xs-full width-sm-auto pull-right"
				   href="{{ route('user.edit') }}">Change Account Settings</a>
			</div>
		@endif
	</div>
	<hr>
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="row">
					<div class="row">
						<div class="col-xs-12 text-xs-left col-sm-5 text-sm-right col-md-4">
							<p class="blr-label">Blockland Name:</p>
						</div>
						<div class="col-xs-12 col-sm-7 hug-sm-left col-md-8">
							<p class="blr-value">{{ $user->bl_name }}</p>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 text-xs-left col-sm-5 text-sm-right col-md-4">
							<p class="blr-label">Blockland ID:</p>
						</div>
						<div class="col-xs-12 col-sm-7 hug-sm-left col-md-8">
							<p class="blr-value">{{ $user->bl_id }}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
