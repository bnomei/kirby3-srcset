<?php

namespace Bnomei;

class Srcset
{
    public static function srcset(\Kirby\Cms\File $file, $preset = 'default', $lazy = null, $prefix = null, $class = null, $imgclass = null, $snippet = 'plugin-srcset-img')
    {
        if (!$file || !is_a($file, 'Kirby\Cms\File')) {
            return null;
        }

        $lazy = !is_null($lazy) ? $lazy : option('bnomei.srcset.lazy', false);
        $isLazy = $lazy !== null && $lazy !== false;
        return snippet($snippet, [
            'file' => $file,
            'lazy' => (is_string($lazy) ? $lazy : ($lazy?'lazyload':'')),
            'isLazy' => $isLazy,
            'preset' => is_array($preset) ? implode(', ', $preset) : $preset,
            'img' => \Bnomei\Srcset::img($file, $preset, $lazy),
            'sources' => \Bnomei\Srcset::sources($file, $preset, $lazy),
            'autoSizes' => option('bnomei.srcset.autosizes'),
            'prefix' => $prefix ? $prefix : option('bnomei.srcset.prefix'),
            'class' => $class ? $class : option('bnomei.srcset.class', ''),
            'imgclass' => $imgclass ? $imgclass : option('bnomei.srcset.imgclass', ''),
        ], true);
    }

    public static function resizeWithType(\Kirby\Cms\File $file, int $width, string $type)
    {
        if (!$file || !is_a($file, 'Kirby\Cms\File')) {
            return null;
        }

        $resize = option('bnomei.srcset.resize', null);
        if ($resize && is_callable($resize)) {
            return $resize($file, $width, $type);
        } else {
            return $file->resize($width);
        }
    }

    public static function presetWidthsForFile($file, $preset = 'default')
    {
        $presets = option('bnomei.srcset.presets');
        $presetWidths = is_array($preset) ? $preset : \Kirby\Toolkit\A::get($presets, $preset, []);
        if (in_array(0, $presetWidths) || count($presetWidths) == 0) {
            $presetWidths[] = intval($file->width());
        }
        sort($presetWidths, SORT_NUMERIC);
        $presetWidths = array_unique($presetWidths);
        return $presetWidths;
    }

    public static function img(\Kirby\Cms\File $file, $preset = 'default', $lazy = false)
    {
        if (!$file && !is_a($file, 'Kirby\Cms\File')) {
            return null;
        }
        $captionFieldname = option('bnomei.srcset.img.alt.fieldname', 'caption');
        $presetWidths = self::presetWidthsForFile($file, $preset);
        return [
            'src' => $file->resize($presetWidths[count($presetWidths)-1])->url(), // trigger thumb if needed
            'alt' => $file->$captionFieldname()->isNotEmpty() ? $file->$captionFieldname()->value() : $file->filename(),
        ];
    }

    public static function sources(\Kirby\Cms\File $file, $preset = 'default', $lazy = false)
    {
        if (!$file && !is_a($file, 'Kirby\Cms\File')) {
            return null;
        }

        $presetWidths = self::presetWidthsForFile($file, $preset);

        $types = option('bnomei.srcset.types', []);
        if (count($types) == 0 || option('bnomei.srcset.types.addsource', false)) {
            $types[] = $file->mime();
        }

        $sources = [];
        foreach ($types as $t) {
            $srcset = [];
            foreach ($presetWidths as $p) {
                if ($p <= 0) {
                    continue;
                }
                $img = static::resizeWithType($file, intval($p), strval($t));
                if ($img && (is_a($img, 'Kirby\CMS\FileVersion') || is_a($img, 'Kirby\CMS\File'))) {
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
