<?php
declare(strict_types = 1);

namespace App\Http\Requests\Assets;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest{
    public function authorize(): bool{
        $asset = $this->route('asset');

        return $asset instanceof Asset && $this->user()->id === $asset->owner_id;

        // TODO: Phase 2 — also check asset status is not DELISTED or ARCHIVED
        // A delisted asset should not accept new photos.
    }

    public function rules(){
        return [
            'photos'   => ['required', 'array', 'min:1', 'max:10'],
            // ↑ 'photos' is the field name the frontend sends.

            'photos.*' => ['required', 'file', 'image', 'mimes:png,jpg,webp,jpeg', 'max:10240'],
            'primary_index' => ['nullable', 'integer', 'min:0', 'max:9'],
        ];
    }

    public function messages(): array{
        return[
            'photos.required'      => 'Please select at least one photo to upload.',
            'photos.max'           => 'You can upload a maximum of 10 photos at once.',
            'photos.*.image'       => 'Each file must be an image.',
            'photos.*.mimes'       => 'Photos must be JPEG, PNG, or WebP format.',
            'photos.*.max'         => 'Each photo must be under 10MB.',
            'primary_index.max'    => 'Primary index cannot exceed the number of photos.',
        ];
    }
}