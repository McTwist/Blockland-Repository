<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable as NotifiableTrait;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
	use AuthenticatableTrait;
	use CanResetPasswordTrait;
	use NotifiableTrait;

	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Attributes that are hidden.
	 *
	 * @var string
	 */
	protected $hidden = ['id', 'email', 'password'];

	/**
	 * The Attributes that are allowed to be Mass Assigned.
	 *
	 * @var array
	 */
	protected $fillable = ['username', 'email', 'password'];

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Repositories that this User has.
	 *
	 * @return Relationship
	 */
	public function repositories()
	{
		return $this->belongsToMany(Repository::class)->withTimestamps();
	}

	/**
	 * Returns the Channels that this User has.
	 *
	 * @return Relationship
	 */
	public function channels()
	{
		return $this->belongsToMany(Channel::class)->withTimestamps();
	}

	/**
	 * Returns the BlocklandUser that this User has.
	 *
	 * @return Relationship
	 */
	public function blockland_user()
	{
		return $this->hasOne(BlocklandUser::class, 'id', 'blockland_id');
	}

	/**
	 * Returns the Files that this User has uploaded.
	 *
	 * @return Relationship
	 */
	public function uploaded()
	{
		return $this->hasMany(File::class, 'uploader_id');
	}


	///////////////////////////
	//* Attribute Overrides *//
	///////////////////////////
	/**
	 * Gets the BL_ID belonging to this User.
	 *
	 * @return int
	 */
	public function getBlIdAttribute()
	{
		$blockland_user = $this->blockland_user;
		if (is_null($blockland_user))
			return null;
		else
			return $blockland_user->first()->id;
	}

	/**
	 * Get the BL Name belonging to this user
	 *
	 * @return string
	 */
	public function getBlNameAttribute()
	{
		$blockland_user = $this->blockland_user;
		if (is_null($blockland_user))
			return '';
		else
			return $blockland_user->first()->name;
	}
}
