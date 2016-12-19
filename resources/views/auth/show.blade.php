@extends('layouts.master')

@section('title', $user->displayname)

@section('content')
	<div class="container-fluid">
		<div class="row">
			<h1 class="mar-xs-btm-half">
				{{ $user->displayname }}
			</h1>
		</div>
		<div class="row">
			{{-- Blockland info --}}
			@if($user->bl_id !== null)
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
			@if(Auth::id() === $user->id)
				<div class="col-xs-12 nopad-xs-both mar-xs-btm col-sm-4 col-md-3 pull-right">
					<a class="btn blr-btn btn-blr-default width-xs-full width-sm-auto pull-right"
					   href="{{ route('user.edit') }}">Change Account Settings</a>
				</div>
			@endif
		</div>
		<div class="row">
			<h2 class="mar-xs-btm-half">Repositories</h2>
		</div>
		<div class="row">
			<div class="col-xs-12 hug-xs-left">
				@if(count($user->repositories) > 0)
					<ul class="user-repo-list">
						@foreach($user->repositories as $repo)
							<li><a href="{{ $repo->route }}"><span class="blr-label">{{ $repo->name }}:</span>
									@if(!empty($repo->summary))
										<span class="blr-value"> {{ $repo->summary }}</span>
									@else
										<span class="blr-value --italic"> No summary.</span>
									@endif</a></li>
						@endforeach
					</ul>
				@else
					<p>{{ $user->username }} has no uploaded files.</p>
				@endif
			</div>
		</div>
	</div>
@endsection
