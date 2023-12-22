<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shopifysession extends Model
{
    use HasFactory;

    protected $table = 'shopifysession';
    public $timestamps = false;
    protected $fillable = [
        'shopify_shop',
        'shopify_token',
        'is_active'
    ];
}