<?php

declare(strict_types=1);

namespace App\Livewire\Assets;

use App\Domains\Shared\Enums\Asset\StatusEnum;
use App\Domains\Shared\Enums\Media\MediaPurpose;
use App\Models\Asset;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

// ↑ WithPagination is a Livewire trait that integrates Laravel's
// paginator with Livewire's reactivity.
// When page changes, Livewire re-renders only the list — no full reload.

class Index extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    // ↑ Empty string means no filter — show all statuses.
    // When owner selects a filter, this updates and
    // Livewire automatically re-runs render() with the new filter.

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        // ↑ updatedXxx() is a Livewire lifecycle hook.
        // It runs automatically when $statusFilter changes.
        // resetPage() resets pagination to page 1.
        // Without this, changing filter while on page 3
        // would show page 3 of the new filter — confusing.
    }

    public function render(): View
    {
        $query = Asset::query()
            ->where('owner_id', auth()->id())
            ->when($this->statusFilter !== '', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->with([
                'media' => fn ($q) => $q
                    ->where('purpose', MediaPurpose::ASSET_PHOTO)
                    ->where('is_primary', true)
                    ->limit(1),
            ])
            ->withCount('media')
            ->orderBy('created_at', 'desc');

        return view('livewire.assets.index', [
            'assets' => $query->paginate(10),
            'statuses' => StatusEnum::values(),
            // ↑ Pass all status values for the filter dropdown.
        ])->layout('layouts.app');
    }
}
