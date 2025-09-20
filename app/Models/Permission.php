<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'employee_name',
        'destination',
        'from_time',
        'to_time',
        'code',
    ];
    public function user()
    {
    return $this->belongsTo(User::class);
    }
}
