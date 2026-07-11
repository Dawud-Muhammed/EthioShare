<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Overview')]
class Dashboard extends Component
{
    public array $overviewStats = [];

    public array $recentActivity = [];

    public function mount()
    {
        $this->overviewStats = [
            ['label' => 'Total bookings', 'value' => '34', 'subtitle' => 'completed', 'icon' => 'calendar-days'],
            ['label' => 'Trust score', 'value' => '78.5', 'subtitle' => 'GOLD tier', 'icon' => 'shield-check'],
            ['label' => 'Earnings', 'value' => 'ETB 45,230', 'subtitle' => 'this month', 'icon' => 'currency-dollar'],
            ['label' => 'Active disputes', 'value' => '0', 'subtitle' => 'open', 'icon' => 'scale'],
        ];

        $this->recentActivity = [
            ['title' => 'Booking completed', 'meta' => 'Booking', 'description' => 'Sonalika Tractor finished successfully.', 'date' => 'May 20, 2026', 'icon' => 'check-circle', 'color' => 'emerald'],
            ['title' => 'Escrow released', 'meta' => 'Transaction', 'description' => 'ETB 2,500 moved from escrow to the owner.', 'date' => 'May 21, 2026', 'icon' => 'banknotes', 'color' => 'amber'],
            ['title' => 'KYC upgraded to GOLD', 'meta' => 'Trust', 'description' => 'Verification review completed and trust tier increased.', 'date' => 'May 10, 2026', 'icon' => 'shield-check', 'color' => 'sky'],
        ];
    }

    public function render()
    {
        return view('dashboard');
    }
}
