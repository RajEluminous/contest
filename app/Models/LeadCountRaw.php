<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadCountRaw extends Model
{
    protected $table = 'lead_counts_raw';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contest_id','cb_account','affiliate_id','email',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
