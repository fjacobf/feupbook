<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'message_id';
    public $timestamps = false; // Assuming you don't have timestamps in your table

    protected $fillable = [
        'emitter_id',
        'group_id',
        'content',
        'date',
        'viewed',
    ];

    public function emitter()
    {
        return $this->belongsTo(User::class, 'emitter_id');
    }

    public function groupChat()
    {
        return $this->belongsTo(GroupChat::class, 'group_id');
    }
}
