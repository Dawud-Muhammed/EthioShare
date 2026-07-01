<?php

namespace App\Livewire\Bookings;

use App\Domains\Bookings\Actions\SubmitReviewAction;
use App\Models\Review;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewIndex extends Component
{
    use WithPagination;

    // Active tab — 'given' (reviews I wrote) or 'received' (reviews on my assets)
    public string $activeTab = 'given';

    // Switching tabs resets pagination back to page 1
    // so you never land on page 3 of a tab that only has 1 page
    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // Computed property — reviews for the current tab, paginated
    // Livewire re-evaluates this on every render cycle when
    // $activeTab or the page number changes
    public function getReviewsProperty(): LengthAwarePaginator
    {
        if ($this->activeTab === 'received') {
            // Reviews left on assets owned by the authenticated user
            return Review::with(['reviewer', 'reviewable'])
                ->where('reviewable_type', 'Asset')
                ->whereHas('reviewable', function ($query) {
                    $query->where('owner_id', Auth::id());
                })
                ->latest()
                ->paginate(10);
        }

        // Default: reviews written by the authenticated user
        return Review::with(['reviewable'])
            ->where('reviewer_id', Auth::id())
            ->latest()
            ->paginate(10);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.bookings.review-index')
            ->layout('layouts.app');
    }
}