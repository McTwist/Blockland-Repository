{{-- Corrections --}}
{{-- Check App\Validation\Rules for possible messages --}}
@if (count($error) > 0)
	<h2>Warnings</h2>

	{{-- Invalid description --}}
	@if ($error->has('info_invalid'))
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
				Version is invalid and needs to be corrected
			</div>
		</div>
	@endif
@endif
