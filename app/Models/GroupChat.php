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
    
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id')
            ->withPivot('status'); // If you need to access the 'status' column in the pivot table
    }

    public function addMember(User $user)
    {
        $this->members()->attach($user->id);
    }

    public function removeMember(User $user)
    {
        $this->members()->detach($user->id);
    }

}
