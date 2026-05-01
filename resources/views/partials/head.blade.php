<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

<script>
    window.loadLeaflet = function () {
        if (window.L) {
            return Promise.resolve();
        }

        if (window.leafletLoading) {
            return window.leafletLoading;
        }

        window.leafletLoading = new Promise((resolve, reject) => {
            if (!document.querySelector('link[data-leaflet-css]')) {
                const css = document.createElement('link');

                css.rel = 'stylesheet';
                css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                css.dataset.leafletCss = 'true';
                css.crossOrigin = '';

                document.head.appendChild(css);
            }

            const script = document.createElement('script');

            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.dataset.leafletJs = 'true';
            script.crossOrigin = '';
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Leaflet could not be loaded.'));

            document.head.appendChild(script);
        });

        return window.leafletLoading;
    };

    window.trafficReportsMap = function (elementId, reports) {
        return {
            map: null,
            escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, (character) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                }[character]));
            },
            async init() {
                await window.loadLeaflet();

                if (!window.L || this.map) {
                    return;
                }

                const element = document.getElementById(elementId);

                if (!element) {
                    return;
                }

                element.innerHTML = '';

                this.map = L.map(elementId).setView([9.082, 8.6753], 6);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(this.map);

                const bounds = [];

                reports.forEach((report) => {
                    const position = [report.latitude, report.longitude];
                    const reporter = report.reporter ? `Reporter: ${this.escapeHtml(report.reporter)}<br>` : '';

                    bounds.push(position);

                    L.marker(position)
                        .addTo(this.map)
                        .bindPopup(`
                            <strong>${this.escapeHtml(report.title)}</strong><br>
                            ${reporter}
                            Status: ${this.escapeHtml(report.status)}<br>
                            Date: ${this.escapeHtml(report.date)}<br>
                            <a href="${report.url}">View report</a>
                        `);
                });

                if (bounds.length > 0) {
                    this.map.fitBounds(bounds, { padding: [30, 30], maxZoom: 15 });
                }

                setTimeout(() => this.map.invalidateSize(), 100);
            },
        };
    };
</script>
