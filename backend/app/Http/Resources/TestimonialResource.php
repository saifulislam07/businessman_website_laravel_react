<?php

namespace App\Http\Resources;

use App\Support\MediaUrls;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Testimonial */
class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'designation' => $this->designation,
            'company' => $this->company,
            'quote' => $this->quote,
            'photo' => MediaUrls::image($this->resource, $this->resource::IMAGE_COLLECTION),
        ];
    }
}
