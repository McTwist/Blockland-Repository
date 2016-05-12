<div class="category teaser">
	@if($category->icon !== null)
		<a href="/category/{{ $category->id }}" style="background-image: url('/img/{{ $category->icon }}');">
	@else
		<a href="/category/{{ $category->id }}">
	@endif

		<div class="category-name">
			{{ $category->name }}
		</div>
	</a>
</div>