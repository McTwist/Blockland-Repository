<!DOCTYPE html>
<html>
	<head>
		<title>Blockland Repository - @yield('title')</title>
		<!-- Fonts -->
		<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>

		<!-- Stylesheets -->
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<link rel="stylesheet" type="text/css" href="/css/form.css">
		<link rel="stylesheet" type="text/css" href="/css/front.css">

		@yield('stylesheets')
	</head>

	<body>
		<div id="root">
			@include('partials.header')

			<div id="@yield('mainbox', 'main')" class="box" role="document">
				@include('partials.navigation')
				
				@yield('content')

				@yield('footer')
			</div>
		</div>
	</body>
</html>
