<x-layouts::app :title="__('Reports Review')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">
                {{ __('Review Traffic Reports') }}
            </h1>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                {{ __('Open submitted offence reports, inspect evidence, and approve or reject each case.') }}
            </p>
        </div>

        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            @if ($reports->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('No reports have been submitted yet.') }}
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
                                    <td class="px-6 py-4 text-neutral-900 dark:text-neutral-100">{{ $report->reporter?->name ?? __('Unknown') }}</td>
                                    <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">{{ $report->offence_type }}</td>
                                    <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ collect([$report->city, $report->state, $report->country])->filter()->implode(', ') ?: __('Not provided') }}</td>
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
                                            href="{{ route('admin.reports.show', $report) }}"
                                            class="rounded-lg border border-neutral-300 px-3 py-1.5 text-xs font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-900"
                                        >
                                            {{ __('Review') }}
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
