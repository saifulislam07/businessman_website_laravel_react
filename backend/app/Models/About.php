<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use App\Models\Concerns\HasWebpConversions;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;

class About extends Model implements HasMedia
{
    use Orderable, HasWebpConversions;

    protected $fillable = ['bio', 'vision', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public const IMAGE_COLLECTION = 'photo';

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::IMAGE_COLLECTION)->singleFile();
    }
}
