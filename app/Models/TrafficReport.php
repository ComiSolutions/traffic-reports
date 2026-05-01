<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Database\Factories\TrafficReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'offence_type',
    'media_path',
    'description',
    'country',
    'state',
    'city',
    'landmark',
    'latitude',
    'longitude',
    'reported_at',
    'status',
    'reviewed_by',
    'reviewed_at',
    'review_note',
])]
class TrafficReport extends Model
{
    /** @use HasFactory<TrafficReportFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'reported_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'status' => ReportStatus::class,
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ReportMedia::class);
    }
}
