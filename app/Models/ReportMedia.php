<?php

namespace App\Models;

use Database\Factories\ReportMediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'traffic_report_id',
    'file_path',
    'file_name',
    'mime_type',
    'file_size',
    'media_type',
])]
class ReportMedia extends Model
{
    /** @use HasFactory<ReportMediaFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function trafficReport(): BelongsTo
    {
        return $this->belongsTo(TrafficReport::class);
    }
}
