<?php

namespace App\Models;

use App\Models\Concerns\HasWebpConversions;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Milestone extends Model implements HasMedia
{
    use Orderable, HasWebpConversions;

    protected $fillable = ['year', 'title', 'description', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public const IMAGE_COLLECTION = 'image';

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::IMAGE_COLLECTION)->singleFile();
    }
}
