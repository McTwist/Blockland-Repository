<nav id="navigation" class="navigation" role="navigation">
	<a href="/">Categories</a>
	<span>&middot;</span>

	@if(Auth::check())

		<a href="#upload">Upload</a>
		<span>&middot;</span>
		<a href="#logout">Log Out</a>

	@else

		<a href="#register">Register</a>
		<span>&middot;</span>
		<a href="#login">Log In</a>

	@endif
</nav>