<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $primaryKey = 'post_id';

    protected $fillable = [
        'owner_id',
        'image',
        'content',
        'date',
    ];

    public function user(): BelongsTo
   {
      return $this->belongsTo(User::class, 'owner_id', 'user_id');
   }

