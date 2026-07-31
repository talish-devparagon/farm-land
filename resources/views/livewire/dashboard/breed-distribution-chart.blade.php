<flux:card class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Breed distribution') }}</flux:heading>
        <flux:icon.chart-pie variant="micro" class="size-4 text-indigo-600 dark:text-indigo-400" />
    </div>
    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Alive animals by breed') }}</flux:text>

    {{-- Chart.js rendering wired up by the frontend pass; see App\Livewire\Charts\DonutChart. --}}
    <livewire:charts.donut-chart
        :segments="$this->segments->all()"
        :total="$this->total"
        total-label="{{ __('alive') }}"
        :empty-message="__('No alive animals to chart yet.')"
    />
</flux:card>
