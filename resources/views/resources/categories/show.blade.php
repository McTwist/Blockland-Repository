@extends('layouts.master')

@section('mainbox', 'main')

@section('scripts')
	<script type="text/javascript">
		window.jQuery(document).ready(function ($) {
			$(".linkrow").click(function () {
				window.document.location = $(this).data("href");
			});
		});
	</script>
@append

@section('content')
	<div class="container-fluid">
		<div class="row mar-xs-btm mar-sm-btm-none">
			<h1>{{ $category->name }}</h1>
		</div>
		{{-- TODO: Sorting. --}}
		{{-- TODO: This view needs more work. It's better than the one before but I'm not happy with it. -DW --}}
		<div class="row">
			@if(count($category->repositories) > 0)
				<table class="blr-table hidden-xs visible-sm visible-md visible-lg">
					<thead>
					<tr class="titlerow">
						<th>Name</th>
						<th>Authors</th>
						<th>Last Updated</th>
						<th>Downloads</th>
					</tr>
					<tr>
						<td colspan="4" class="divider">
							<hr class="mar-xs-vert-half"/>
						</td>
					</tr>
					</thead>
					<tbody>
					@foreach($category->repositories()->get() as $repo)
						<tr class="linkrow" data-href='{{ $repo->route }}'>
							<td>
								<table>
									<thead>
									<tr>
										<th class="text-xs-left">
											{{ $repo->name }}
										</th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td class="text-xs-left">
											{{ str_limit($repo->summary, $limit = 130, $end = '&hellip;') }}
										</td>
									</tr>
									</tbody>
								</table>
							</td>
							<td class="middle">
								{{ str_limit($repo->authors->implode('name', ', '), $limit = 26, $end = '&hellip;') }}
							</td>
							<td class="middle text-nowrap">
								{{ date('Y-m-d', $repo->updated_at->getTimestamp()) }}
							</td>
							<td class="middle">
								{{ number_format($repo->downloads, 0, ',', ' ') }}
							</td>
						</tr>
						@if (!($loop->last))
							<tr>
								<td colspan="4" class="divider">
									<hr class="mar-xs-vert-half"/>
								</td>
							</tr>
						@endif
					@endforeach
					</tbody>
				</table>
				{{-- A completely separate table for XS size.
					 I could put it all in the same table but it just made things confusing. --}}
				<table class="blr-table blr-table-xs visible-xs hidden-sm hidden-md hidden-lg text-xs-left">
					<tbody>
					@foreach($category->repositories()->get() as $repo)
						<tr class="linkrow" data-href='{{ $repo->route }}'>
							<td>
								<table>
									<thead>
									<tr>
										<th colspan="6" class="reponame">{{ $repo->name }}</th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td colspan="6">{{ str_limit($repo->summary, $limit = 140, $end = '&hellip;') }}</td>
									</tr>
									<tr class="footer-titles">
										<th>Authors:</th>
										<td>{{ str_limit($repo->authors->implode('name', ', '), $limit = 26, $end = '&hellip;') }}</td>
										<th>Last Updated:</th>
										<td>{{ date('Y-m-d', $repo->updated_at->getTimestamp()) }}</td>
										<th>Downloads:</th>
										<td class="text-xs-right">{{ number_format($repo->downloads, 0, ',', ' ') }}</td>
									</tr>
									</tbody>
								</table>
							</td>
						</tr>
						@if (!($loop->last))
							<tr>
								<td colspan="6" class="divider">
									<hr class="mar-xs-vert-half"/>
								</td>
							</tr>
						@endif
					@endforeach
					</tbody>
				</table>
			@else
				<p>This category has no add-ons.</p>
			@endif
		</div>
	</div>
@endsection
