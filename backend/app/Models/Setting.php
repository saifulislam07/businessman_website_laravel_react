<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use App\Models\Concerns\HasWebpConversions;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model implements HasMedia
{
    use Orderable, HasWebpConversions;

    protected $fillable = ['key', 'value', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'value' => 'json',
        ];
    }

    public const IMAGE_COLLECTION = 'hero';

    public const OG_COLLECTION = 'og';

    public const LOGO_COLLECTION = 'logo';

    public const LOGO_LIGHT_COLLECTION = 'logo_light';

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::IMAGE_COLLECTION)->singleFile();
        $this->addMediaCollection(self::OG_COLLECTION)->singleFile();
        $this->addMediaCollection(self::LOGO_COLLECTION)->singleFile();
        $this->addMediaCollection(self::LOGO_LIGHT_COLLECTION)->singleFile();
    }
}
