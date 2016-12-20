@extends('layouts.master')

@section('title', 'Edit ' . $save->name)

@section('mainbox', 'edit')

@section('content')
	<div class="container-fluid">
		{{ Form::open(['route' => ['save.update', $save->slug], 'method' => 'put','files' => true, 'class' => 'form-horizontal']) }}
		<fieldset class="blr-fieldset">
			<legend>Edit Save</legend>
			<div class="row">
				{{-- Category --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('category', 'Category:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::select('category', $categories, $save->category_id, ['disabled', 'required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Title --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('title', 'Title:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::text('title', $save->name, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Summary --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('summary', 'Summary:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::text('summary', $save->summary, ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Authors --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('authors', 'Authors:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::text('authors', $save->authors->implode('name', ', '), ['required' => 'true', 'class' => 'form-control blr-form-control']) }}
					</div>
				</div>

				{{-- Description --}}
				<div class="row form-group">
					<div class="col-xs-12 text-xs-left col-sm-2 hug-sm-right">
						{{ Form::label('description', 'Description:', ['class' => 'control-label control-label-blr']) }}
					</div>
					<div class="col-xs-12 col-sm-8">
						{{ Form::textarea('description', $save->description, ['class' => 'form-control blr-form-control']) }}
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
					<div class="col-xs-12 mar-xs-top col-sm-4 col-sm-push-6">
						{{ Form::submit('Update', ['class' => 'btn blr-btn btn-blr-default width-xs-full float-sm-right width-sm-auto']) }}
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
		<div class="row mar-xs-top extend-xs-double mar-sm-top-none">
			{{-- Delete --}}
			<div class="col-xs-12">
				{{ Form::open(['route' => ['save.destroy', $save->slug], 'method' => 'delete', 'class' => 'form-horizontal']) }}
				{{ Form::submit('Delete Save', ['class' => 'btn blr-btn btn-blr-red width-xs-full float-sm-left width-sm-auto uppercase vpull-btn-sm-up']) }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
@endsection
