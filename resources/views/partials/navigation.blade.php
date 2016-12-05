{{-- Currently there is nothing to show on the navigation bar on the login page so skip the bar entirely. --}}
@if(!Request::is('user/login'))
	<div class="row mar-bottom">
		<nav id="navigation" class="navigation col-xs-12" role="navigation">
			<div class="row">
				@if(Auth::check())
					<div class="col-xs-12 col-sm-9 col-sm-push-3 col-md-10 col-md-push-2">
						<div class="row">
							<div class="col-xs-12">
								<div class="text-xs-center text-sm-right">Welcome, {{ Auth::user()->username }}.
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 text-xs-center text-sm-right">
								<ul class="nav-item-group">
									<li class="nav-item"><a class="blacklink" href="{{ route('user.show') }}">My Profile</a></li>
									<li class="nav-item"><a class="blacklink" href="{{ route('user.logout') }}">Log Out</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3 col-sm-pull-9 col-md-2 col-md-pull-10">
						<a href="{{ route('addon.upload') }}" id="btn-new-addon"
						   class="btn blr-btn btn-blr-default width-xs-full width-sm-auto float-sm-left">Upload
							Add-On</a>
					</div>
				@else
					<div class="col-sm-2 col-sm-offset-10">
						<input type="button" class="btn blr-btn btn-blr-default float-sm-right"
							   onclick="showLoginPopup()" value="Log In"/>
					</div>
				@endif
			</div>
		</nav>
	</div>
	<hr class="fullwidth separator-nav">
@endif
