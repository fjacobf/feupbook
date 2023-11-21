<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Added to define Eloquent relationships.
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
   use HasFactory;

   // Don't add create and update timestamps in database.
   // public $timestamps  = false;

   protected $primaryKey = 'post_id';

   protected $fillable = ['content', 'owner_id'];

   /**
   * Get the user that owns the post.
   */
   public function user(): BelongsTo
   {
      return $this->belongsTo(User::class, 'owner_id', 'user_id');
   }
}
