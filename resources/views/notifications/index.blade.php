<x-layouts::app :title="__('Notifications')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div>
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">
                {{ __('Notifications') }}
            </h1>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                {{ __('Status updates for your traffic offence reports.') }}
            </p>
        </div>

        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            @if ($notifications->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('You have no notifications yet.') }}
                </div>
            @else
                <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach ($notifications as $notification)
                        <div class="flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between {{ $notification->read_at ? '' : 'bg-neutral-50 dark:bg-neutral-900' }}">
                            <div>
                                <div class="flex items-center gap-2">
                                    @unless ($notification->read_at)
                                        <span class="h-2 w-2 rounded-full bg-green-600"></span>
                                    @endunless

                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ $notification->data['message'] ?? __('Your report status has been updated.') }}
                                    </p>
                                </div>

                                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                                    {{ $notification->data['offence_type'] ?? __('Traffic offence') }}
                                    <span class="mx-1">·</span>
                                    {{ $notification->created_at?->format('M j, Y g:i A') }}
                                </p>
                            </div>

                            <div class="flex items-center justify-end gap-2">
                                @if (! empty($notification->data['url']))
                                    <a
                                        href="{{ $notification->data['url'] }}"
                                        class="rounded-lg border border-neutral-300 px-3 py-1.5 text-xs font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-900"
                                    >
                                        {{ __('View Report') }}
                                    </a>
                                @endif

                                @unless ($notification->read_at)
                                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="rounded-lg bg-neutral-900 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-neutral-700 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-300"
                                        >
                                            {{ __('Mark Read') }}
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
