<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamAffiliate extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'team_affiliates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'team_id', 'affiliate_id'];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
