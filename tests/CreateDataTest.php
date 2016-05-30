<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateDataTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testCreateModels()
	{
		$max_categories = 10;
		$max_users = 10;
		$max_addons_per_user = 5;
		// Create categories
		$categories = factory(App\Models\Category::class, $max_categories)->create();
		// Create users
		$users = factory(App\Models\User::class, $max_users)->create()->each(function($u) use ($max_addons_per_user, $max_categories, &$categories) {
			// Create addons
			$u->addons()->saveMany(factory(App\Models\Addon::class, mt_rand(0, $max_addons_per_user))->create()->each(function($a) use ($max_categories, &$categories) {
				// Attach category to addon
				$categories->random()->save([$a]);
			}));
		});

	    $this->assertTrue(true);
	}
}
