<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    use HasFactory;

    protected $table = 'comment_likes';
    protected $primaryKey = ['user_id', 'comment_id'];
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'comment_id'
    ];

}
