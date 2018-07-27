<?php

namespace Bnomei;

class Srcset
{
    public static function srcset($file, $preset = 'default', $lazy = null) {
        $lazy = !is_null($lazy) ? $lazy : option('bnomei.srcset.lazy', false);
        $isLazy = $lazy !== null && $lazy !== false;
        return snippet('plugin-srcset', [
            'file' => $file,
            'lazy' => (is_string($lazy) ? $lazy : ($lazy?'lazy':'')),
            'isLazy' => $isLazy,
            'preset' => $preset,
            'img' => \Bnomei\Srcset::img($file, $preset, $lazy),
            'sources' => \Bnomei\Srcset::sources($file, $preset, $lazy)
        ], true);
    }

    public static function resizeWithType($file, $width, $type) {
        if(!$file || !is_a($file, 'Kirby\Cms\File')) return null;

        $resize = option('bnomei.srcset.resize', null);
        if($resize && is_callable($resize)) {
            return $resize($file, $width, $type);
        } else {
            return $file->resize($width);
        }
    }

    public static function img($file, $preset = 'default', $lazy = false)
    {
        if (!$file && !is_a($file, 'Kirby\Cms\File')) return null;
        return [
            'src' => $file->resize()->url(), // trigger thumb if needed
            'alt' => $file->caption()->isNotEmpty() ? $file->caption()->value() : $file->filename(),
        ];
    }

    public static function sources($file, $preset = 'default', $lazy = false) {
        if (!$file && !is_a($file, 'Kirby\Cms\File')) return null;

        $presets = option('bnomei.srcset.presets');
        $presetWidths = \Kirby\Toolkit\A::get($presets, $preset, []);
        $presetWidths[] = intval($file->width());
        sort($presetWidths, SORT_NUMERIC);
        $presetWidths = array_unique($presetWidths);

        $types = option('bnomei.srcset.types', []);
        if (count($types) == 0) {
            $types[] = $file->mime();
        }

        $sources = [];
        foreach($types as $t) {
            $srcset = [];
            foreach($presetWidths as $p) {
                $img = static::resizeWithType($file, intval($p), strval($t));
                if($img && is_a($img, 'Kirby\CMS\File')) {
                    $srcset[] = $img->url() . ' ' . $p . 'w';
                }
            }
            $sources[] = [
                'srcset' => implode(', ', $srcset),
                'type' => $t,
            ];
        }

        return $sources;
    }
}
