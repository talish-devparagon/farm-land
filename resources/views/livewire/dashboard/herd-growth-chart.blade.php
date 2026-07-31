<flux:card class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Herd growth') }}</flux:heading>
        <flux:icon.arrow-trending-up variant="micro" class="size-4 text-cyan-600 dark:text-cyan-400" />
    </div>
    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('New intake over the last :n months', ['n' => $months]) }}</flux:text>
    {{-- Chart.js rendering wired up by the frontend pass; see App\Livewire\Charts\BarChart. --}}
    <livewire:charts.bar-chart :series="$this->herdGrowth" height="h-48"/>
</flux:card>
