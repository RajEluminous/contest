<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prize';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'contest_id','name','aff_tools_link', 'contest_type', 'column', 'column_label_1', 'column_label_2', 'created_at', 'updated_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
