<div class="flex h-full w-full flex-1 flex-col gap-6">
    
    <div>
        <flux:heading size="xl" level="1">Welcome back, {{ auth()->user()->name ?? 'Abebe' }}</flux:heading>
        <flux:subheading size="lg">Manage your bookings, assets, and reputation from one place.</flux:subheading>
    </div>

    <div class="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
        @foreach($overviewStats as $stat)
            <flux:card class="flex items-start justify-between gap-3">
                <div>
                    <flux:subheading>{{ $stat['label'] }}</flux:subheading>
                    <flux:heading size="xl" class="mt-2">{{ $stat['value'] }}</flux:heading>
                    <flux:text class="mt-1 text-xs">{{ $stat['subtitle'] }}</flux:text>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                    <flux:icon name="{{ $stat['icon'] }}" variant="mini" />
                </div>
            </flux:card>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.6fr_0.9fr]">
        
        <flux:card>
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <flux:heading>Recent activity</flux:heading>
                    <flux:subheading>Latest bookings, transactions, and reputation events.</flux:subheading>
                </div>
                <flux:badge color="emerald" size="sm">{{ count($recentActivity) }} items</flux:badge>
            </div>

            <div class="space-y-4">
                @foreach($recentActivity as $activity)
                    <div class="flex gap-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 p-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-600 dark:bg-{{ $activity['color'] }}-500/20 dark:text-{{ $activity['color'] }}-400">
                            <flux:icon name="{{ $activity['icon'] }}" variant="micro" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:heading size="sm">{{ $activity['title'] }}</flux:heading>
                                <flux:badge color="zinc" size="sm">{{ $activity['meta'] }}</flux:badge>
                            </div>
                            <flux:text class="mt-1 text-sm">{{ $activity['description'] }}</flux:text>
                            <flux:text class="mt-2 text-xs font-medium">{{ $activity['date'] }}</flux:text>
                        </div>
                    </div>
                @endforeach
            </div>
        </flux:card>

        <div class="space-y-6">
            
            <flux:card class="bg-zinc-900 dark:bg-zinc-950 text-white border-none shadow-md">
                <flux:subheading class="text-zinc-400">Quick actions</flux:subheading>
                <flux:heading size="lg" class="mt-1 text-white">Move faster</flux:heading>
                <flux:text class="mt-2 text-sm text-zinc-300">Continue with the most common account actions.</flux:text>
                
                <div class="mt-6 grid gap-3">
                    <flux:button variant="primary" icon="calendar" class="w-full justify-center">
                        Book an asset
                    </flux:button>
                    <flux:button variant="filled" icon="plus-circle" class="w-full justify-center bg-zinc-800 hover:bg-zinc-700 text-white border-zinc-700">
                        List asset
                    </flux:button>
                    <flux:button variant="filled" icon="user" :href="route('profile.edit')" wire:navigate class="w-full justify-center bg-zinc-800 hover:bg-zinc-700 text-white border-zinc-700">
                        View profile
                    </flux:button>
                </div>
            </flux:card>

            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <flux:heading>Profile summary</flux:heading>
                        <flux:subheading>{{ auth()->user()->name ?? 'Abebe Tekle' }} · Oromia</flux:subheading>
                    </div>
                    <flux:badge color="amber" size="sm">GOLD</flux:badge>
                </div>
                
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-800/50 p-3">
                        <dt class="text-zinc-500 dark:text-zinc-400">Email</dt>
                        <dd class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">{{ auth()->user()->email ?? 'abebe@example.et' }}</dd>
                    </div>
                    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-800/50 p-3">
                        <dt class="text-zinc-500 dark:text-zinc-400">Phone</dt>
                        <dd class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">+251911234567</dd>
                    </div>
                    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-800/50 p-3">
                        <dt class="text-zinc-500 dark:text-zinc-400">Trust score</dt>
                        <dd class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">82.3</dd>
                    </div>
                    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-800/50 p-3">
                        <dt class="text-zinc-500 dark:text-zinc-400">Region</dt>
                        <dd class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">Oromia</dd>
                    </div>
                </dl>
            </flux:card>
        </div>
    </div>
</div>