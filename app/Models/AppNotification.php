<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'app_notifications';

    protected $fillable = [
        'company_id', 'user_id', 'type', 'title', 'message', 'data', 'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function scopeUnread($query) { return $query->where('is_read', false); }

    public static function createForCompany(int $companyId, string $type, string $title, string $message, array $data = []): static
    {
        return static::create([
            'company_id' => $companyId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
