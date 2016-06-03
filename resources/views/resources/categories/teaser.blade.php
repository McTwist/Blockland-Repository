<div class="category teaser">
	@if($category->icon !== null)
		<a href="{{ route('categories.show', $category->id) }}" style="background-image: url('/img/{{ $category->icon }}');">
	@else
		<a href="{{ route('categories.show', $category->id) }}">
	@endif

		<div class="category-name">
			{{ $category->name }}
		</div>
	</a>
</div>
