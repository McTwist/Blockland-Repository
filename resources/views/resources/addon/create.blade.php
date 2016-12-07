@extends('layouts.master')

@section('title', 'Create Add-On')

@section('mainbox', 'upload')

@section('content')
	<div class="container-fluid">
		{{ Form::open(['route' => 'addon.store', 'method' => 'post', 'files' => true, 'class' => 'form-horizontal']) }}
		<fieldset class="blr-fieldset">
			<legend>Create Add-On</legend>
			<div class="row">
				{{-- Category --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('category', 'Category:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::select('category', $categories, $category, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Title --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('title', 'Title:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::text('title', $title, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Summary --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('summary', 'Summary:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::text('summary', $summary, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Authors --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('authors', 'Authors:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::text('authors', $developers, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Description --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('description', 'Description:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::textarea('description', $description, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<div class="row form-group">
					{{-- Channel --}}
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('channel', 'Channel:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-4">
						{{ Form::text('channel', $channel, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>

					{{-- Version --}}
					<div class="col-xs-12 text-xs-left col-sm-1 hug-sm-right">
						{{ Form::label('version', 'Version:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-3">
						{{ Form::text('version', $version, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Corrections --}}
				{{-- Check App\Validation\Rules for possible messages --}}
				@if (count($error) > 0)
					<h2>Warnings</h2>

					{{-- Invalid description --}}
					@if ($error->has('description_invalid'))
						<div class="row form-group">
							<div class="col-xs-12 col-sm-8 col-sm-offset-2">
								Description is required and will be created
							</div>
						</div>
					@endif
					{{-- Missing namecheck --}}
					@if ($error->has('namecheck_missing'))
						<div class="row form-group">
							<div class="col-xs-12 col-sm-8 col-sm-offset-2">
								{{ Form::checkbox('namecheck_missing', 'namecheck_missing', false, ['id' => 'namecheck_missing']) }}
								{{ Form::label('namecheck_missing', 'Create namecheck', ['class' => 'checkbox-inline blr-checkbox']) }}
							</div>
						</div>
					@endif
					{{-- Invalid namecheck --}}
					@if ($error->has('namecheck_invalid'))
						<div class="row form-group">
							<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
								{{ Form::label('namecheck_invalid', 'Fix namecheck:', ['class' => 'control-label control-label-blr']) }}
							</div>
							<div class="col-xs-12 col-sm-3">
								{{ Form::checkbox('namecheck_invalid', true, ['class' => 'checkbox-inline blr-checkbox float-sm-right']) }}
							</div>
						</div>
					@endif
					{{-- Invalid version --}}
					@if ($error->has('version_invalid'))
						<div class="row form-group">
							<div class="col-xs-12 col-sm-8 col-sm-offset-2">
								Version is invalid and needs to be corrected
							</div>
						</div>
					@endif
				@endif

				<h2>Screenshots</h2>

				<div class="row">
					<div class="col-xs-12 col-sm-8 col-sm-offset-2">
						{{-- TODO: Proper file input. --}}
						{{ Form::file('screenshot') }}
					</div>
				</div>

				<div class="row">
					{{-- Create --}}
					<div class="col-xs-12 col-sm-4 col-sm-push-6 mar-top">
						{{ Form::submit('Create Add-On', ['class' => 'btn blr-btn btn-blr-default width-xs-full float-sm-right width-sm-auto']) }}
					</div>
				</div>

				@if (count($errors) > 0)
					<div class="row">
						<div class="col-xs-12">
							<ul>
								@foreach($error->all() as $err)
									<li>{{ $err }}</li>
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
