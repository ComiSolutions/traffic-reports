<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="auth-page antialiased">
        <div class="auth-shell">
            <aside class="auth-aside">
                <a href="{{ route('home') }}" class="auth-brand" wire:navigate>
                    <span class="auth-brand-mark">
                        <span>TR</span>
                    </span>
                    {{ __('Traffic Reports') }}
                </a>

                <div class="auth-aside-content">
                    <div class="auth-kicker">
                        <span></span>
                        {{ __('Evidence-led traffic offence reporting') }}
                    </div>

                    <h1>
                        {{ __('Capture incidents clearly, then track every review from one dashboard.') }}
                    </h1>

                    <p>
                        {{ __('Submit reports with location, media evidence, and a status trail built for focused administrative review.') }}
                    </p>
                </div>

                <div class="auth-feature-grid">
                    <div>
                        <strong>GPS</strong>
                        <span>{{ __('Location capture') }}</span>
                    </div>
                    <div>
                        <strong>{{ __('Media') }}</strong>
                        <span>{{ __('Photo and video') }}</span>
                    </div>
                    <div>
                        <strong>{{ __('Review') }}</strong>
                        <span>{{ __('Status workflow') }}</span>
                    </div>
                </div>
            </aside>

            <main class="auth-main">
                <div class="auth-main-inner">
                    <a href="{{ route('home') }}" class="auth-mobile-brand" wire:navigate>
                        <span class="auth-brand-mark">
                            <span>TR</span>
                        </span>
                        {{ __('Traffic Reports') }}
                    </a>

                    {{ $slot }}
                </div>
            </main>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
