@php
        $mapReports = $reports
            ->filter(fn ($report) => filled($report->latitude) && filled($report->longitude))
            ->map(fn ($report) => [
                'id' => $report->id,
                'title' => $report->offence_type,
                'status' => $report->status?->value ?? 'pending',
                'date' => optional($report->reported_at ?? $report->created_at)->format('M j, Y g:i A'),
                'latitude' => (float) $report->latitude,
                'longitude' => (float) $report->longitude,
                'url' => route('reports.show', $report),
            ])
            ->values();
@endphp

<section class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('Traffic Offence Dashboard') }}
                </h1>

                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('Welcome, :name. Submit reports with evidence, track review status, and receive updates from administrators.', ['name' => auth()->user()->name]) }}
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">
                <a
                    href="{{ route('notifications.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-900"
                    wire:navigate
                >
                    {{ __('Notifications') }}
                    @if (auth()->user()->unreadNotifications()->count() > 0)
                        <span class="ml-2 rounded-full bg-green-600 px-2 py-0.5 text-xs text-white">
                            {{ auth()->user()->unreadNotifications()->count() }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ route('reports.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-neutral-700 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-300"
                    wire:navigate
                >
                    {{ __('Report Offence') }}
                </a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 p-5 dark:border-neutral-700">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Total Reports') }}</p>
                <p class="mt-2 text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $reportCounts['total'] }}</p>
            </div>
            <div class="rounded-xl border border-neutral-200 p-5 dark:border-neutral-700">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Pending Review') }}</p>
                <p class="mt-2 text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $reportCounts['pending'] }}</p>
            </div>
            <div class="rounded-xl border border-neutral-200 p-5 dark:border-neutral-700">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Approved') }}</p>
                <p class="mt-2 text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $reportCounts['approved'] }}</p>
            </div>
            <div class="rounded-xl border border-neutral-200 p-5 dark:border-neutral-700">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Rejected') }}</p>
                <p class="mt-2 text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $reportCounts['rejected'] }}</p>
            </div>
        </div>

        <div
            class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700"
            x-data="trafficReportsMap('user-reports-map', @js($mapReports))"
            x-init="init()"
        >
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('Reports Map') }}
                </h2>
            </div>

            <div id="user-reports-map" class="h-96 w-full" style="min-height: 24rem;" wire:ignore></div>
        </div>

        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('Submitted Reports') }}
                </h2>
            </div>

            @if ($reports->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('You have not submitted any reports yet.') }}
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-neutral-200 bg-neutral-50 text-xs uppercase text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
                            <tr>
                                <th class="px-6 py-3">{{ __('Media') }}</th>
                                <th class="px-6 py-3">{{ __('Offence') }}</th>
                                <th class="px-6 py-3">{{ __('Status') }}</th>
                                <th class="px-6 py-3">{{ __('Date') }}</th>
                                <th class="px-6 py-3 text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach ($reports as $report)
                                <tr>
                                    <td class="px-6 py-4">
                                        @php
                                            $mediaPath = $report->media_path ?? $report->media->first()?->file_path;
                                            $mediaUrl = $mediaPath ? Illuminate\Support\Facades\Storage::disk('public')->url($mediaPath) : null;
                                            $extension = strtolower(pathinfo($mediaPath ?? '', PATHINFO_EXTENSION));
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true);
                                            $isVideo = in_array($extension, ['mp4', 'webm', 'mov'], true);
                                        @endphp

                                        <div class="h-20 w-28 overflow-hidden rounded-lg border border-neutral-200 bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-900">
                                            @if ($mediaUrl && $isImage)
                                                <img
                                                    src="{{ $mediaUrl }}"
                                                    alt="{{ __('Report media') }}"
                                                    class="h-full w-full object-cover"
                                                >
                                            @elseif ($mediaUrl && $isVideo)
                                                <video
                                                    src="{{ $mediaUrl }}"
                                                    class="h-full w-full object-cover"
                                                    controls
                                                    muted
                                                ></video>
                                            @else
                                                <div class="flex h-full w-full items-center justify-center px-3 text-center text-xs text-neutral-500 dark:text-neutral-400">
                                                    {{ __('No media') }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">
                                        <div>{{ $report->offence_type }}</div>
                                        <div class="mt-1 text-xs font-normal text-neutral-500 dark:text-neutral-400">
                                            {{ collect([$report->city, $report->state, $report->country])->filter()->implode(', ') }}
                                        </div>
                                        <div class="mt-1 text-xs font-normal text-neutral-500 dark:text-neutral-400">
                                            {{ $report->latitude }}, {{ $report->longitude }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium capitalize text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                                            {{ $report->status?->value ?? __('pending') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">
                                        {{ $report->reported_at?->format('M j, Y g:i A') ?? $report->created_at?->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a
                                            href="{{ route('reports.show', $report) }}"
                                            class="font-medium text-neutral-900 underline-offset-4 hover:underline dark:text-neutral-100"
                                            wire:navigate
                                        >
                                            {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
 </section>
