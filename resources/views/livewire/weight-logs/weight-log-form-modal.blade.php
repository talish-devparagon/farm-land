<div>
    <flux:modal wire:model.self="show" class="md:w-full md:max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $weightLogId ? __('Edit weight log') : __('Add weight log') }}
                </flux:heading>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:input wire:model="weight" type="number" step="0.01" min="0" :label="__('Weight (kg)')" />
                <flux:input wire:model="recorded_at" type="date" :label="__('Recorded date')" />
            </div>

            <div class="flex items-center justify-between gap-2">
                <div>
                    @if ($weightLogId)
                        <flux:button variant="danger" wire:click="delete" wire:confirm="{{ __('Are you sure?') }}">
                            {{ __('Delete') }}
                        </flux:button>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <flux:modal.close>
                        <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
