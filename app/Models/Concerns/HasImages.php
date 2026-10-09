<?php

namespace App\Models\Concerns;

use App\Support\Image;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Uploaded photos for a model, served as WebP in several widths.
 *
 * Each model lists its collections and widths in IMAGE_WIDTHS; a conversion called
 * "<collection>-<width>" is generated for every width. When nothing was uploaded the
 * caller passes a fallback URL (the placeholder photos from the seeders).
 */
trait HasImages
{
    use InteractsWithMedia;

    /** @return array<string, list<int>> collection => widths */
    abstract protected function imageWidths(): array;

    public function registerMediaConversions(?Media $media = null): void
    {
        foreach ($this->imageWidths() as $collection => $widths) {
            foreach ($widths as $width) {
                $this->addMediaConversion("{$collection}-{$width}")
                    ->performOnCollections($collection)
                    ->width($width)
                    ->format('webp')
                    ->quality(82)
                    ->withResponsiveImages(false)
                    ->nonOptimized();
            }
        }
    }

    /** The photo in a collection (the $position-th for multi-photo collections). */
    public function image(string $collection, ?string $fallback = null, int $position = 0): ?Image
    {
        $media = $this->getMedia($collection)->values()->get($position);

        if (! $media) {
            return $fallback ? new Image($fallback) : null;
        }

        $widths = $this->imageWidths()[$collection] ?? [];
        $available = collect($widths)
            ->filter(fn (int $w) => $media->hasGeneratedConversion("{$collection}-{$w}"))
            ->sort()->values();

        if ($available->isEmpty()) {
            return new Image($media->getUrl(), null, $media->getCustomProperty('width'), $media->getCustomProperty('height'));
        }

        $largest = $available->last();
        $ratio = ($media->getCustomProperty('width') && $media->getCustomProperty('height'))
            ? $media->getCustomProperty('height') / $media->getCustomProperty('width') : null;

        return new Image(
            src: $media->getUrl("{$collection}-{$largest}"),
            srcset: $available->map(fn (int $w) => $media->getUrl("{$collection}-{$w}")." {$w}w")->implode(', '),
            width: $largest,
            height: $ratio ? (int) round($largest * $ratio) : null,
        );
    }
}
