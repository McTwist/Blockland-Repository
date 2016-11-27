{{-- TODO: Make the last row of icons centered on sm scale --}}
<div class="category teaser col-sm-3 col-md-2 float-md-none">
	@php($imgpath = $category->icon == null ? '/img/category_unknown.png' : "/img/$category->icon")
	<a href="{{ route('categories.show', $category->id) }}" style="background-image: url('{{ $imgpath }}')">
		<div class="category-name">{{ $category->name }}</div>
	</a>
</div>
