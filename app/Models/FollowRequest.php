<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowRequest extends Model
{
    use HasFactory;

    protected $table = 'follow_requests';
    protected $primaryKey = ['req_id', 'rcv_id'];
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'req_id',
        'rcv_id',
        'date',
        'status',
    ];

    
}
