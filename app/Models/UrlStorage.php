<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UrlStorage
 * @package App\Models
 */
class UrlStorage extends Model
{
    protected $table = 'minUrl';
    /**
     * @var array - fields avaliable for multiple insert 
     */
    protected $fillable = ['url','tstamp']; 
    public $timestamps = false;
}
