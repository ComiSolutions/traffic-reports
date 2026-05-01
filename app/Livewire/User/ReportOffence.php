<?php

namespace App\Livewire\User;

use App\Enums\ReportStatus;
use App\Models\TrafficReport;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Report Offence')]
class ReportOffence extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $media = null;

    public string $description = '';

    public string $country = 'Nigeria';

    public string $state = '';

    public string $city = '';

    public string $latitude = '';

    public string $longitude = '';

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'media' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime', 'max:51200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'country' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function updatedState(): void
    {
        $this->city = '';
    }

    public function submit(): void
    {
        $validated = $this->validate();
        $coordinates = $this->coordinatesFor($validated);

        $mediaPath = $this->media?->store('reports', 'public');

        if (! $mediaPath) {
            $this->addError('media', __('The uploaded file could not be saved. Please try again.'));

            return;
        }

        if (! $coordinates) {
            $this->addError('city', __('We could not convert this location to coordinates. Please allow GPS or select another city.'));

            return;
        }

        TrafficReport::create([
            'user_id' => auth()->id(),
            'offence_type' => 'Traffic offence',
            'media_path' => $mediaPath,
            'description' => $validated['description'],
            'country' => $validated['country'],
            'state' => $validated['state'],
            'city' => $validated['city'],
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'reported_at' => now(),
            'status' => ReportStatus::Pending,
        ]);

        session()->flash('status', __('Traffic offence report submitted successfully.'));

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.user.report-offence', [
            'countries' => ['Nigeria'],
            'states' => array_keys($this->locations()),
            'cities' => $this->state ? ($this->locations()[$this->state] ?? []) : [],
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{latitude: float, longitude: float}|null
     */
    private function coordinatesFor(array $validated): ?array
    {
        $query = "{$validated['city']}, {$validated['state']}, {$validated['country']}";
        $cacheKey = 'geocode:'.sha1(strtolower($query));

        if ($coordinates = $this->knownCoordinates($validated['state'], $validated['city'])) {
            return $coordinates;
        }

        if (! (bool) env('GEOCODING_ENABLED', true)) {
            return null;
        }

        $coordinates = Cache::remember($cacheKey, now()->addMonth(), function () use ($query) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'TrafficOffenceReportingSystem/1.0 (local development)',
                    'Referer' => config('app.url'),
                ])
                    ->acceptJson()
                    ->timeout(8)
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $query,
                        'format' => 'jsonv2',
                        'limit' => 1,
                        'addressdetails' => 0,
                    ]);
            } catch (ConnectionException) {
                return null;
            }

            if (! $response->ok() || empty($response->json())) {
                return null;
            }

            $result = $response->json()[0];

            return [
                'latitude' => (float) $result['lat'],
                'longitude' => (float) $result['lon'],
            ];
        });

        if ($coordinates) {
            return $coordinates;
        }

        if ($validated['latitude'] !== null && $validated['longitude'] !== null) {
            return [
                'latitude' => (float) $validated['latitude'],
                'longitude' => (float) $validated['longitude'],
            ];
        }

        return null;
    }

    /**
     * @return array{latitude: float, longitude: float}|null
     */
    private function knownCoordinates(string $state, string $city): ?array
    {
        $coordinates = [
            'Cross River' => [
                'Calabar' => ['latitude' => 4.9757, 'longitude' => 8.3417],
                'Ikom' => ['latitude' => 5.9603, 'longitude' => 8.7206],
                'Ogoja' => ['latitude' => 6.6584, 'longitude' => 8.7992],
            ],
            'Lagos' => [
                'Ikeja' => ['latitude' => 6.6018, 'longitude' => 3.3515],
                'Lagos' => ['latitude' => 6.5244, 'longitude' => 3.3792],
                'Lekki' => ['latitude' => 6.4698, 'longitude' => 3.5852],
                'Epe' => ['latitude' => 6.5841, 'longitude' => 3.9834],
                'Badagry' => ['latitude' => 6.4150, 'longitude' => 2.8813],
            ],
            'FCT' => [
                'Abuja' => ['latitude' => 9.0765, 'longitude' => 7.3986],
                'Gwagwalada' => ['latitude' => 8.9391, 'longitude' => 7.0816],
            ],
            'Rivers' => [
                'Port Harcourt' => ['latitude' => 4.8156, 'longitude' => 7.0498],
                'Bonny' => ['latitude' => 4.4516, 'longitude' => 7.1707],
            ],
            'Akwa Ibom' => [
                'Uyo' => ['latitude' => 5.0380, 'longitude' => 7.9095],
                'Ikot Ekpene' => ['latitude' => 5.1794, 'longitude' => 7.7108],
                'Eket' => ['latitude' => 4.6423, 'longitude' => 7.9244],
            ],
        ];

        return $coordinates[$state][$city] ?? null;
    }

    /**
     * @return array<string, list<string>>
     */
    private function locations(): array
    {
        return [
            'Abia' => ['Umuahia', 'Aba'],
            'Adamawa' => ['Yola', 'Mubi'],
            'Akwa Ibom' => ['Uyo', 'Ikot Ekpene', 'Eket'],
            'Anambra' => ['Awka', 'Onitsha', 'Nnewi'],
            'Bauchi' => ['Bauchi', 'Azare'],
            'Bayelsa' => ['Yenagoa'],
            'Benue' => ['Makurdi', 'Gboko'],
            'Borno' => ['Maiduguri', 'Biu'],
            'Cross River' => ['Calabar', 'Ikom', 'Ogoja'],
            'Delta' => ['Asaba', 'Warri', 'Sapele'],
            'Ebonyi' => ['Abakaliki'],
            'Edo' => ['Benin City', 'Auchi'],
            'Ekiti' => ['Ado Ekiti'],
            'Enugu' => ['Enugu', 'Nsukka'],
            'FCT' => ['Abuja', 'Gwagwalada'],
            'Gombe' => ['Gombe'],
            'Imo' => ['Owerri', 'Orlu'],
            'Jigawa' => ['Dutse', 'Hadejia'],
            'Kaduna' => ['Kaduna', 'Zaria'],
            'Kano' => ['Kano'],
            'Katsina' => ['Katsina', 'Daura'],
            'Kebbi' => ['Birnin Kebbi'],
            'Kogi' => ['Lokoja', 'Okene'],
            'Kwara' => ['Ilorin', 'Offa'],
            'Lagos' => ['Ikeja', 'Lagos', 'Lekki', 'Epe', 'Badagry'],
            'Nasarawa' => ['Lafia', 'Keffi'],
            'Niger' => ['Minna', 'Suleja'],
            'Ogun' => ['Abeokuta', 'Ijebu Ode', 'Sagamu'],
            'Ondo' => ['Akure', 'Ondo'],
            'Osun' => ['Osogbo', 'Ile Ife', 'Ilesa'],
            'Oyo' => ['Ibadan', 'Ogbomoso'],
            'Plateau' => ['Jos'],
            'Rivers' => ['Port Harcourt', 'Bonny'],
            'Sokoto' => ['Sokoto'],
            'Taraba' => ['Jalingo', 'Wukari'],
            'Yobe' => ['Damaturu', 'Potiskum'],
            'Zamfara' => ['Gusau'],
        ];
    }
}
