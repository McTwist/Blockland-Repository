<!DOCTYPE html>
<html>
	<head>
		<!-- Favicons -->
		<link rel="icon" href="/favicon.ico">
		<link rel="icon" href="/favicon_192x192.png" sizes="192x192">
		<link rel="icon" href="/favicon_32x32.png" sizes="32x32">
		<link rel="icon" href="/favicon_16x16.png" sizes="16x16">

		<!-- Title -->
		<title>Blockland Repository - @yield('title')</title>
		
		<!-- Fonts -->
		<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>

		<!-- Stylesheets -->
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<link rel="stylesheet" type="text/css" href="/css/form.css">
		<link rel="stylesheet" type="text/css" href="/css/app.css">

		@yield('stylesheets')
	</head>

	<body>
		<div class="--center">
			@include('partials.header')

			<div id="@yield('mainbox', 'main')" class="container box" role="document">
				@include('partials.navigation')
				
				@yield('content', view('resources.categories.index'))

				@yield('footer', view('partials.footer'))
			</div>
		</div>
	</body>
</html>
