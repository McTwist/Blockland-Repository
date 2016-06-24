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

	protected $hidden = ['id', 'email', 'password'];

	protected $fillable = ['username', 'email', 'password'];

	public function addons()
	{
		return $this->belongsToMany(Addon::class)->withTimestamps();
	}

	public function blockland_user()
	{
		return $this->hasMany(BlocklandUser::class, 'id', 'blockland_id');
	}

	public function bl_id()
	{
		$blockland_user = $this->blockland_user();
		if (is_null($blockland_user))
			return '';
		else
			return $blockland_user->first()->id;
	}

	public function bl_name()
	{
		$blockland_user = $this->blockland_user();
		if (is_null($blockland_user))
			return '';
		else
			return $blockland_user->first()->name;
	}
}
