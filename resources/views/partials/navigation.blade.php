<nav id="navigation" class="navigation" role="navigation">
	<ul class="nav-item-group">
		<li class="nav-item">
			<a href="{{ route('categories.index') }}">Categories</a>
		</li>

		@if(Auth::check())

			<li class="nav-item"><a href="{{ route('user.show') }}">Profile ({{ Auth::user()->username }})</a></li>
			<li class="nav-item"><a href="#" onclick="showUpload()">Upload</a></li>
			<li class="nav-item"><a href="{{ route('user.logout') }}">Log Out</a></li>

			@include('resources.addon.upload')

		@else

			<li class="nav-item"><a href="{{ route('user.login') }}">Log In</a></li>

		@endif
	</ul>
</nav>
<hr>