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

    public string $landmark = '';

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
            'landmark' => ['nullable', 'string', 'max:180'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function updatedState(): void
    {
        $this->city = '';
        $this->latitude = '';
        $this->longitude = '';
    }

    public function updatedCity(): void
    {
        if (! $this->state || ! $this->city) {
            $this->latitude = '';
            $this->longitude = '';

            return;
        }

        $coordinates = $this->knownCoordinates($this->state, $this->city);

        if (! $coordinates) {
            return;
        }

        $this->latitude = number_format($coordinates['latitude'], 6, '.', '');
        $this->longitude = number_format($coordinates['longitude'], 6, '.', '');
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
            'landmark' => $validated['landmark'],
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
        return $this->locationCoordinates()[$state][$city] ?? null;
    }

    /**
     * @return array<string, array<string, array{latitude: float, longitude: float}>>
     */
    private function locationCoordinates(): array
    {
        return [
            'Abia' => [
                'Umuahia' => ['latitude' => 5.5320, 'longitude' => 7.4860],
                'Aba' => ['latitude' => 5.1216, 'longitude' => 7.3733],
            ],
            'Adamawa' => [
                'Yola' => ['latitude' => 9.2035, 'longitude' => 12.4954],
                'Mubi' => ['latitude' => 10.2686, 'longitude' => 13.2670],
            ],
            'Akwa Ibom' => [
                'Uyo' => ['latitude' => 5.0380, 'longitude' => 7.9095],
                'Ikot Ekpene' => ['latitude' => 5.1794, 'longitude' => 7.7108],
                'Eket' => ['latitude' => 4.6423, 'longitude' => 7.9244],
            ],
            'Anambra' => [
                'Awka' => ['latitude' => 6.2101, 'longitude' => 7.0741],
                'Onitsha' => ['latitude' => 6.1498, 'longitude' => 6.7857],
                'Nnewi' => ['latitude' => 6.0105, 'longitude' => 6.9103],
            ],
            'Bauchi' => [
                'Bauchi' => ['latitude' => 10.3158, 'longitude' => 9.8442],
                'Azare' => ['latitude' => 11.6765, 'longitude' => 10.1948],
            ],
            'Bayelsa' => [
                'Yenagoa' => ['latitude' => 4.9267, 'longitude' => 6.2676],
            ],
            'Benue' => [
                'Makurdi' => ['latitude' => 7.7322, 'longitude' => 8.5391],
                'Gboko' => ['latitude' => 7.3228, 'longitude' => 9.0011],
            ],
            'Borno' => [
                'Maiduguri' => ['latitude' => 11.8311, 'longitude' => 13.1510],
                'Biu' => ['latitude' => 10.6129, 'longitude' => 12.1946],
            ],
            'Cross River' => [
                'Calabar' => ['latitude' => 4.9757, 'longitude' => 8.3417],
                'Ikom' => ['latitude' => 5.9603, 'longitude' => 8.7206],
                'Ogoja' => ['latitude' => 6.6584, 'longitude' => 8.7992],
            ],
            'Delta' => [
                'Asaba' => ['latitude' => 6.1985, 'longitude' => 6.7319],
                'Warri' => ['latitude' => 5.5167, 'longitude' => 5.7500],
                'Sapele' => ['latitude' => 5.8941, 'longitude' => 5.6767],
            ],
            'Ebonyi' => [
                'Abakaliki' => ['latitude' => 6.3249, 'longitude' => 8.1137],
            ],
            'Edo' => [
                'Benin City' => ['latitude' => 6.3350, 'longitude' => 5.6037],
                'Auchi' => ['latitude' => 7.0676, 'longitude' => 6.2636],
            ],
            'Ekiti' => [
                'Ado Ekiti' => ['latitude' => 7.6233, 'longitude' => 5.2209],
            ],
            'Enugu' => [
                'Enugu' => ['latitude' => 6.5244, 'longitude' => 7.5086],
                'Nsukka' => ['latitude' => 6.8567, 'longitude' => 7.3958],
            ],
            'FCT' => [
                'Abuja' => ['latitude' => 9.0765, 'longitude' => 7.3986],
                'Gwagwalada' => ['latitude' => 8.9391, 'longitude' => 7.0816],
            ],
            'Gombe' => [
                'Gombe' => ['latitude' => 10.2897, 'longitude' => 11.1673],
            ],
            'Imo' => [
                'Owerri' => ['latitude' => 5.4850, 'longitude' => 7.0351],
                'Orlu' => ['latitude' => 5.7957, 'longitude' => 7.0351],
            ],
            'Jigawa' => [
                'Dutse' => ['latitude' => 11.7562, 'longitude' => 9.3389],
                'Hadejia' => ['latitude' => 12.4498, 'longitude' => 10.0444],
            ],
            'Kaduna' => [
                'Kaduna' => ['latitude' => 10.5105, 'longitude' => 7.4165],
                'Zaria' => ['latitude' => 11.1113, 'longitude' => 7.7227],
            ],
            'Kano' => [
                'Kano' => ['latitude' => 12.0022, 'longitude' => 8.5920],
            ],
            'Katsina' => [
                'Katsina' => ['latitude' => 12.9908, 'longitude' => 7.6006],
                'Daura' => ['latitude' => 13.0333, 'longitude' => 8.3167],
            ],
            'Kebbi' => [
                'Birnin Kebbi' => ['latitude' => 12.4539, 'longitude' => 4.1975],
            ],
            'Kogi' => [
                'Lokoja' => ['latitude' => 7.8023, 'longitude' => 6.7333],
                'Okene' => ['latitude' => 7.5512, 'longitude' => 6.2359],
            ],
            'Kwara' => [
                'Ilorin' => ['latitude' => 8.4966, 'longitude' => 4.5421],
                'Offa' => ['latitude' => 8.1491, 'longitude' => 4.7207],
            ],
            'Lagos' => [
                'Ikeja' => ['latitude' => 6.6018, 'longitude' => 3.3515],
                'Lagos' => ['latitude' => 6.5244, 'longitude' => 3.3792],
                'Lekki' => ['latitude' => 6.4698, 'longitude' => 3.5852],
                'Epe' => ['latitude' => 6.5841, 'longitude' => 3.9834],
                'Badagry' => ['latitude' => 6.4150, 'longitude' => 2.8813],
            ],
            'Nasarawa' => [
                'Lafia' => ['latitude' => 8.4966, 'longitude' => 8.5153],
                'Keffi' => ['latitude' => 8.8465, 'longitude' => 7.8735],
            ],
            'Niger' => [
                'Minna' => ['latitude' => 9.5836, 'longitude' => 6.5463],
                'Suleja' => ['latitude' => 9.1806, 'longitude' => 7.1794],
            ],
            'Ogun' => [
                'Abeokuta' => ['latitude' => 7.1475, 'longitude' => 3.3619],
                'Ijebu Ode' => ['latitude' => 6.8194, 'longitude' => 3.9173],
                'Sagamu' => ['latitude' => 6.8322, 'longitude' => 3.6319],
            ],
            'Ondo' => [
                'Akure' => ['latitude' => 7.2571, 'longitude' => 5.2058],
                'Ondo' => ['latitude' => 7.0932, 'longitude' => 4.8353],
            ],
            'Osun' => [
                'Osogbo' => ['latitude' => 7.7827, 'longitude' => 4.5418],
                'Ile Ife' => ['latitude' => 7.4905, 'longitude' => 4.5521],
                'Ilesa' => ['latitude' => 7.6279, 'longitude' => 4.7416],
            ],
            'Oyo' => [
                'Ibadan' => ['latitude' => 7.3775, 'longitude' => 3.9470],
                'Ogbomoso' => ['latitude' => 8.1337, 'longitude' => 4.2401],
            ],
            'Plateau' => [
                'Jos' => ['latitude' => 9.8965, 'longitude' => 8.8583],
            ],
            'Rivers' => [
                'Port Harcourt' => ['latitude' => 4.8156, 'longitude' => 7.0498],
                'Bonny' => ['latitude' => 4.4516, 'longitude' => 7.1707],
            ],
            'Sokoto' => [
                'Sokoto' => ['latitude' => 13.0059, 'longitude' => 5.2476],
            ],
            'Taraba' => [
                'Jalingo' => ['latitude' => 8.8937, 'longitude' => 11.3596],
                'Wukari' => ['latitude' => 7.8714, 'longitude' => 9.7779],
            ],
            'Yobe' => [
                'Damaturu' => ['latitude' => 11.7489, 'longitude' => 11.9661],
                'Potiskum' => ['latitude' => 11.7139, 'longitude' => 11.0811],
            ],
            'Zamfara' => [
                'Gusau' => ['latitude' => 12.1628, 'longitude' => 6.6614],
            ],
        ];
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
