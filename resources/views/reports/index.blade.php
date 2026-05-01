<x-layouts::app :title="__('My Reports')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('My Reports') }}
                </h1>
                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('Track every traffic offence report you have submitted.') }}
                </p>
            </div>

            <a
                href="{{ route('reports.create') }}"
                class="rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-neutral-700 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-300"
                wire:navigate
            >
                {{ __('Report Offence') }}
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            @if ($reports->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('You have not submitted any reports yet.') }}
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-neutral-200 bg-neutral-50 text-xs uppercase text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
                            <tr>
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
                                    <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">{{ $report->offence_type }}</td>
                                    <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ collect([$report->city, $report->state, $report->country])->filter()->implode(', ') ?: __('Not provided') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium capitalize text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                                            {{ $report->status?->value ?? __('pending') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $report->reported_at?->format('M j, Y g:i A') ?? $report->created_at?->format('M j, Y g:i A') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a
                                            href="{{ route('reports.show', $report) }}"
                                            class="rounded-lg border border-neutral-300 px-3 py-1.5 text-xs font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-900"
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
    </div>
</x-layouts::app>
