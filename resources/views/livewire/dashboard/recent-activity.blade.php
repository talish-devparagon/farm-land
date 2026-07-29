<flux:card class="space-y-3">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Recent activity') }}</flux:heading>
        <flux:icon.sparkles variant="micro" class="size-4 text-amber-500 dark:text-amber-400" />
    </div>
    @if ($this->activities->isEmpty())
        <div class="flex flex-col items-center justify-center gap-2 py-8 text-center">
            <flux:icon.clock variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('No recent activity yet.') }}</flux:text>
        </div>
    @else
        <ul class="space-y-4">
            @foreach ($this->activities as $activity)
                <li wire:key="dash-activity-{{ $loop->index }}" class="flex items-start gap-3">
                    <span class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-full {{ $activity['iconWrapClass'] }} {{ $activity['iconTextClass'] }}">
                        <flux:icon :name="$activity['icon']" variant="micro" class="size-4" />
                    </span>
                    <div class="min-w-0">
                        <flux:text class="leading-snug">{{ $activity['label'] }}</flux:text>
                        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ $activity['date']->diffForHumans() }}</flux:text>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</flux:card>
