
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));

const app = new Vue({
	el: '#app'
});

/**
 * Selectize
 */

require('selectize');

$(document).ready(function() {
	$('.tags').selectize({
		plugins: ['remove_button'],
		delimiter: ',',
		persist: false,
		valueField: 'tag',
		labelField: 'tag',
		searchField: 'tag',
		create: function(input) {
			return {
				tag: input
			}
		},
		load: function(query, callback) {
			if (!query.length) return callback();
			$.ajax({
				url: '/tags/' + encodeURIComponent(query),
				type: 'GET',
				error: function() {
					callback();
				},
				success: function(res) {
					callback(res.tags.slice(0, 10).map(function(val) {
						return { tag: val };
					}));
				}
			});
		}
	});
	$('.authors').selectize({
		plugins: ['remove_button'],
		delimiter: ',',
		persist: false,
		valueField: 'author',
		labelField: 'author',
		searchField: 'author',
		create: function(input) {
			return {
				author: input
			}
		},
		load: function(query, callback) {
			if (!query.length) return callback();
			$.ajax({
				url: '/authors/' + encodeURIComponent(query),
				type: 'GET',
				error: function() {
					callback();
				},
				success: function(res) {
					callback(res.authors.slice(0, 10).map(function(val) {
						return { author: val };
					}));
				}
			});
		}
	});
});
