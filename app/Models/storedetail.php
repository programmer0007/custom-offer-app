<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class storedetail extends Model
{
    use HasFactory;

    protected $table = 'storedetails';
    public $timestamps = false;
    protected $fillable = [
        'url',
        'accesstoken'
    ];
}
