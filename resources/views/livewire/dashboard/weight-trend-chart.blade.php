<flux:card class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Weight trend') }}</flux:heading>
        <flux:icon.scale variant="micro" class="size-4 text-violet-600 dark:text-violet-400" />
    </div>
    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Average weight over the last :n months', ['n' => $months]) }}</flux:text>

    {{-- Chart.js rendering wired up by the frontend pass; see App\Livewire\Charts\LineChart. --}}
    <livewire:charts.line-chart
        :series="$this->weightTrend"
        color="violet"
        unit="kg"
        height="h-48"
        :empty-message="__('No weight data recorded yet.')"
    />
</flux:card>
