@extends('layouts.master')

@section('title', 'Edit ' . $addon->name)

@section('mainbox', 'edit')

@section('content')
	<div class="container-fluid">
		{{ Form::open(['route' => ['addon.update', $addon->slug], 'method' => 'put','files' => true, 'class' => 'form-horizontal']) }}
		<fieldset class="blr-fieldset">
			<legend>Edit Add-On</legend>
			<div class="row">
				{{-- Category --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('category', 'Category:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::select('category', $categories, $addon->category_id, ['disabled', 'required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Title --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('title', 'Title:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::text('title', $addon->name, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Summary --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('summary', 'Title:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::text('summary', $addon->summary, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Authors --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('authors', 'Authors:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::text('authors', $addon->authors, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Description --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('description', 'Description:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::textarea('description', $addon->description, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				<h2>Screenshots</h2>

				<div class="row">
					<div class="col-xs-12 col-sm-8 col-sm-offset-2">
						{{-- TODO: Proper file input. --}}
						{{ Form::file('screenshot') }}
					</div>
				</div>

				<div class="row">
					{{-- Update --}}
					<div class="col-xs-12 col-sm-4 col-sm-push-6 mar-top">
						{{-- FIXME: Updating an add-on actually deletes the add-on. --}}
						{{ Form::submit('Update', ['class' => 'btn blr-btn btn-blr-default width-xs-full float-sm-right width-sm-auto']) }}
					</div>

					{{-- Delete --}}
					<div class="col-xs-12 col-sm-4 col-sm-pull-2 mar-top">
						{{ Form::open(['route' => ['addon.destroy', $addon->slug], 'method' => 'delete', 'class' => 'form-horizontal']) }}
						{{-- TODO: Red button. --}}
						{{ Form::submit('Delete Add-On', ['class' => 'btn blr-btn btn-blr-red width-xs-full float-sm-left width-sm-auto uppercase']) }}
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</fieldset>
		{{ Form::close() }}
	</div>
@endsection
