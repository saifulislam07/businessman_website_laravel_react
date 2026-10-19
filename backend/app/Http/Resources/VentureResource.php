<?php

namespace App\Http\Resources;

use App\Support\MediaUrls;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Venture */
class VentureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'industry' => $this->industry,
            'country' => $this->country,
            'description' => RichText::html($this->description),
            'website_url' => $this->website_url,
            'founded_year' => $this->founded_year,
            'logo' => MediaUrls::image($this->resource, $this->resource::IMAGE_COLLECTION),
            'cover' => MediaUrls::image($this->resource, $this->resource::COVER_COLLECTION),
        ];
    }
}
