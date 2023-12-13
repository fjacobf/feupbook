<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'date',
        'notified_user',
        'message',
        'notification_type',
        'comment_id',
        'post_id',
        'group_id',
        'user_id',
        'viewed',
    ];

    public $timestamps = false;

    public function notif_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'notified_user','user_id');
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'comment_id');
    }
}
