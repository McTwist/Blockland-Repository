<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlocklandUser extends Model
{
	protected $fillable = ['id', 'name'];

	public function user()
	{
		return $this->belongsTo(User::class, 'blockland_id');
	}
}
