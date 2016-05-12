<nav id="navigation" class="navigation" role="navigation">
	<ul class="nav-item-group">
		<li class="nav-item">
			<a href="{{ route('categories.index') }}">Categories</a>
		</li>

		@if(Auth::check())

			<li class="nav-item"><a href="#upload">Upload</a></li>
			<li class="nav-item"><a href="#logout">Log Out</a></li>

		@else

			<li class="nav-item"><a href="#register">Register</a></li>
			<li class="nav-item"><a href="#login">Log In</a></li>

		@endif
	</ul>
</nav>