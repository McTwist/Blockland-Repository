<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
	use AuthenticatableTrait;
	use CanResetPasswordTrait;

	protected $hidden = ['id', 'password'];

	protected $fillable = ['username', 'email', 'password'];

	public function addons()
	{
		return $this->belongsToMany(Addon::class)->withTimestamps();
	}
}
