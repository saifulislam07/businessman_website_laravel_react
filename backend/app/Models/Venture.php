<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use App\Models\Concerns\HasWebpConversions;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;

class Venture extends Model implements HasMedia
{
    use Orderable, HasWebpConversions;

    protected $fillable = ['name', 'slug', 'industry', 'country', 'description', 'website_url', 'founded_year', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'founded_year' => 'integer',
        ];
    }

    public const IMAGE_COLLECTION = 'logo';

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::IMAGE_COLLECTION)->singleFile();
    }
}
