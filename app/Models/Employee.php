<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'full_name',
        'phone',
        'department',
        'telegram_chat_id',
        'telegram_username',
        'telegram_full_name',
        'registered_via',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
