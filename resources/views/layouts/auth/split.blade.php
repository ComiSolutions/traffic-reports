<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-neutral-950">
        <div class="grid min-h-dvh lg:grid-cols-[1.05fr_0.95fr]">
            <div class="relative hidden min-h-dvh overflow-hidden bg-[#07110f] px-10 py-9 text-white lg:flex lg:flex-col">
                <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(5,150,105,.28),transparent_38%),linear-gradient(45deg,rgba(251,191,36,.16),transparent_34%)]"></div>
                <div class="absolute inset-x-10 top-28 h-px bg-emerald-300/20"></div>
                <div class="absolute inset-x-10 bottom-28 h-px bg-amber-200/15"></div>

                <a href="{{ route('home') }}" class="relative z-20 flex items-center gap-3 text-lg font-semibold" wire:navigate>
                    <span class="flex size-11 items-center justify-center rounded-lg bg-emerald-400 text-emerald-950">
                        <span class="text-sm font-black">TR</span>
                    </span>
                    {{ __('Traffic Reports') }}
                </a>

                <div class="relative z-20 my-auto max-w-xl">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-emerald-300/25 bg-emerald-300/10 px-3 py-1.5 text-sm text-emerald-100">
                        <span class="size-2 rounded-full bg-emerald-300"></span>
                        {{ __('Evidence-led traffic offence reporting') }}
                    </div>

                    <h1 class="text-5xl font-bold leading-tight tracking-normal">
                        {{ __('Capture incidents clearly, then track every review from one dashboard.') }}
                    </h1>

                    <p class="mt-5 text-base leading-8 text-zinc-300">
                        {{ __('Submit reports with location, media evidence, and a status trail built for focused administrative review.') }}
                    </p>
                </div>

                <div class="relative z-20 grid grid-cols-3 gap-3">
                    <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                        <p class="text-2xl font-bold">GPS</p>
                        <p class="mt-1 text-xs text-zinc-300">{{ __('Location capture') }}</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                        <p class="text-2xl font-bold">{{ __('Media') }}</p>
                        <p class="mt-1 text-xs text-zinc-300">{{ __('Photo and video') }}</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                        <p class="text-2xl font-bold">{{ __('Review') }}</p>
                        <p class="mt-1 text-xs text-zinc-300">{{ __('Status workflow') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex min-h-dvh w-full items-center justify-center bg-zinc-50 px-5 py-8 dark:bg-neutral-950 sm:px-8 lg:px-12">
                <div class="w-full max-w-md">
                    <a href="{{ route('home') }}" class="mb-8 flex items-center justify-center gap-3 font-semibold text-zinc-900 dark:text-white lg:hidden" wire:navigate>
                        <span class="flex size-10 items-center justify-center rounded-lg bg-emerald-500 text-emerald-950">
                            <span class="text-sm font-black">TR</span>
                        </span>
                        {{ __('Traffic Reports') }}
                    </a>

                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
