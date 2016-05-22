<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model implements AuthenticatableContract
{
	use AuthenticatableTrait;

	protected $hidden = ['id', 'password'];

	protected $fillable = ['username', 'email', 'password'];

	public function addons()
	{
		return $this->belongsToMany(Addon::class);
	}
}
