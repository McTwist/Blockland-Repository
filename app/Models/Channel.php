<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
	protected $hidden = ['id'];

	protected $fillable = ['name', 'slug', 'description'];

	public function addon()
	{
		return $this->belongsTo(Addon::class);
	}

	public function restart_required()
	{
		return null;
	}
}
