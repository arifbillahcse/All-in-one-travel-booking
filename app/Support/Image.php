<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * An image ready for an <img> tag: the best available file, its responsive
 * variants and its size. Built from uploaded media, or from a fallback URL.
 */
final class Image
{
    public function __construct(
        public readonly string $src,
        public readonly ?string $srcset = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
    ) {}

    /**
     * Attributes for an <img>: src, srcset, sizes, width and height.
     *
     * @param  string  $sizes  the CSS "sizes" hint, e.g. "(min-width: 900px) 33vw, 100vw"
     * @param  array{0: int, 1: int}  $box  width and height to declare when the file size is unknown
     */
    public function attributes(string $sizes = '100vw', array $box = [0, 0]): HtmlString
    {
        $width = $this->width ?: ($box[0] ?: null);
        $height = $this->height ?: ($box[1] ?: null);

        $html = 'src="'.e($this->src).'"';

        if ($this->srcset) {
            $html .= ' srcset="'.e($this->srcset).'" sizes="'.e($sizes).'"';
        }

        if ($width && $height) {
            $html .= ' width="'.$width.'" height="'.$height.'"';
        }

        return new HtmlString($html);
    }
}
