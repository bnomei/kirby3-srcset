<?php

declare(strict_types=1);

namespace Bnomei;

use Kirby\Cms\File;
use Kirby\Image\Image;
use Kirby\Toolkit\Str;

final class SrcsetFile
{
    /*
     * @var File
     */
    private $file;

    /*
     * @var image
     */
    private $image;

    /*
     * @var array|string|null
     */
    private $sizes;

    /*
     * @var int
     */
    private $width;

    /*
     * @var int
     */
    private $height;

    /*
     * @var int
     */
    private $quality;

    /**
     * SrcsetFile constructor.
     * @param File $file
     * @param null $sizes
     * @param int|null $width
     * @param int|null $height
     * @param int|null $quality
     */
    public function __construct(File $file, $sizes = null, ?int $width = null, ?int $height = null, ?int $quality = null)
    {
        $this->file = $file;
        $this->image = new Image($file->root(), $file->url());

        if (is_callable($sizes)) {
            $sizes = $sizes();
        }
        if (is_string($sizes) && (Str::contains($sizes, ' ') || Str::contains($sizes, ','))) {
            $sizes = str_replace(['[', ']', ',', '  ', 'px'], ['', '', ' ', ' ', ''], $sizes);
            $sizes = array_map(function ($v) {
                return intval(trim($v));
            }, explode(' ', $sizes));
        }
        $this->sizes = $sizes;

        $this->width = $width;
        $this->height = $height;
        $this->quality = $quality;
    }

    /**
     * @return string|null
     */
    public function src(): ?string
    {
        // NOTE: call to trigger thumb component
        return $this->file->resize($this->width, $this->height, $this->quality)->url();
    }

    /**
     * @return string|null
     */
    public function srcset(): ?string
    {
        return $this->file->srcset($this->sizes);
    }

    /**
     * @return string
     */
    public function sizes(): string
    {
        if (is_array($this->sizes)) {
            return json_encode($this->sizes, JSON_FORCE_OBJECT);
        }
        return strval($this->sizes);
    }

    /**
     * @return string|null
     */
    public function ratio(): string
    {
        return strval(round(100.0 / $this->image->dimensions()->ratio(), 2));
    }

    /**
     * @return Image
     */
    public function image(): Image
    {
        return $this->image;
    }
}
