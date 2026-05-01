<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\TrafficReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrafficReport>
 */
class TrafficReportFactory extends Factory
{
    protected $model = TrafficReport::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'offence_type' => fake()->words(3, true),
            'media_path' => 'reports/'.fake()->uuid().'.jpg',
            'description' => fake()->optional()->sentence(),
            'country' => 'Nigeria',
            'state' => 'Cross River',
            'city' => 'Calabar',
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'reported_at' => now(),
            'status' => ReportStatus::Pending,
        ];
    }
}
