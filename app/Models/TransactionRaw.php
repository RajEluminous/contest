<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionRaw extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transactions_raw';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'contest_id','receipt', 'item_no','transaction_time', 'transaction_type', 'vendor', 'affiliate', 'quantity', 'account_amount', 'customer_amount', 'created_at', 'updated_at'];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
