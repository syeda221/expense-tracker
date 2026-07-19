<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisorMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'role',
        'message',
    ];

    public function conversation()
    {
        return $this->belongsTo(AdvisorConversation::class, 'conversation_id');
    }
}
