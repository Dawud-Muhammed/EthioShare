<div class="space-y-6">
    {{-- Header with Search --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <flux:heading size="xl">My Assets</flux:heading>
            <flux:subheading>Manage your listings and track performance.</flux:subheading>
        </div>
        
        <div class="flex items-center gap-2">
            {{-- Search Bar --}}
            <flux:input placeholder="Search assets..." icon="magnifying-glass" wire:model.live.debounce.300ms="search" />
            <flux:button variant="primary" icon="plus" wire:click="createAsset">Add New</flux:button>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <flux:card>
            <flux:subheading>Total Active</flux:subheading>
            <flux:heading size="lg">{{ count($assets) }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:subheading>Monthly Earnings</flux:subheading>
            <flux:heading size="lg">ETB 12,500</flux:heading>
        </flux:card>
        <flux:card>
            <flux:subheading>Pending Bookings</flux:subheading>
            <flux:heading size="lg">3</flux:heading>
        </flux:card>
    </div>

    {{-- Assets Grid --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($assets as $asset)
            <flux:card class="group flex flex-col transition hover:border-emerald-500/50">
                <div class="relative mb-4 h-40 w-full overflow-hidden rounded-lg">
                    <img src="{{ $asset['image'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                    <flux:badge color="emerald" class="absolute right-2 top-2">{{ $asset['status'] }}</flux:badge>
                </div>

                <div class="flex flex-1 flex-col justify-between">
                    <div>
                        <flux:heading size="lg">{{ $asset['title'] }}</flux:heading>
                        <flux:text class="text-xs">Last booked: {{ $asset['lastBooked'] }}</flux:text>
                    </div>

                    <div class="mt-4 flex items-end justify-between border-t border-zinc-100 pt-4 dark:border-zinc-800">
                        <div class="flex flex-col">
                            <flux:subheading class="text-[10px]">Earnings</flux:subheading>
                            <flux:heading size="md">{{ $asset['earnings'] }}</flux:heading>
                        </div>
                        <div class="flex gap-1">
                            <flux:button variant="ghost" size="xs" icon="pencil" />
                            <flux:button variant="ghost" size="xs" icon="chart-bar" />
                            <flux:button variant="ghost" size="xs" color="red" icon="trash" wire:click="deleteAsset({{ $asset['id'] }})" />
                        </div>
                    </div>
                </div>
            </flux:card>
        @empty
            <div class="col-span-full py-16 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">
                <flux:icon.document-plus class="mx-auto h-10 w-10 text-zinc-300" />
                <flux:heading class="mt-2">No assets found</flux:heading>
            </div>
        @endforelse
    </div>
</div>