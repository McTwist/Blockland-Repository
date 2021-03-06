<!DOCTYPE html>
<html>
<head>
	<!-- Favicons -->
	<link rel="icon" href="/favicon.ico">
	<link rel="icon" href="/favicon_192x192.png" sizes="192x192">
	<link rel="icon" href="/favicon_32x32.png" sizes="32x32">
	<link rel="icon" href="/favicon_16x16.png" sizes="16x16">

	<!-- Title -->
	<title>
		@hasSection('title')
			@yield('title') - Blockland Repository
		@else
			Blockland Repository
		@endif
	</title>

	<!-- Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>

	<!-- Stylesheets -->
	<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/css/app.css">

	@yield('stylesheets')
</head>

<body>
<div id="popup-box-wrapper" class="container container-table" style="display: none;">
	<div id="popup-box-container" class="row vertical-center-row"></div>
</div>
<div class="--center">
	@include('partials.header')

	<div id="@yield('mainbox', 'main')" class="container low-box" role="document">
		@include('partials.navigation')

		@yield('content', view('resources.categories.index'))

		@yield('footer', view('partials.footer'))
	</div>
</div>

@yield('scripts.footer')

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript">if (!window.jQuery) {
		document.write('<script src="/js/jquery.min.js"><\/script>');
	}</script>
<script type="text/javascript" src="/js/popup.js"></script>
@yield('scripts')
</body>
</html>
