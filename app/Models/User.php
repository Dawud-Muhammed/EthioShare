<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Shared\Enums\User\AccountStatusEnum;
use App\Shared\Enums\User\BusinessTypeEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

    // =====================
    // CONFIGURATION
    // =====================
    //--The attributes that are mass assignable.

#[Fillable([
    'email', 
    'email_verified_at', 
    'phone_number', 
    'password', 
    'fayda_id',  
    'fayda_verified_at',
    'kyc_tier',
    'kyc_tier_verified_at',
    'kyc_metadata',
    'first_name',
    'last_name',
    'business_name',
    'business_registration_number',
    'business_type',
    'country_region',
    'city',
    'address_line_1',
    'address_line_2',
    'postal_code',
    'location',
    'account_status',
    'is_verified',
    'is_two_factor_enabled',
    'total_trust_score',
    'trust_score_updated_at',
    'last_login_at',
])]

    //--The attributes that should be hidden for serialization.
#[Hidden([
    'password', 
    'two_factor_secret', 
    'two_factor_recovery_codes', 
    'remember_token',
    'fayda_id',
    'phone_number',
    'business_registration_number',
])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable, HasUlids, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array{
        return[
            'password' => 'hashed',

            //--Security: AES-256-GCM Encryption at Rest
            'fayda_id' => 'encrypted',
            'phone_number' => 'encrypted',
            'business_verification_number' => 'encrypted',

            //--JSON & Enums
            'kyc_metadata' => 'array',
            'business_type' => BusinessTypeEnum::class,
            'account_status' => AccountStatusEnum::class,

            //--Booleans and Decimals
            'is_two_factor_enabled' => 'boolean',
            'is_verified' => 'boolean',
            'total_trust_score' => 'decimal:2',

            //--dates
            'email_verified_at' => 'datetime',
            'fayda_verified_at' => 'datetime',
            'kyc_tier_verified_at' => 'datetime',
            'trust_score_updated_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    public function assets(): HasMany{
        return $this->hasMany(Asset::class, 'owner_key');
    }
    public function bookingAsRenter(): HasMany{
        return $this->hasMany(Booking::class, 'renter_id');
    }
    public function bookingAsOwner(): HasMany{
        return $this->hasMany(Booking::class, 'owner_id');
    }
    public function trustScores():HasOne{
        return $this->hasone(TrustScore::class);
    }
    public function reviewsAsReviwer():HasMany{
        return $this->hasMany(Review::class, 'reviwer_id');
    }
    public function disputesInitiated(): HasMany{
        return $this->hasMany(Dispute::class, 'initiator_id');
    }
    public function disputesReceived(): HasMany{
        return $this->hasMany(Dispute::class, 'respondent_id');
    }
    public function media():MorphMany{
        return $this->morphMany(Media::class, 'mediable');
    }
    
    // =====================
    // COMPUTED ATTRIBUTES
    // =====================

    //--* Get the user's full name.
    protected function fullName(): Attribute{
        return Attribute::make(
            get:fn ()=>trim("{$this->first_name} {$this->last_name}")
        )->shouldCache();
    }
    
    //--* Determine if the user operates as a business entity.
    protected function isCorporateEntity(): Attribute{
        return Attribute::make(
            get: fn()=>in_array($this->business_type,[
                BusinessTypeEnum::SME,
                BusinessTypeEnum::CORPORATIVE,
                BusinessTypeEnum::COOPERATIVE
            ])
        );
    }

    // =====================
    // SCOPES (QUERY BUILDERS)
    // =====================

    public function scopeActive($query){
        return $query->where('account_status', AccountStatusEnum::ACTIVE);
    }
    public function scopeRequiresKycUpgrade($query){
        return $query->where('kyc_tier','<',2);
    }

    // =====================
    // DOMAIN BUSINESS LOGIC
    // =====================
    //-- * Check if the user is eligible to rent heavy machinery.

    public function canRentHeavyMachinery(): bool{
        return $this->is_verified && $this->kyc_tier >= 2 && $this->account_status === AccountStatusEnum::ACTIVE;
    }
    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}