<?php

namespace App\Http\Resources;

use App\Support\MediaUrls;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Partner */
class PartnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'logo' => MediaUrls::image($this->resource, $this->resource::IMAGE_COLLECTION),
        ];
    }
}
