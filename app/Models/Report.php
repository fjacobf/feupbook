<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $primaryKey = 'report_id';

    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'user_id',
        'post_id',
        'date',
        'report_type'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
}
