<x-filament::page>
    <form wire:submit="verify">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-4">
            Verify ID
        </x-filament::button>
    </form>
</x-filament::page>