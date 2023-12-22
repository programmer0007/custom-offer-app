<?php

namespace App\Models\shpyAuth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class storedetail extends Model
{
    use HasFactory;
    protected $table= 'storedetails';
    protected $fillable= [
        'url', 'accessToken'
    ];
    public $timestamps = false;
}
