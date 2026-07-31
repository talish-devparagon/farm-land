/**
 * Alpine is bundled and started by Livewire (via @fluxScripts) and exposed
 * globally as `window.Alpine` — it must not be imported from the `alpinejs`
 * package here, since that would start a second, disconnected Alpine
 * instance that Livewire's own directive scanning never sees. Custom
 * `Alpine.data(...)` components are registered below on the `alpine:init`
 * event, which Livewire dispatches on `document` right before it starts
 * Alpine, so registration always happens in time.
 */

/**
 * Resolve a Tailwind v4 CSS custom property (defined in the `oklch()` color
 * space) to a color string usable as a Chart.js/canvas color. An optional
 * alpha uses the CSS Color 4 "relative color" syntax so the browser handles
 * the oklch → alpha-blended conversion itself — string-concatenating a hex
 * alpha suffix (e.g. `color + '40'`) does not work for non-hex color
 * functions like `oklch()`.
 */
function resolveChartColor(token, shade, alpha = 1) {
    const value = getComputedStyle(document.documentElement)
        .getPropertyValue(`--color-${token}-${shade}`)
        .trim();

    if (!value) {
        return alpha >= 1 ? 'black' : `rgb(0 0 0 / ${alpha})`;
    }

    return alpha >= 1 ? value : `rgb(from ${value} r g b / ${alpha})`;
}

/**
 * Check if the document is in dark mode.
 */
function isDark() {
    return document.documentElement.classList.contains('dark');
}

/**
 * Get the theme-aware color for a given token (e.g., 'cyan', 'emerald'),
 * optionally blended with transparency (0-1).
 */
function themeColor(token, alpha = 1) {
    return resolveChartColor(token, isDark() ? 400 : 500, alpha);
}

/**
 * Global set of live chart instances for reactive theme updates.
 */
const chartInstances = new Set();

/**
 * Register a chart instance for reactive theme updates.
 */
function registerChart(chart, recolorFn) {
    chartInstances.add({ chart, recolorFn });
}

/**
 * Unregister a chart instance when it's destroyed.
 */
function unregisterChart(chart) {
    for (const instance of chartInstances) {
        if (instance.chart === chart) {
            chartInstances.delete(instance);
            break;
        }
    }
}

/**
 * MutationObserver to detect dark/light mode changes and update all charts.
 */
const themeObserver = new MutationObserver(() => {
    chartInstances.forEach(({ chart, recolorFn }) => {
        recolorFn();
        chart.update('none');
    });
});

themeObserver.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class'],
});

document.addEventListener('alpine:init', () => {
/**
 * Alpine bar chart component.
 */
Alpine.data('barChart', (initialData, color, height = 'h-36') => ({
    chartInstance: null,
    color,
    height,

    init() {
        this.$nextTick(() => {
            const canvas = this.$refs.canvas;
            if (!this.$el.dataset.debugId) this.$el.dataset.debugId = Math.random().toString(36).slice(2);
            console.log('[DEBUG barChart init]', this.color, 'elId=', this.$el.dataset.debugId, 'canvasSameElAsElDataset=', canvas === this.$refs.canvas);
            if (!canvas) return;

            // Guard against a duplicate init pass re-creating a chart on a
            // canvas that already has one attached — Chart.js throws if you
            // don't destroy the existing instance first.
            const existing = Chart.getChart(canvas);
            console.log('[DEBUG barChart existing?]', this.color, !!existing);
            if (existing) {
                this.chartInstance = existing;
                return;
            }

            const borderColor = themeColor(this.color);
            const backgroundColor = themeColor(this.color, 0.125);

            this.chartInstance = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: initialData.labels,
                    datasets: [
                        {
                            data: initialData.values,
                            borderColor,
                            backgroundColor,
                            borderWidth: 4,
                            fill: true,
                            borderRadius: 8,
                            pointRadius: 0,
                            hoverBackgroundColor: themeColor(this.color, 0.25),
                        },
                    ],
                },
                options: {
                    indexAxis: 'x',
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: false,
                            },
                            ticks: {
                                color: isDark() ? '#a1a1aa' : '#71717a',
                                font: {
                                    size: 12,
                                },
                            },
                        },
                        y: {
                            display: false,
                            beginAtZero: true,
                        },
                    },
                },
            });

            registerChart(this.chartInstance, () => {
                this.chartInstance.data.datasets[0].borderColor = themeColor(this.color);
                this.chartInstance.data.datasets[0].backgroundColor = themeColor(this.color, 0.125);
                this.chartInstance.data.datasets[0].hoverBackgroundColor = themeColor(this.color, 0.25);
            });

            Alpine.onElRemoved(this.$el, () => this.destroy());

            this.$wire.on('chart-data-updated', ({ chartData }) => {
                console.log('[DEBUG barChart chart-data-updated fired]', this.color, JSON.stringify(chartData));
                this.chartInstance.data.labels = chartData.labels;
                this.chartInstance.data.datasets[0].data = chartData.values;
                this.chartInstance.update('none');
            });
        });
    },

    destroy() {
        if (this.chartInstance) {
            unregisterChart(this.chartInstance);
            this.chartInstance.destroy();
        }
    },
}));

