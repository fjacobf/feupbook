<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowRequest extends Model
{
    use HasFactory;

    protected $table = 'follow_requests';

    protected $fillable = [
        'req_id',
        'rcv_id',
        'status',
    ];
}
