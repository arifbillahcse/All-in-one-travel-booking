<?php

namespace App\Listeners;

use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

/** Remembers each upload's pixel size so pages can reserve space and avoid layout shift. */
class StoreImageDimensions
{
    public function handle(MediaHasBeenAddedEvent $event): void
    {
        $media = $event->media;

        if (! str_starts_with((string) $media->mime_type, 'image/')) {
            return;
        }

        $size = @getimagesize($media->getPath());

        if ($size) {
            $media->setCustomProperty('width', $size[0])->setCustomProperty('height', $size[1])->save();
        }
    }
}
