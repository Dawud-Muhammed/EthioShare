<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Assets')]
class Asset extends Component
{
public $assets = [
        [
            'id' => 1, 
            'title' => 'Toyota Hiace', 
            'status' => 'Available', 
            'earnings' => 'ETB 5,000', 
            'lastBooked' => '2 days ago',
            'image' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?q=80&w=400' 
        ],
    ];

    public $editing = false;
    public $form = ['title' => '', 'status' => 'Available', 'earnings' => '0'];

    public function createAsset() {
        $this->assets[] = array_merge(['id' => count($this->assets) + 1], $this->form);
        $this->reset('form');
    }

    public function deleteAsset($id) {
        $this->assets = array_filter($this->assets, fn($a) => $a['id'] !== $id);
    }

    public function render()
    {
        return view('asset');
    }
}