<?php
declare(strict_types = 1);

namespace App\Domains\Assets\Actions;

use App\Domains\Shared\Enums\Asset\StatusEnum;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class UpdateAssetAction{
    public function execute(array $data, Asset $asset, User $owner): Asset{
        $this->ensureOwnership($asset, $owner);
        $this->ensureIsEditable($asset);

        return DB::transaction(function () use($data, $asset){
            $asset->update($data);
            // TODO: Phase 2 — dispatch AssetUpdated event:
            // event(new \App\Domains\Assets\Events\AssetUpdated($asset, $data));
            // Listeners will: re-index search, notify active bookers of price change.
            
            return $asset->fresh();
        });
    }

    private function ensureOwnership(Asset $asset, User $owner): void{
        if($asset->owner_id !== $owner->id){
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'You do not own this asset.'
            );
          
        }
    }
    private function ensureIsEditable(Asset $asset): void{
        $assetStatus = [StatusEnum::DELISTED->value , StatusEnum::ARCHIVED->value] ;
        if(in_array($asset->status->values(), $assetStatus)){
            throw new \InvalidArgumentException(
                '"Assets with status {$asset->status->value} cannot be edited."'
            );
        }
    }
}