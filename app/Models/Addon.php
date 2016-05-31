<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
	protected $hidden = ['id', 'category_id'];

	protected $fillable = ['name', 'slug', 'description'];

	public function owners()
	{
		return $this->belongsToMany(User::class)->withTimestamps();
	}

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function authors()
	{
		// TODO: Authors is mentioned in add-on
		// Note: This might return a string, but find a better way to use it
		return $this->owners()->get()->implode('name', ', ');
	}

	public function size()
	{
		return 0;
	}

	public function size_si()
	{
		return '0 KB';
	}

	public function size_bin()
	{
		return '0 KiB';
	}

	public function summary()
	{
		return '';
	}

	public function version()
	{
		return '';
	}

	public function downloads()
	{
		return 0;
	}

	public function uploader()
	{
		return '';
	}

	public function download_link()
	{
		return '';
	}
}
