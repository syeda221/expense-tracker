<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisorConversation extends Model
{
    protected $fillable = [
        'user_id',
        'title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(AdvisorMessage::class, 'conversation_id');
    }
}
