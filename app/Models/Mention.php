<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
    use HasFactory;
    protected $table = 'mentions';
    protected $primaryKey = ['post_id', 'user_mentioned'];
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['post_id', 'user_mentioned'];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_mentioned');
    }
}
