<div class="row">
	<nav id="navigation" class="navigation col-xs-12" role="navigation">
		<div class="row">
			@if(Auth::check())
				<div class="col-sm-2">
					<input id="btn-new-addon" type="button" class="btn blr-btn btn-blr-default float-sm-left"
						   onclick="showUploadPopup()" value="Upload Add-On"/>
				</div>
				<div class="col-sm-10">
					<div class="row">
						<div class="col-xs-12 hug-xs-right">
							<p id="welcome-text">Welcome, {{ Auth::user()->username }}.</p>
						</div>
					</div>
					<div class="row">
						<ul class="nav-item-group col-xs-12">

							<li class="nav-item"><a href="{{ route('user.show') }}">My Profile</a></li>
							<li class="nav-item"><a href="{{ route('user.logout') }}">Log Out</a></li>
						</ul>
					</div>
				</div>
			@else
				<div class="col-sm-2  col-sm-offset-10">
					<input type="button" class="btn blr-btn btn-blr-default float-sm-right"
						   onclick="showLoginPopup()" value="Log In"/>
				</div>
			@endif
		</div>
	</nav>
</div>