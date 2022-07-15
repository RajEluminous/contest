<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContestCbProducts extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'contest_cb_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'contest_id', 'clickbank_account', 'clickbank_product_ids', 'include_rebill', 'flag_contest_type', 'created_by', 'updated_by'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
