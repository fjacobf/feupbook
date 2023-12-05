<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Post;
use App\Models\User;


class Bookmark extends Model
{
    use HasFactory;

    protected $table = 'bookmarks';
    protected $primaryKey = ['bookmarked_post', 'user_id'];
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'bookmarked_post',
        'user_id',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'bookmarked_post', 'post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
