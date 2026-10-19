<?php

namespace App\Http\Resources;

use App\Support\MediaUrls;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Expertise */
class ExpertiseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => RichText::html($this->description),
            'icon' => $this->icon,
            'image' => MediaUrls::image($this->resource, $this->resource::IMAGE_COLLECTION),
        ];
    }
}
