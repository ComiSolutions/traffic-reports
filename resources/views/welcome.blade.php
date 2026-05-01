<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('Traffic Offence Reporting System') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        <style>
            :root {
                color-scheme: dark;
                font-family: "Instrument Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                --bg: #07110f;
                --panel: #0d1916;
                --panel-2: #101f1b;
                --border: rgba(255, 255, 255, 0.12);
                --text: #f8fafc;
                --muted: #a7b5b0;
                --accent: #34d399;
                --accent-2: #fbbf24;
                --danger: #f87171;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                background:
                    radial-gradient(circle at 20% 10%, rgba(52, 211, 153, 0.18), transparent 28rem),
                    radial-gradient(circle at 90% 35%, rgba(251, 191, 36, 0.12), transparent 24rem),
                    var(--bg);
                color: var(--text);
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .page {
                width: min(1180px, calc(100% - 32px));
                min-height: 100vh;
                margin: 0 auto;
                display: flex;
                flex-direction: column;
                padding: 24px 0;
            }

            .nav {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
            }

            .brand {
                display: inline-flex;
                align-items: center;
                gap: 12px;
                font-weight: 700;
            }

            .brand-mark {
                width: 42px;
                height: 42px;
                display: grid;
                place-items: center;
                border-radius: 10px;
                background: var(--accent);
                color: #052e25;
                font-weight: 800;
            }

            .nav-actions {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 42px;
                padding: 0 16px;
                border-radius: 10px;
                border: 1px solid var(--border);
                color: var(--text);
                font-size: 14px;
                font-weight: 700;
                transition: transform 160ms ease, background 160ms ease, border-color 160ms ease;
            }

            .btn:hover {
                transform: translateY(-1px);
                background: rgba(255, 255, 255, 0.08);
                border-color: rgba(255, 255, 255, 0.2);
            }

            .btn-primary {
                border-color: transparent;
                background: var(--accent);
                color: #052e25;
            }

            .btn-primary:hover {
                background: #6ee7b7;
            }

            .hero {
                flex: 1;
                display: grid;
                grid-template-columns: minmax(0, 1.06fr) minmax(340px, 0.94fr);
                align-items: center;
                gap: 54px;
                padding: 56px 0 38px;
            }

            .eyebrow {
                width: fit-content;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 20px;
                padding: 7px 12px;
                border: 1px solid rgba(52, 211, 153, 0.28);
                border-radius: 999px;
                background: rgba(52, 211, 153, 0.1);
                color: #d1fae5;
                font-size: 14px;
            }

            .dot {
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: var(--accent);
            }

            h1 {
                max-width: 780px;
                margin: 0;
                font-size: clamp(2.6rem, 7vw, 5.8rem);
                line-height: 0.96;
                letter-spacing: 0;
            }

            .lead {
                max-width: 650px;
                margin: 24px 0 0;
                color: var(--muted);
                font-size: clamp(1rem, 2vw, 1.18rem);
                line-height: 1.75;
            }

            .hero-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 32px;
            }

            .preview-shell {
                border: 1px solid var(--border);
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.045);
                padding: 14px;
                box-shadow: 0 24px 80px rgba(0, 0, 0, 0.32);
            }

            .preview {
                border-radius: 14px;
                background: var(--panel);
                padding: 18px;
            }

            .preview-header,
            .report-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
            }

            .small {
                margin: 0;
                color: var(--muted);
                font-size: 13px;
            }

            .title {
                margin: 0 0 4px;
                font-weight: 800;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                padding: 5px 10px;
                background: rgba(251, 191, 36, 0.16);
                color: #fde68a;
                font-size: 12px;
                font-weight: 800;
            }

            .media {
                min-height: 250px;
                margin-top: 18px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 16px;
                background:
                    linear-gradient(135deg, rgba(6, 78, 59, 0.9), rgba(15, 23, 42, 0.96) 58%, rgba(127, 29, 29, 0.82)),
                    repeating-linear-gradient(90deg, transparent 0 38px, rgba(255,255,255,.04) 38px 39px);
            }

            .media-tag {
                align-self: flex-end;
                border-radius: 8px;
                background: rgba(0, 0, 0, 0.3);
                padding: 5px 8px;
                font-size: 12px;
                font-weight: 700;
            }

            .stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
                margin-top: 14px;
            }

            .stat,
            .feature {
                border: 1px solid var(--border);
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.035);
                padding: 14px;
            }

            .stat strong {
                display: block;
                font-size: 28px;
            }

            .features {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
                padding-bottom: 8px;
            }

            .feature strong {
                display: block;
                margin-bottom: 6px;
            }

            .feature p {
                margin: 0;
                color: var(--muted);
                font-size: 14px;
                line-height: 1.55;
            }

            @media (max-width: 900px) {
                .hero {
                    grid-template-columns: 1fr;
                    gap: 32px;
                    padding-top: 42px;
                }
            }

            @media (max-width: 640px) {
                .page {
                    width: min(100% - 24px, 1180px);
                    padding-top: 16px;
                }

                .nav {
                    align-items: flex-start;
                }

                .nav-actions {
                    flex-wrap: wrap;
                    justify-content: flex-end;
                }

                .hide-mobile {
                    display: none;
                }

                .hero-actions,
                .btn {
                    width: 100%;
                }

                .features,
                .stats {
                    grid-template-columns: 1fr;
                }

                .media {
                    min-height: 210px;
                }
            }
        </style>
    </head>
    <body>
        <main class="page">
            <nav class="nav">
                <a href="{{ route('home') }}" class="brand">
                    <span class="brand-mark">TR</span>
                    <span>{{ __('Traffic Reports') }}</span>
                </a>

                @if (Route::has('login'))
                    <div class="nav-actions">
                        @auth
                            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="btn">
                                {{ __('Dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn">
                                {{ __('Log in') }}
                            </a>

                            @if ($canRegister ?? false)
                                <a href="{{ route('register') }}" class="btn btn-primary hide-mobile">
                                    {{ __('Register') }}
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </nav>

            <section class="hero">
                <div>
                    <div class="eyebrow">
                        <span class="dot"></span>
                        {{ __('Real-time offence reporting') }}
                    </div>

                    <h1>{{ __('Report traffic offences with evidence, location, and clear review workflows.') }}</h1>

                    <p class="lead">
                        {{ __('Upload photos or videos, capture GPS coordinates automatically, and track the status of every submitted report from one focused dashboard.') }}
                    </p>

                    <div class="hero-actions">
                        @auth
                            <a href="{{ route('reports.create') }}" class="btn btn-primary">{{ __('Report Offence') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Start Reporting') }}</a>
                        @endauth

                        <a href="{{ Route::has('login') ? route('login') : '#' }}" class="btn">{{ __('Review Reports') }}</a>
                    </div>
                </div>

                <div class="preview-shell">
                    <div class="preview">
                        <div class="preview-header">
                            <div>
                                <p class="title">{{ __('Live Report') }}</p>
                                <p class="small">{{ __('GPS evidence captured') }}</p>
                            </div>
                            <span class="badge">{{ __('Pending') }}</span>
                        </div>

                        <div class="media">
                            <span class="media-tag">{{ __('Video') }}</span>
                            <div>
                                <p class="title">{{ __('Illegal lane obstruction') }}</p>
                                <p class="small">{{ __('6.5244, 3.3792') }}</p>
                            </div>
                        </div>

                        <div class="stats">
                            <div class="stat">
                                <strong>24</strong>
                                <span class="small">{{ __('Submitted') }}</span>
                            </div>
                            <div class="stat">
                                <strong>11</strong>
                                <span class="small">{{ __('Approved') }}</span>
                            </div>
                            <div class="stat">
                                <strong>4</strong>
                                <span class="small">{{ __('Rejected') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="features">
                <div class="feature">
                    <strong>{{ __('Evidence Upload') }}</strong>
                    <p>{{ __('Attach images or videos to support each report.') }}</p>
                </div>
                <div class="feature">
                    <strong>{{ __('GPS Capture') }}</strong>
                    <p>{{ __('Latitude and longitude are recorded during submission.') }}</p>
                </div>
                <div class="feature">
                    <strong>{{ __('Admin Review') }}</strong>
                    <p>{{ __('Reports move through pending, approved, and rejected states.') }}</p>
                </div>
            </section>
        </main>
    </body>
</html>
