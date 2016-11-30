{{-- TODO: Make the last row of icons centered on sm scale --}}
<div class="category teaser col-xs-6 col-sm-4 col-md-3">
	@php($imgpath = $category->icon == null ? '/img/category_unknown.png' : "/img/$category->icon")
	<a href="{{ route('categories.show', $category->id) }}" style="background-image: url('{{ $imgpath }}')">
		<div class="category-name">{{ $category->name }}</div>
	</a>
</div>
