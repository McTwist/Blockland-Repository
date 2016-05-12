<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
	protected $hidden = ['id', 'category_id'];

	public function owners()
	{
		return $this->belongsToMany(User::class);
	}
}
