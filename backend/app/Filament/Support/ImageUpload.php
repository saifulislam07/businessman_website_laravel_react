<?php

namespace App\Filament\Support;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ImageUpload
{
    public static function make(string $name, string $collection, string $label = 'Image'): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make($name)
            ->label($label)
            ->collection($collection)
            ->image()
            ->imageEditor()
            ->maxSize(5120)
            ->imagePreviewHeight('160')
            // Serve the preview from the admin's own origin so it still loads when the
            // admin is opened on a host that differs from APP_URL (localhost vs 127.0.0.1).
            ->getUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, string $file): ?array {
                $media = $component->getRecord()?->getRelationValue('media')->firstWhere('uuid', $file);

                if (! $media) {
                    return null;
                }

                return [
                    'name' => $media->name ?: $media->file_name,
                    'size' => $media->size,
                    'type' => $media->mime_type,
                    'url' => parse_url($media->getUrl(), PHP_URL_PATH),
                ];
            })
            ->helperText('JPG, PNG or WebP, up to 5 MB.')
            ->columnSpanFull();
    }
}
