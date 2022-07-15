<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'contests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'contest_type', 'contest_result_places', 'start_date', 'end_date', 'is_display_counter_timer', 'action_after_countdown_expire', 'action_after_countdown_expire_value', 'is_display_revenue', 'is_display_total_sale', 'display_sales_result', 'display_leads_result', 'status', 'created_by', 'updated_by'];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
