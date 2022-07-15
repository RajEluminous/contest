<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClickbankProduct extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'clickbank_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'clickbank_account_id', 'product_id', 'name', 'created_by', 'updated_by'];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
