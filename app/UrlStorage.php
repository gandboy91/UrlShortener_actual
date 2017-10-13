<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UrlStorage extends Model
{
    protected $table = 'minUrl';
	protected $fillable = ['url','tstamp','slug']; 

    public $timestamps = false;
}
