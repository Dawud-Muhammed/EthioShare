<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Booking;
use App\Domains\Shared\Enums\Booking\BookingStatusEnum;

class BookingIndex extends Component
{
    // Why WithPagination trait?
    // This trait gives you $this->resetPage() and wires
    // Livewire's pagination to Laravel's paginator automatically.
    // Without it, changing filters wouldn't reset to page 1 —
    // you'd be on page 3 of filtered results with no page 3.
    use WithPagination;

    // =====================
    // PROPERTIES
    // =====================

    // Why $activeTab as a string property?
    // The tab the user is on is UI state. Livewire persists
    // UI state as public properties. When Livewire re-renders,
    // it reads $activeTab and shows the right content.
    // 'renter' = "my rentals", 'owner' = "my asset bookings"
    public string $activeTab = 'renter';

    // Why $statusFilter?
    // Users want to filter by status — show only PENDING bookings,
    // or only COMPLETED bookings. Empty string means "show all."
    public string $statusFilter = '';

    // =====================
    // LIFECYCLE HOOKS
    // =====================

    // Why updatedActiveTab and updatedStatusFilter?
    // These are Livewire lifecycle hooks. They fire automatically
    // when the named property changes. Their job: reset pagination
    // back to page 1 whenever the user switches tabs or filters.
    //
    // Without these, switching from "renter" tab to "owner" tab
    // while on page 3 would show page 3 of the owner results —
    // which might not exist. Always reset page on filter change.
    public function updatedActiveTab(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    // =====================
    // COMPUTED PROPERTIES
    // =====================

    #[\Livewire\Attributes\Computed]
    public function bookings()
    {
        // Why start with a base query and add conditions?
        // This pattern is called a query builder chain. You build
        // the query piece by piece instead of writing multiple
        // separate queries. The database only gets hit once at
        // the end when ->paginate() is called.
        $query = Booking::query()
            // Why eager load here and not in mount()?
            // mount() runs once. This computed property runs every
            // time the component re-renders (tab switch, filter change,
            // page change). The with() here ensures relationships are
            // always loaded fresh with each query.
            ->with(['asset', 'renter', 'owner']);

        // Why check $activeTab to decide which column to filter on?
        // The renter's bookings are in renter_id.
        // The owner's bookings are in owner_id.
        // Same table, same model, different perspective.
        if ($this->activeTab === 'renter') {
            $query->where('renter_id', auth()->id());
        } else {
            $query->where('owner_id', auth()->id());
        }

        // Why only apply status filter when it's not empty?
        // Empty string means "show all statuses." If you always
        // applied the filter, an empty string would try to match
        // booking_status = '' and return zero results.
        if ($this->statusFilter !== '') {
            $query->where(
                'booking_status',
                BookingStatusEnum::from($this->statusFilter)
            );
        }

        // Why latest()?
        // Most recent bookings appear first. A user who just made
        // a booking should see it at the top, not buried on page 4.
        return $query->latest()->paginate(10);
    }

    // Why statusOptions as a computed property?
    // The status filter dropdown needs a list of options.
    // Building this from the enum means it updates automatically
    // if you add a new status in Phase 2. No hardcoded arrays
    // in the Blade view.
    #[\Livewire\Attributes\Computed]
    public function statusOptions(): array
    {
        return BookingStatusEnum::cases();
    }

    #[\Livewire\Attributes\Computed]
    public function pendingOwnerCount(): int{
        
        return Booking::where('owner_id', auth()->id())
            ->where('booking_status', BookingStatusEnum::PENDING)
            ->count();
    }
    // =====================
    // RENDER
    // =====================

    public function render()
    {
        return view('livewire.bookings.index')
            ->layout('layouts.app');
    }
}