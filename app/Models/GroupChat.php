<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChat extends Model
{
    protected $table = 'group_chats';
    protected $primaryKey = 'group_id';
    public $timestamps = false; // Assuming you don't have timestamps in your table

    protected $fillable = [
        'owner_id',
        'name',
        'description',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'group_id');
    }
}
