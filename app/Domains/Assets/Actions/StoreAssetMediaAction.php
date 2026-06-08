<?php
declare(strict_types = 1);

namespace App\Domains\Assets\Actions;

use App\Domains\Shared\Enums\Media\MediaPurpose;
use App\Domains\Shared\Enums\Media\MediaType;
use App\Models\Asset;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreAssetMediaAction{
    public function execute(array $files, Asset $asset, User $owner, int $primaryIndex = 0): Collection{
        return DB::transaction(function () use ($files, $asset, $owner, $primaryIndex){
            $createMedia = collect();

            $this->demoteExistingPrimary($asset, $primaryIndex, count($files));

            foreach($files as $index => $file){

                $isPrimary = ($index === $primaryIndex);
                $storedPath = $this->storeFile($file, $asset);

                $media = Media::create([
                    'media_type' => MediaType::PHOTO,
                    'mediable_type' => Asset::class,
                    'mediable_id' => $asset->id,
                    'file_name'   => $file->getClientOriginalName(),
                    'mime_type'   => $file->getMimeType(),
                    'file_size_bytes' => $file->getSize(),
                    'file_hash'     => hash_file('sha256', $file->getRealPath()),
                    // TODO: Phase 2 — check for duplicate hash before storing.

                    'disk_name'     => 'local',
                    // ↑ Hardcoded for Phase 1. Local disk.
                    // TODO: Phase 2 — use config('filesystems.default')
                    // So switching to S3 only requires changing one config value.

                    'disk_path'     => $storedPath,
                    // ↑ The path returned by storeFile().

                    'cdn_url'       => null,
                    // ↑ No CDN in Phase 1. Phase 2 this becomes the S3/CDN URL.
                    // TODO: Phase 2 — generate CDN URL after S3 upload.

                    'purpose' => MediaPurpose::ASSET_PHOTO,
                    'is_primary' => $isPrimary,

                    'virus_scan_status'        => null,
                    // ↑ Phase 1: we skip virus scanning.
                    // TODO: Phase 2 — set to VirusScanStatus::PENDING
                    // and dispatch a ScanUploadedFileJob to process it.

                    'content_moderation_status' => null,
                    // ↑ Phase 1: we skip content moderation.
                    // TODO: Phase 2 — set to ContentModerationStatus::PENDING
                    // and dispatch a ModerateMediaContentJob.

                    'is_encrypted'  => false,
                    // ↑ Phase 1: no encryption for asset photos.
                    // KYC documents WILL be encrypted in Phase 2.
                    // TODO: Phase 2 — encrypt sensitive media via KMS.

                    'uploaded_by_id' => $owner->id,
                ]);

                $createMedia->push($media);
            }
            return $createMedia;
        });
    }
    
    private function storeFile(UploadedFile $file, Asset $asset) : string {
        $fileName = Str::ulid() . '.' . $file->getClientOriginalExtension();

        $directory = "assets/{$asset->id}";
        // ↑ Store all photos for one asset in one folder.
        // "assets/01ARZ3NDEKTSV4RRFFQ69G5FAV/"
        // Why per-asset folders? When you delete an asset, you can delete the entire folder.
        // Also makes S3 migration easier — one folder per asset.

        return Storage::disk('local')->putFileAs($directory, $file, $fileName);
    }
    private function demoteExistingPrimary(Asset $asset, int $primaryIndex, int $uploadCount):void{
        if($primaryIndex < $uploadCount){
            $asset->media()
                  ->where('purpose', MediaPurpose::ASSET_PHOTO)
                  ->where('is_primary', true)
                  ->update(['is_primary' => false]);
        }
    }
}