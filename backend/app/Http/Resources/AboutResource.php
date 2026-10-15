<?php

namespace App\Http\Resources;

use App\Support\MediaUrls;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\About */
class AboutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bio' => RichText::html($this->bio),
            'vision' => $this->vision,
            'photo' => MediaUrls::image($this->resource, $this->resource::IMAGE_COLLECTION),
        ];
    }
}
