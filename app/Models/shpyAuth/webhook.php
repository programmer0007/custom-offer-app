<?php

namespace App\Models\shpyAuth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class webhook extends Model
{
    use HasFactory;
    protected $table= 'webhooks';
    protected $fillable= [
        'client_id', 'webhook_id', 'topic', 'address', 'response'
    ];
    public $timestamps = false;
}
