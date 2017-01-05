@extends('layouts.master')

@section('title', 'Create Add-On')

@section('mainbox', 'upload')

@section('content')
	<div class="container-fluid">
		{{ Form::open(['route' => 'save.store', 'method' => 'post', 'files' => true, 'class' => 'form-horizontal']) }}
		<fieldset class="blr-fieldset">
			<legend>Create Save</legend>
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
						{{ Form::text('summary', null, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Authors --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('authors', 'Authors:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::text('authors', null, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Tags --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('tags', 'Tags:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::text('tags', null, ['class' => 'form-control blr-form-control tags']) }}
					</div>
				</div>

				{{-- Description --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('description', 'Description:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::textarea('description', null, ['class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<div class="row form-group">
					{{-- Channel --}}
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('channel', 'Channel:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-4">
						{{ Form::text('channel', null, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>

					{{-- Version --}}
					<div class="col-xs-12 text-xs-left col-sm-1 hug-sm-right">
						{{ Form::label('version', 'Version:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-3">
						{{ Form::text('version', null, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				@include('resources.save.corrections')

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
						{{ Form::submit('Create Save', ['class' => 'btn blr-btn btn-blr-default width-xs-full float-sm-right width-sm-auto']) }}
					</div>
				</div>

				@if (count($errors) > 0)
					<div class="row">
						<div class="col-xs-12">
							<ul>
								@foreach($errors->all() as $err)
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
