<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlStorage extends Model
{
    protected $table = 'minUrl';
	protected $fillable = ['url','tstamp']; 
    public $timestamps = false;
}
