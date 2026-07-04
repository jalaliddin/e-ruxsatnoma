<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'employee_id',
        'category_id',
        'employee_name',
        'destination',
        'reason',
        'from_time',
        'to_time',
        'code',
        'status',
        'approver_id',
        'hr_id',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'from_time' => 'datetime',
            'to_time' => 'datetime',
            'decided_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function category()
    {
        return $this->belongsTo(PermissionCategory::class, 'category_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function hr()
    {
        return $this->belongsTo(User::class, 'hr_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForManager($query, int $userId)
    {
        return $query->where('approver_id', $userId);
    }
}
