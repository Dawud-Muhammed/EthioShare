<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

 #[Fillable([
    'asset_id',
    'name',
    'description
    location',
    'region',
    'operating_hours_json',
    'contact_person_name',
    'contact_phone',
    'contact_email',
    'qr_checkpoint_enabled',
    'thermal_imaging_enabled',
    'access_code',
    'parking_available',
 ])]
     /**
     * The attributes that should be hidden for serialization arrays.
     * Prevents leakage of PII and security keys over API payloads.
     *
     * @var array<int, string>
     */
 #[Hidden([
    'contact_phone',
    'contact_email',
    'access_code',
    'deleted_at',
 ])]
 #[Casts([
    //--Security: AES-256-GCM Native Encryption at Rest
    'contact_phone' => 'encrypted',
    'contact_email' => 'encrypted',
    'access_code' => 'encrypted',   

    //--jsonb stractured payloads
    'operating_hours_json' > 'array',

    //--boolean
    'qr_checkpoint_enabled' => 'boolean',
    'thermal_imaging_enabled' => 'boolean',
    'parking_available' => 'boolean',
 ])]
class HandoffLocation extends Model
{
    // =====================
    // RELATIONSHIPS
    // =====================   
    public function assets(): BelongsTo{
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    // =====================
    // COMPUTED ATTRIBUTES
    // =====================
    //--  * Determine if the checkpoint possesses advanced automated infrastructure.
    protected function hasSmartInfrastructure():Attribute{
        return Attribute::make(
            get: fn()=> $this->qr_checkpoint_enabled && !is_null($this->access_code)
        )->shouldCache();
    }

    //--* Format a display label summarizing the point's primary operational point.
    
    protected function statusLabel(): Attribute{
        return Attribute::make(
            get: function(){
                if($this->thermal_imaging_enabled){
                    return "premium High-Security Node {{ $this->region }}";
                }
                return "Standard Eco-Handoff Node ({$this->region})";
            }
        )->shouldCache();
    } 

    // =====================
    // SCOPES (QUERY BUILDERS)
    // =====================
    //--* Scope a query to only include handoff locations ready for automated QR check-ins.
    public function scopeAutomated($query){
        return $query->where('qr_checkpoint_enabled',true)
                    ->whereNotNull('access_code');
    }

    //--* Scope a query to filter handoff spots within a target logistics region.
    public function scopeInRegion($query, string $region){
        return $query->where('region', $region);
    }

    // =====================
    // DOMAIN BUSINESS LOGIC
    // =====================    
    //--* Validate an inbound IoT hardware signal's entry key against the encrypted token.
    public function validateGateToken(string $providedCode): bool{
        if(empty($this->access_code)){
            return false;
        }
        return hash_equals($this->access_code, $providedCode);
    }
}
