<section
    class="mx-auto flex w-full max-w-3xl flex-col gap-6"
    x-data
    x-init="
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    $wire.set('latitude', String(position.coords.latitude));
                    $wire.set('longitude', String(position.coords.longitude));
                },
                () => {},
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }
    "
>
        <div>
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">
                {{ __('Report Offence') }}
            </h1>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                {{ __('Upload evidence and allow location access so the report can include GPS coordinates.') }}
            </p>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-200">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="submit" class="space-y-6 rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
            <div>
                <label for="media" class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    {{ __('Image or Video') }}
                </label>
                <input
                    id="media"
                    type="file"
                    wire:model="media"
                    accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime"
                    class="block w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 file:mr-4 file:rounded-md file:border-0 file:bg-neutral-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:file:bg-neutral-100 dark:file:text-neutral-900"
                >
                @error('media')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <div wire:loading wire:target="media" class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('Uploading media...') }}
                </div>
            </div>

            <div>
                <label for="description" class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    {{ __('Description') }}
                </label>
                <textarea
                    id="description"
                    wire:model="description"
                    rows="5"
                    class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-neutral-200 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:focus:ring-neutral-800"
                ></textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label for="country" class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        {{ __('Country') }}
                    </label>
                    <select
                        id="country"
                        wire:model="country"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-neutral-200 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:focus:ring-neutral-800"
                    >
                        @foreach ($countries as $countryOption)
                            <option value="{{ $countryOption }}">{{ $countryOption }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="state" class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        {{ __('State') }}
                    </label>
                    <select
                        id="state"
                        wire:model.live="state"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-neutral-200 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:focus:ring-neutral-800"
                    >
                        <option value="">{{ __('Select state') }}</option>
                        @foreach ($states as $stateOption)
                            <option value="{{ $stateOption }}">{{ $stateOption }}</option>
                        @endforeach
                    </select>
                    @error('state')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="city" class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        {{ __('City') }}
                    </label>
                    <select
                        id="city"
                        wire:model.live="city"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-neutral-200 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:focus:ring-neutral-800"
                    >
                        <option value="">{{ __('Select city') }}</option>
                        @foreach ($cities as $cityOption)
                            <option value="{{ $cityOption }}">{{ $cityOption }}</option>
                        @endforeach
                    </select>
                    @error('city')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <input type="hidden" wire:model="latitude">
            <input type="hidden" wire:model="longitude">

            <div class="gps-card {{ $latitude && $longitude ? 'gps-card-ready' : '' }}" wire:key="gps-{{ $state }}-{{ $city }}-{{ $latitude }}-{{ $longitude }}">
                <div class="gps-radar" aria-hidden="true">
                    <span></span>
                </div>

                <div class="gps-copy">
                    <p class="gps-label">{{ $latitude && $longitude ? __('GPS locked') : __('GPS standby') }}</p>
                    <p class="gps-title">
                        {{ $latitude && $longitude ? __('Coordinates generated for selected location') : __('Select a state and city to generate GPS instantly') }}
                    </p>
                    <div class="gps-values">
                        <span>{{ __('Latitude') }}: <strong>{{ $latitude ?: __('Waiting') }}</strong></span>
                        <span>{{ __('Longitude') }}: <strong>{{ $longitude ?: __('Waiting') }}</strong></span>
                    </div>
                </div>
            </div>

            <div>
                <label for="landmark" class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    {{ __('Incident landmark') }}
                    <span class="text-neutral-400">{{ __('(optional)') }}</span>
                </label>
                <input
                    id="landmark"
                    type="text"
                    wire:model="landmark"
                    maxlength="180"
                    placeholder="{{ __('Nearest bus stop, junction, road name, or notable building') }}"
                    class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-neutral-200 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:focus:ring-neutral-800"
                >
                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    {{ __('This helps reviewers locate the exact incident area beyond the city GPS point.') }}
                </p>
                @error('landmark')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            @error('latitude')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            @error('longitude')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3">
                <a
                    href="{{ route('dashboard') }}"
                    class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-900"
                    wire:navigate
                >
                    {{ __('Cancel') }}
                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-neutral-700 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-300"
                    wire:loading.attr="disabled"
                    wire:target="submit,media"
                >
                    {{ __('Submit Report') }}
                </button>
            </div>
        </form>
</section>
