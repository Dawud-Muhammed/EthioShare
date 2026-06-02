<?php

namespace App\Models;

use App\Shared\Enums\Asset\StatusEnum;
use App\Shared\Enums\Asset\VisibilityEnum;
use App\Shared\Enums\Asset\TypeEnum;
use App\Shared\Enums\Asset\ConditionEnum;
use App\Shared\Enums\Asset\DeliveryMethodEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Casts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'owner_id',
    'title',
    'description',
    'asset_type',
    'condition',
    'hourly_rate',
    'daily_rate',
    'weekly_rate',
    'monthly_rate',
    'security_deposit',
    'estimated_value',
    'available_from',
    'available_until',
    'location',
    'region',
    'service_radius_km',
    'delivery_method',
    'specifications',
    'features',
    'status',
    'status_updated_at',
    'visibility',
    'average_rating',
    'total_reviews',
    'total_bookings',
    'total_rental_hours'
])]
#[Hidden([
    'deleted_at'
])]
#[Casts([
//--enums
    'asset_type' => TypeEnum::Class,
    'condition'  => ConditionEnum::class,
    'delivery_method'=> DeliveryMethodEnum::class,
    'status' => StatusEnum::class,
    'visibility' => VisibilityEnum::class,

//--Decimals [financial and geospatial]
    'hourly_rate' => 'decimal:2',
    'daily_rate' => 'decimal:2',
    'weekly_rate' => 'decimal:2',
    'monthly_rate' => 'decimal:2',
    'security_deposit' => 'decimal:2',
    'estimated_value' => 'decimal:2',
    'service_radius_km' => 'decimal:2',
    'average_rating' => 'decimal:2',

//--jsonb payloads
    'specifications' => 'array',
    'features' => 'array',    
 
//--dates
    'available_from' => 'date',
    'available_until' => 'date', 
    'status_updated_at' => 'datetime',
//--intgers
    'total_reviews' => 'integer',
    'total_bookings' => 'integer',
    'total_rental_hours' => 'integer',    
])]
class Asset extends Model
{
    // =====================
    // RELATIONSHIPS
    // =====================
    public function owner(): BelongsTo{
        return $this->belongsTo(User::class,'owner_id');
    }
    public function bookings(): HasMany{
        return $this->hasMany(Booking::class,'asset_id');
    }
    public function handOffLocations(): HasMany{
        return $this->hasMany(HandoffLocation::class,'asset_id');
    }
    public function media():MorphMany{
        return $this->morphMany(Media::class, 'mediable');
    }
    public function reviews(): MorphMany{
        return $this->morphMany(Review::class, 'reviewable');
    }

    // =====================
    // COMPUTED ATTRIBUTES
    // =====================
    //--Determine if the asset is currently available for rent.
    public function isAvailableForRent(): Attribute{
        return Attribute::make(
            get: fn()=> $this->status === StatusEnum::ACTIVE 
                      && $this->visibility === VisibilityEnum::PUBLIC
                      && (is_null($this->avaulable_from) || $this->available_from->isPast())
                      &&(is_null($this->available_untill) || $this->available_untill->isFuture())
        )->shouldCache();
    }

    /**
     * Parse PostGIS WKB/WKT location into a structured array.
     * (Assuming spatial data isn't handled by a dedicated package here).
    */
    protected function coordinates(): Attribute{
        return Attribute::make(
            get: function() {
                // In a true PostGIS setup, $this->location returns standard WKB/WKT.
                // You would extract latitude/longitude here or rely on a spatial package.
                return[
                    'latitude' => null,
                    'altitude' => null,
                ];
            }
        )->shouldCache();
    }

    // =====================
    // SCOPES (QUERY BUILDERS)
    // =====================
    public function scopeActive($query){
        return $query->where('status',StatusEnum::ACTIVE);
    }    
    public function scopePubliclyVisible($query){
        return $query->where('visibility', VisibilityEnum::PUBLIC);
    }

    // =====================
    // DOMAIN BUSINESS LOGIC
    // =====================
    //-- * Safely transition the asset state to published.
    
    public function publish(): void{
        $this->update([
            'status' => StatusEnum::ACTIVE,
            'status_updated_at' => now(),
        ]);
    }
}
