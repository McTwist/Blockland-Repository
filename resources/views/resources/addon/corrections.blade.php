{{-- Corrections --}}
{{-- Check App\Validation\Rules for possible messages --}}
@if (count($error) > 0)
	<h2>Warnings</h2>

	{{-- File removals --}}
	@if ($error->has('file_removals'))
		<div class="row form-group">
			<div class="col-xs-12 col-sm-8 col-sm-offset-2">
				{{ Form::checkbox('file_removals', 1, true, ['id' => 'file_removals']) }}
				{{ Form::label('file_removals', 'Remove unnecessary files', ['class' => 'checkbox-inline blr-checkbox']) }}
			</div>
		</div>
	@endif
	{{-- Missing info --}}
	@if ($error->has('info_missing'))
		<div class="row form-group">
			<div class="col-xs-12 col-sm-8 col-sm-offset-2">
				{{ $error->first('info_missing') }}
			</div>
		</div>
	@endif
	{{-- Invalid info --}}
	@if ($error->has('info_invalid'))
		<div class="row form-group">
			<div class="col-xs-12 col-sm-8 col-sm-offset-2">
				{{ $error->first('info_invalid') }}
			</div>
		</div>
	@endif
	{{-- Missing namecheck --}}
	@if ($error->has('namecheck_missing'))
		<div class="row form-group">
			<div class="col-xs-12 col-sm-8 col-sm-offset-2">
				{{ Form::checkbox('namecheck_missing', 1, false, ['id' => 'namecheck_missing']) }}
				{{ Form::label('namecheck_missing', 'Create namecheck', ['class' => 'checkbox-inline blr-checkbox']) }}
			</div>
		</div>
	@endif
	{{-- Invalid namecheck --}}
	@if ($error->has('namecheck_invalid'))
		<div class="row form-group">
			<div class="col-xs-12 col-sm-8 col-sm-offset-2">
				{{ Form::checkbox('namecheck_invalid', 1, true, ['id' => 'namecheck_invalid']) }}
				{{ Form::label('namecheck_invalid', 'Fix namecheck', ['class' => 'checkbox-inline blr-checkbox']) }}
			</div>
		</div>
	@endif
	{{-- Invalid version --}}
	@if ($error->has('version_invalid'))
		<div class="row form-group">
			<div class="col-xs-12 col-sm-8 col-sm-offset-2">
				{{ $error->first('version_invalid') }}
			</div>
		</div>
	@endif
@endif
