<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringApplicationCharge extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'recurring_application_charge';
    public $timestamps = false;
    protected $fillable = [
        'client_id',
        'charge_id',
        'name',
        'price',
        'response',
        'is_approve',
        'is_active'
    ];
}
