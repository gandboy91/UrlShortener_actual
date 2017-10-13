<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopProducts extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_products';

	 /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
	public function country()
    {
        return $this->belongsTo('App\ShopCountry','country_id');
    }
	public function sklads()
    {
        return $this->hasMany('App\ShopSkladProduct','product_id');
    }
	public function images()
    {
        return $this->hasMany('App\CmsMedia','product_id');
    }
}
