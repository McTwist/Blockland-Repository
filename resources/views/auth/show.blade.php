@extends('layouts.master')

@section('title', $user->username)

@section('content')
	<div class="container-fluid">
		<div class="row">
			<h1 class="title mar-xs-btm-half">
				{{ $user->username }}
			</h1>
		</div>
		<div class="row">
			@if(Auth::id() === $user->id)
				<div class="col-xs-12 nopad-xs-both mar-xs-btm col-sm-4 col-md-3 pull-right">
					<a class="btn blr-btn btn-blr-default width-xs-full width-sm-auto pull-right"
					   href="{{ route('user.edit') }}">Change Account Settings</a>
				</div>
			@endif
			{{-- Repositories --}}
			<div class="col-xs-12 col-sm-6">
				@forelse($user->repositories as $repo)
					<div class="col-xs-12 text-xs-left col-sm-5 text-sm-right col-md-4 blr-label">
						<a href="{{ $repo->route }}"><div>{{ $repo->name }}</div></a>
					</div>
				@empty
					This user has no uploaded files.
				@endforelse
			</div>
			{{-- Blockland info --}}
			@if($user->bl_id != -1)
				<div class="col-xs-12 col-sm-6">
					<div class="row">
						<div class="col-xs-12 text-xs-left col-sm-5 text-sm-right col-md-4 blr-label">
							Blockland Name:
						</div>
						<div class="col-xs-12 col-sm-7 hug-sm-left col-md-8">
							<p class="text-xs-center text-sm-left blr-value">{{ $user->bl_name }}</p>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 text-xs-left col-sm-5 text-sm-right col-md-4 blr-label">
							Blockland ID:
						</div>
						<div class="col-xs-12 col-sm-7 hug-sm-left col-md-8">
							<p class="text-xs-center text-sm-left blr-value">{{ $user->bl_id }}</p>
						</div>
					</div>
				</div>
			@endif
		</div>
	</div>
@endsection
