<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	protected $hidden = ['id', 'password'];

	public function addons()
	{
		return $this->belongsToMany(Addon::class);
	}
}
