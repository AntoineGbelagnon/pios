<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity;

class AuditLog extends Activity
{
    protected $table = 'activity_log';

    protected $fillable = [
        'log_name', 'description', 'subject_type', 'subject_id', 'causer_type', 'causer_id',
        'user_id', 'company_id', 'shop_id', 'action', 'old_values', 'new_values', 'ip_address', 'properties',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'properties' => 'collection',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $audit): void {
            $user = auth()->user();
            $audit->user_id ??= $user?->id;
            $audit->company_id ??= $user?->company_id;
            $audit->shop_id ??= request()->attributes->get('shop_id');
            $audit->action ??= $audit->description;
            $audit->ip_address ??= request()->ip();
        });
    }
}