<?php

namespace App\Livewire\Dashboard;

use App\Models\Animal;
use App\Models\BreedingRecord;
use App\Models\HealthRecord;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RecentActivity extends Component
{
    /**
     * Merged, most-recent-first feed of animal additions, health records, and breeding records.
     *
     * @return Collection<int, array{type: string, label: string, date: CarbonInterface, icon: string, iconWrapClass: string, iconTextClass: string}>
     */
    #[Computed]
    public function activities(): Collection
    {
        $recentAnimals = Animal::query()
            ->latest()
            ->limit(8)
            ->get(['id', 'tag_number', 'name', 'created_at'])
            ->map(fn (Animal $animal): array => $this->toActivity(
                type: 'animal_added',
                label: __('New animal added: :tag', ['tag' => $animal->tag_number]),
                date: $animal->created_at,
            ));

        $recentHealthRecords = HealthRecord::query()
            ->with('animal')
            ->whereHas('animal')
            ->latest('date')
            ->limit(8)
            ->get()
            ->map(fn (HealthRecord $record): array => $this->toActivity(
                type: 'health_record',
                label: __(':type recorded for :tag', ['type' => str($record->type->value)->headline(), 'tag' => $record->animal->tag_number]),
                date: $record->date,
            ));

        $recentBreedingRecords = BreedingRecord::query()
            ->with('doe')
            ->whereHas('doe')
            ->latest('mating_date')
            ->limit(8)
            ->get()
            ->map(fn (BreedingRecord $record): array => $this->toActivity(
                type: 'breeding_record',
                label: __('Breeding recorded for :tag', ['tag' => $record->doe->tag_number]),
                date: $record->mating_date,
            ));

        return $recentAnimals->concat($recentHealthRecords)->concat($recentBreedingRecords)
            ->sortByDesc('date')
            ->values()
            ->take(8);
    }

    /**
     * @return array{type: string, label: string, date: CarbonInterface, icon: string, iconWrapClass: string, iconTextClass: string}
     */
    private function toActivity(string $type, string $label, CarbonInterface $date): array
    {
        [$icon, $color] = match ($type) {
            'animal_added' => ['user-plus', 'cyan'],
            'health_record' => ['shield-check', 'green'],
            'breeding_record' => ['sparkles', 'rose'],
            default => ['clock', 'zinc'],
        };

        return [
            'type' => $type,
            'label' => $label,
            'date' => $date,
            'icon' => $icon,
            'iconWrapClass' => $this->iconWrapClasses()[$color],
            'iconTextClass' => $this->iconTextClasses()[$color],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function iconWrapClasses(): array
    {
        return [
            'cyan' => 'bg-cyan-500/10',
            'green' => 'bg-green-500/10',
            'rose' => 'bg-rose-500/10',
            'zinc' => 'bg-zinc-500/10',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function iconTextClasses(): array
    {
        return [
            'cyan' => 'text-cyan-600 dark:text-cyan-400',
            'green' => 'text-green-600 dark:text-green-400',
            'rose' => 'text-rose-600 dark:text-rose-400',
            'zinc' => 'text-zinc-600 dark:text-zinc-400',
        ];
    }
}
