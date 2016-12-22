<?php

// Home page
Breadcrumbs::register('home', function($breadcrumbs)
{
	$breadcrumbs->push('Home', route('pages.home'));
});

// Category
Breadcrumbs::register('category', function($breadcrumbs, $category)
{
	$breadcrumbs->parent('home');
	$breadcrumbs->push($category->name, route('categories.show', $category->id));
});

// Repository
Breadcrumbs::register('repo', function($breadcrumbs, $repo)
{
	$breadcrumbs->parent('category', $repo->category);
	$breadcrumbs->push($repo->name, $repo->route);
});

// User page
Breadcrumbs::register('user', function($breadcrumbs, $user)
{
	$breadcrumbs->parent('home');
	$breadcrumbs->push($user->displayname, route('user.show', $user->id));
});
