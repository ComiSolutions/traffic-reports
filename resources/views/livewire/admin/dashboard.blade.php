@php
        $mapReports = $reports
            ->filter(fn ($report) => filled($report->latitude) && filled($report->longitude))
            ->map(fn ($report) => [
                'id' => $report->id,
                'title' => $report->offence_type,
                'reporter' => $report->reporter?->name ?? __('Unknown'),
                'status' => $report->status?->value ?? 'pending',
                'date' => optional($report->reported_at ?? $report->created_at)->format('M j, Y g:i A'),
                'latitude' => (float) $report->latitude,
                'longitude' => (float) $report->longitude,
                'url' => route('admin.reports.show', $report),
            ])
            ->values();
@endphp

<section class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('Admin Dashboard') }}
                </h1>

                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('Review submitted traffic offence reports and filter by status.') }}
                </p>
            </div>

            <div class="w-full md:w-64">
                <label for="status" class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    {{ __('Filter reports') }}
                </label>

                <select
                    id="status"
                    wire:model.live="status"
                    class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-neutral-200 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:focus:ring-neutral-800"
                >
                    <option value="all">{{ __('All reports') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach ($statuses as $status)
                <div class="rounded-xl border border-neutral-200 p-5 dark:border-neutral-700">
                    <p class="text-sm font-medium capitalize text-neutral-600 dark:text-neutral-400">
                        {{ $status->value }}
                    </p>
                    <p class="mt-2 text-3xl font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ $statusCounts->get($status->value, 0) }}
                    </p>
                </div>
            @endforeach
        </div>

        <div
            class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700"
            wire:key="admin-reports-map-{{ $status }}"
            x-data="trafficReportsMap('admin-reports-map', @js($mapReports))"
            x-init="init()"
        >
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('Reports Map') }}
                </h2>
            </div>

            <div id="admin-reports-map" class="h-96 w-full" style="min-height: 24rem;" wire:ignore></div>
        </div>

        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('Reports') }}
                </h2>
            </div>

            @if (session('status'))
                <div class="border-b border-green-200 bg-green-50 px-6 py-3 text-sm text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-200">
                    {{ session('status') }}
                </div>
            @endif

            @if ($reports->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('No reports match the selected filter.') }}
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-neutral-200 bg-neutral-50 text-xs uppercase text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
                            <tr>
                                <th class="px-6 py-3">{{ __('Reporter') }}</th>
                                <th class="px-6 py-3">{{ __('Offence') }}</th>
                                <th class="px-6 py-3">{{ __('Location') }}</th>
                                <th class="px-6 py-3">{{ __('Status') }}</th>
                                <th class="px-6 py-3">{{ __('Submitted') }}</th>
                                <th class="px-6 py-3 text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach ($reports as $report)
                                <tr>
                                    <td class="px-6 py-4 text-neutral-900 dark:text-neutral-100">
                                        {{ $report->reporter?->name ?? __('Unknown') }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ $report->offence_type }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">
                                        <div>{{ collect([$report->city, $report->state, $report->country])->filter()->implode(', ') }}</div>
                                        <div class="mt-1 text-xs">
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
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                wire:click="approve({{ $report->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="approve({{ $report->id }})"
                                                class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60"
                                            >
                                                {{ __('Approve') }}
                                            </button>

                                            <button
                                                type="button"
                                                wire:click="reject({{ $report->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="reject({{ $report->id }})"
                                                class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60"
                                            >
                                                {{ __('Reject') }}
                                            </button>

                                            <a
                                                href="{{ route('admin.reports.show', $report) }}"
                                                class="rounded-lg border border-neutral-300 px-3 py-1.5 text-xs font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-900"
                                                wire:navigate
                                            >
                                                {{ __('View') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
 </section>
