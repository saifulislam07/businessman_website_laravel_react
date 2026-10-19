<?php

namespace App\Http\Resources;

use App\Support\MediaUrls;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Milestone */
class MilestoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'title' => $this->title,
            'description' => RichText::html($this->description),
            'image' => MediaUrls::image($this->resource, $this->resource::IMAGE_COLLECTION),
        ];
    }
}
