<x-layouts::app :title="__('Review Report')">
    @php
        $mediaPath = $report->media_path ?? $report->media->first()?->file_path;
        $mediaUrl = $mediaPath ? Illuminate\Support\Facades\Storage::disk('public')->url($mediaPath) : null;
        $extension = strtolower(pathinfo($mediaPath ?? '', PATHINFO_EXTENSION));
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true);
        $isVideo = in_array($extension, ['mp4', 'webm', 'mov'], true);
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('Review Report #:id', ['id' => $report->id]) }}
                </h1>
                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('Submitted by :name', ['name' => $report->reporter?->name ?? __('Unknown')]) }}
                </p>
            </div>

            <span class="w-fit rounded-full bg-neutral-100 px-3 py-1 text-sm font-medium capitalize text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                {{ $report->status?->value ?? __('pending') }}
            </span>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1fr_22rem]">
            <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ __('Evidence') }}
                    </h2>
                </div>

                <div class="bg-neutral-50 p-6 dark:bg-neutral-900">
                    @if ($mediaUrl && $isImage)
                        <img src="{{ $mediaUrl }}" alt="{{ __('Report evidence') }}" class="max-h-[32rem] w-full rounded-lg object-contain">
                    @elseif ($mediaUrl && $isVideo)
                        <video src="{{ $mediaUrl }}" controls class="max-h-[32rem] w-full rounded-lg"></video>
                    @else
                        <div class="flex min-h-64 items-center justify-center rounded-lg border border-dashed border-neutral-300 text-sm text-neutral-500 dark:border-neutral-700 dark:text-neutral-400">
                            {{ __('No media uploaded for this report.') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <div class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ __('Report Details') }}
                    </h2>

                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Offence') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $report->offence_type }}</dd>
                        </div>
                        <div>
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Description') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ $report->description ?: __('No description provided.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Location') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ collect([$report->city, $report->state, $report->country])->filter()->implode(', ') ?: __('Not provided') }}</dd>
                        </div>
                        <div>
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Coordinates') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ $report->latitude }}, {{ $report->longitude }}</dd>
                        </div>
                        <div>
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Submitted') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ $report->reported_at?->format('M j, Y g:i A') ?? $report->created_at?->format('M j, Y g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Reviewed By') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ $report->reviewer?->name ?? __('Not reviewed yet') }}</dd>
                        </div>
                    </dl>

                    <a
                        href="https://www.openstreetmap.org/?mlat={{ $report->latitude }}&mlon={{ $report->longitude }}#map=16/{{ $report->latitude }}/{{ $report->longitude }}"
                        target="_blank"
                        class="mt-4 inline-flex rounded-lg border border-neutral-300 px-3 py-2 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-900"
                    >
                        {{ __('Open Map') }}
                    </a>
                </div>

                <form method="POST" class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                    @csrf
                    @method('PATCH')

                    <label for="review_note" class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        {{ __('Review Note') }}
                    </label>
                    <textarea
                        id="review_note"
                        name="review_note"
                        rows="4"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-neutral-200 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:focus:ring-neutral-800"
                    >{{ old('review_note', $report->review_note) }}</textarea>
                    @error('review_note')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                        <button
                            type="submit"
                            formaction="{{ route('admin.reports.approve', $report) }}"
                            class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700"
                        >
                            {{ __('Approve') }}
                        </button>
                        <button
                            type="submit"
                            formaction="{{ route('admin.reports.reject', $report) }}"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                        >
                            {{ __('Reject') }}
                        </button>
                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="rounded-lg border border-neutral-300 px-4 py-2 text-center text-sm font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-900"
                            wire:navigate
                        >
                            {{ __('Back') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts::app>
