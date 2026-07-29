<flux:card class="space-y-3">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Health reminders') }}</flux:heading>
        <flux:link href="{{ route('upcoming.index') }}" wire:navigate class="text-sm">{{ __('View all') }}</flux:link>
    </div>
    @if ($this->records->isEmpty())
        <div class="flex flex-col items-center justify-center gap-2 py-8 text-center">
            <flux:icon.check-circle variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('Nothing due in the next 30 days.') }}</flux:text>
        </div>
    @else
        <ul class="divide-y divide-zinc-200 dark:divide-white/10">
            @foreach ($this->records as $healthRecord)
                <li wire:key="dash-health-{{ $healthRecord->id }}" class="flex items-center justify-between gap-2 py-2.5">
                    <div class="min-w-0">
                        <flux:link href="{{ route('animals.show', $healthRecord->animal) }}" wire:navigate class="font-medium">{{ $healthRecord->animal->tag_number }}</flux:link>
                        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ $healthRecord->next_due_date->toFormattedDateString() }}</flux:text>
                    </div>
                    <flux:badge color="blue" size="sm">{{ $healthRecord->type->value }}</flux:badge>
                </li>
            @endforeach
        </ul>
    @endif
</flux:card>
