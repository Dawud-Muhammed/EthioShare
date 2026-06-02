<?php

namespace App\Models;

use App\Shared\Enums\Media\MediaType;
use App\Shared\Enums\Media\MediaPurpose;
use App\Shared\Enums\Media\VirusScanStatus;
use App\Shared\Enums\Media\ContentModerationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// =====================
// CONFIGURATION
// =====================
//--
#[Fillable([
    'media_type',
    'mediable_type',
    'mediable_id',
    'file_name',
    'mime_type',
    'file_size_bytes',
    'file_hash',
    'disk_name',
    'disk_path',
    'cdn_url',
    'purpose',
    'upload_reason',
    'is_primary',
    'virus_scan_status',
    'content_moderation_status',
    'is_encrypted',
    'encryption_key_id',
    'uploaded_by_id',
])]
#[Hidden([
    'disk_path',
    'encryption_key_id',
    'deleted_at',
])]
class Media extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    // =====================
    // CASTING PIPELINE
    // =====================

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Infrastructure Security
            'encryption_key_id' => 'encrypted',

            // Strict Enums (State & Categorization)
            'media_type' => MediaType::class,
            'purpose' => MediaPurpose::class,
            'virus_scan_status' => VirusScanStatus::class,
            'content_moderation_status' => ContentModerationStatus::class,

            // Booleans & Integers
            'is_primary' => 'boolean',
            'is_encrypted' => 'boolean',
            'file_size_bytes' => 'integer',
        ];
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    /**
     * The polymorphic relationship mapping this media to its parent entity.
     * Maps to User, Asset, Booking, or Dispute models.
     */
    public function mediable(): MorphTo
    {
        // Explicitly defining the type and id columns prevents mapping failures
        return $this->morphTo(__FUNCTION__, 'mediable_type', 'mediable_id');
    }

    /**
     * The user who uploaded this specific media file.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    // =====================
    // COMPUTED ATTRIBUTES
    // =====================

    /**
     * Resolve the public-facing URL for the media asset.
     * Prioritizes CDN for bandwidth optimization.
     */
    protected function publicUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cdn_url ?? config("filesystems.disks.{$this->disk_name}.url") . '/' . $this->disk_path
        )->shouldCache();
    }

    /**
     * Determine if the file has passed all automated compliance checks.
     */
    protected function isVerifiedSafe(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->virus_scan_status === VirusScanStatus::PASSED 
                       && $this->content_moderation_status === ContentModerationStatus::APPROVED
        )->shouldCache();
    }

    // =====================
    // SCOPES (QUERY BUILDERS)
    // =====================

    /**
     * Scope to quickly isolate the primary cover photo for an asset.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope to filter out unmoderated or flagged content.
     */
    public function scopeSafeForPublic($query)
    {
        return $query->where('virus_scan_status', VirusScanStatus::PASSED)
                     ->where('content_moderation_status', ContentModerationStatus::APPROVED);
    }

    // =====================
    // DOMAIN BUSINESS LOGIC
    // =====================

    /**
     * Flag a file as malicious and quarantine it immediately.
     */
    public function quarantine(string $reason): void
    {
        $this->update([
            'virus_scan_status' => VirusScanStatus::FLAGGED,
            'content_moderation_status' => ContentModerationStatus::REJECTED,
            'upload_reason' => $reason, // Repurposing column temporarily or appending admin notes
            'is_primary' => false,
        ]);
    }
}