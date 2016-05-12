<!DOCTYPE html>
<html>
	<head>
		<title>Blockland Repository</title>
		<!-- Fonts -->
		<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>
		<!-- Stylesheets -->
		<!--link rel="stylesheet" type="text/css" href="/css/main.css">
		<link rel="stylesheet" type="text/css" href="/css/form.css">
		<link rel="stylesheet" type="text/css" href="/css/front.css"-->
	</head>
	<body>
		<div id="root">
			<div id="logo">
				<a href="/"><img src="/img/blockland_repository.png"></a>
			</div>
			<div id="main" class="box">
				<div id="navigation">
					<!--?php include 'inc/navigation.php'; ?-->
				</div>
				<div id="categories">

					@foreach($categories as $category)
						@if($category->icon !== null)
							<a href="category.php?id={{$category->id}" style="background-image: url('/img/{{$category->icon}}');"><div>{{$category->name}}</div></a>
						@elseif
							<a href="category.php?id={{$category->id}"><div>{{$category->name}}</div></a>
						@endif
					@endforeach

				</div>
				<hr>
				<div id="footer">
					<!--?php include 'inc/footer.php'; ?-->
				</div>
			</div>
		</div>
	</body>
</html>