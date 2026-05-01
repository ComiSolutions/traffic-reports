<?php

namespace Database\Factories;

use App\Models\ReportMedia;
use App\Models\TrafficReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportMedia>
 */
class ReportMediaFactory extends Factory
{
    protected $model = ReportMedia::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'traffic_report_id' => TrafficReport::factory(),
            'file_path' => 'reports/'.fake()->uuid().'.jpg',
            'file_name' => fake()->uuid().'.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(10_000, 5_000_000),
            'media_type' => 'image',
        ];
    }
}
