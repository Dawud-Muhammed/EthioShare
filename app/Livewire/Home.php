<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('EthioShare - Trusted Asset Sharing')]
class Home extends Component
{
    public function render()
    {
        return view('welcome');
    }
}
