<?php

declare(strict_types=1);

namespace App\Livewire\Assets;

use Livewire\Component;

class Browse extends Component
{
    public function render()
    {
        return view('livewire.assets.browse')
            ->layout('layouts.marketplace');
        // ↑ This is the connection between the page and the layout.
        // It says: "render my view, but wrap it inside marketplace.blade.php"
        // The content of browse.blade.php fills the $slot in the layout.
        // This is why $slot exists in layouts — Livewire fills it automatically.
    }
}