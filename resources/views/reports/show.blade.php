<x-layouts::app :title="__('Report Details')">
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
                    {{ __('Report #:id', ['id' => $report->id]) }}
                </h1>
                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('Submitted :date', ['date' => $report->reported_at?->format('M j, Y g:i A') ?? $report->created_at?->format('M j, Y g:i A')]) }}
                </p>
            </div>

            <span class="w-fit rounded-full bg-neutral-100 px-3 py-1 text-sm font-medium capitalize text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                {{ $report->status?->value ?? __('pending') }}
            </span>
        </div>

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
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Reviewed By') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ $report->reviewer?->name ?? __('Not reviewed yet') }}</dd>
                        </div>
                        <div>
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Review Note') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ $report->review_note ?: __('No review note yet.') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4 flex flex-col gap-2">
                        <a
                            href="https://www.openstreetmap.org/?mlat={{ $report->latitude }}&mlon={{ $report->longitude }}#map=16/{{ $report->latitude }}/{{ $report->longitude }}"
                            target="_blank"
                            class="rounded-lg border border-neutral-300 px-3 py-2 text-center text-sm font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-900"
                        >
                            {{ __('Open Map') }}
                        </a>

                        <a
                            href="{{ route('dashboard') }}"
                            class="rounded-lg bg-neutral-900 px-3 py-2 text-center text-sm font-medium text-white transition hover:bg-neutral-700 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-300"
                            wire:navigate
                        >
                            {{ __('Back to Dashboard') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
