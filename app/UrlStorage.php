<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UrlStorage extends Model
{
	//наша таблица
    protected $table = 'minUrl';
	//открыли поля для массового заполнения
	protected $fillable = ['url','tstamp','slug']; 

    public $timestamps = false;
}