/**
 * Alpine line chart component with optional gradient and gap handling.
 */
Alpine.data('lineChart', (initialData, color, unit = '', hasData = true) => ({
    chartInstance: null,
    color,
    unit,
    hasData,

    init() {
        this.$nextTick(() => {
            const canvas = this.$refs.canvas;
            if (!canvas) return;

            const existing = Chart.getChart(canvas);
            if (existing) {
                this.chartInstance = existing;
                return;
            }

            const ctx = canvas.getContext('2d');

            const buildGradient = () => {
                const lineColor = themeColor(this.color);
                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                gradient.addColorStop(0, themeColor(this.color, 0.25));
                gradient.addColorStop(1, themeColor(this.color, 0));
                return { lineColor, gradient };
            };

            const { lineColor, gradient } = buildGradient();

            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: initialData.labels,
                    datasets: [
                        {
                            data: initialData.values,
                            borderColor: lineColor,
                            backgroundColor: gradient,
                            borderWidth: 4,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointBackgroundColor: lineColor,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            spanGaps: false,
                            tension: 0.3,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: isDark() ? '#27272a' : '#fafafa',
                            titleColor: isDark() ? '#fafafa' : '#27272a',
                            bodyColor: isDark() ? '#fafafa' : '#27272a',
                            borderColor: isDark() ? '#52525b' : '#e4e4e7',
                            borderWidth: 1,
                            padding: 8,
                            displayColors: false,
                            callbacks: {
                                label: (context) => {
                                    if (context.raw === null) return '';
                                    return `${context.raw}${this.unit ? ' ' + this.unit : ''}`;
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: false,
                            },
                            ticks: {
                                color: isDark() ? '#a1a1aa' : '#71717a',
                                font: {
                                    size: 12,
                                },
                            },
                        },
                        y: {
                            display: false,
                            beginAtZero: true,
                        },
                    },
                },
            });

            registerChart(this.chartInstance, () => {
                const { lineColor: newLineColor, gradient: newGradient } = buildGradient();
                this.chartInstance.data.datasets[0].borderColor = newLineColor;
                this.chartInstance.data.datasets[0].backgroundColor = newGradient;
                this.chartInstance.data.datasets[0].pointBackgroundColor = newLineColor;
            });

            Alpine.onElRemoved(this.$el, () => this.destroy());

            this.$wire.on('chart-data-updated', ({ chartData, hasData: newHasData }) => {
                this.chartInstance.data.labels = chartData.labels;
                this.chartInstance.data.datasets[0].data = chartData.values;

                if (newHasData !== undefined) {
                    this.hasData = newHasData;
                }

                this.chartInstance.update('none');
            });
        });
    },

    destroy() {
        if (this.chartInstance) {
            unregisterChart(this.chartInstance);
            this.chartInstance.destroy();
        }
    },
}));

/**
 * Alpine donut chart component with centered total display.
 */
Alpine.data('donutChart', (initialData) => ({
    chartInstance: null,

    init() {
        this.$nextTick(() => {
            const canvas = this.$refs.canvas;
            if (!canvas) return;

            const existing = Chart.getChart(canvas);
            if (existing) {
                this.chartInstance = existing;
                return;
            }

            const colors = initialData.colorTokens.map(token => themeColor(token));

            this.chartInstance = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: initialData.labels,
                    datasets: [
                        {
                            data: initialData.values,
                            backgroundColor: colors,
                            borderWidth: 0,
                        },
                    ],
                },
                options: {
                    cutout: '70%',
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: isDark() ? '#27272a' : '#fafafa',
                            titleColor: isDark() ? '#fafafa' : '#27272a',
                            bodyColor: isDark() ? '#fafafa' : '#27272a',
                            borderColor: isDark() ? '#52525b' : '#e4e4e7',
                            borderWidth: 1,
                            padding: 8,
                            displayColors: false,
                            callbacks: {
                                label: (context) => {
                                    return `${context.label}: ${context.raw}`;
                                },
                            },
                        },
                    },
                },
            });

            registerChart(this.chartInstance, () => {
                const newColors = initialData.colorTokens.map(token => themeColor(token));
                this.chartInstance.data.datasets[0].backgroundColor = newColors;
            });

            Alpine.onElRemoved(this.$el, () => this.destroy());
        });
    },

    destroy() {
        if (this.chartInstance) {
            unregisterChart(this.chartInstance);
            this.chartInstance.destroy();
        }
    },
}));
});
