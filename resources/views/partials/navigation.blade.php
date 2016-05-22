<nav id="navigation" class="navigation" role="navigation">
	<ul class="nav-item-group">
		<li class="nav-item">
			<a href="{{ route('categories.index') }}">Categories</a>
		</li>

		@if(Auth::check())

			<li class="nav-item"><a href="{{ route('addon.upload') }}">Upload</a></li>
			<li class="nav-item"><a href="{{ route('user.logout') }}">Log Out</a></li>

		@else

			<li class="nav-item"><a href="{{ route('user.register') }}">Register</a></li>
			<li class="nav-item"><a href="{{ route('user.login') }}">Log In</a></li>

		@endif
	</ul>
</nav>