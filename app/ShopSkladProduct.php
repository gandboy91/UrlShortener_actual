<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopSkladProduct extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_sklad_product';

	 /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

	public function sklad()
    {
        return $this->belongsTo('App\ShopSklad','sklad_id');
    }

}
