<section class="w-full max-w-2xl">
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Farm Settings') }}</flux:heading>
        <flux:subheading size="lg">{{ __('Manage your farm profile and details') }}</flux:subheading>
        <flux:separator variant="subtle" class="mt-6" />
    </div>

    <flux:card class="space-y-6">
        <form wire:submit="update" class="space-y-6">
            <flux:input
                wire:model="name"
                :label="__('Farm name')"
                type="text"
                required
                autofocus
                autocomplete="organization"
            />

            <flux:input
                wire:model="location"
                :label="__('Location')"
                type="text"
                placeholder="{{ __('e.g. Springfield, IL') }}"
            />

            <flux:input
                wire:model="phone"
                :label="__('Phone number')"
                type="tel"
                placeholder="{{ __('Optional') }}"
            />

            <flux:field>
                <flux:label>{{ __('Status') }}</flux:label>

                @if ($this->canEditStatus)
                    <flux:select wire:model="status">
                        @foreach (\App\Enums\FarmStatus::cases() as $case)
                            <flux:select.option value="{{ $case->value }}">
                                {{ __(ucfirst($case->value)) }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="status" />
                @else
                    <div>
                        <x-farm-status-badge :status="$status" />
                    </div>
                @endif
            </flux:field>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:card>
</section>
