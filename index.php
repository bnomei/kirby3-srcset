<?php

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('bnomei/srcset', [
    'options' => [
        'lazy' => false, // bool or class-name
        'lazy.prefix' => 'data-', // bool or string
        'autosizes' => false, // https://github.com/aFarkas/lazysizes#markup-api
        'presets' => [
            'default' => [320, 0],
            'breakpoints' => [576, 768, 992, 1200],
        ],
        'snippet' => 'plugin-srcset-picture',
        'types' => [],
        'resize' => function ($file, $width, $type) {
            // NOTE: override and do something with $type
            return $file->resize($width);
        }
    ],
    'snippets' => [
        'plugin-srcset-picture' => __DIR__ . '/snippets/srcset-picture.php',
        'plugin-srcset-img' => __DIR__ . '/snippets/srcset-img.php',
        'plugin-srcset-image' => __DIR__ . '/snippets/srcset-img.php',
    ],
    'fileMethods' => [
        'srcset' => function ($preset = 'default', $lazy = null, $prefix = null) {
            return \Bnomei\Srcset::srcset($this, $preset, $lazy, $prefix);
        }
    ],
    'tags' => [
        'srcset' => [
            'attr' => ['preset', 'lazy', 'link', 'class', 'prefix'],
            'html' => function ($tag) {
                try {
                    $file = Kirby::instance()->file($tag->value, $tag->parent());
                    $preset = (string) $tag->preset;
                    $prefix = (string) $tag->prefix;
                    if (\Kirby\Toolkit\Str::contains($preset, ' ') || \Kirby\Toolkit\Str::contains($preset, ',')) {
                        $preset = str_replace(['[',']',',','  ','px'], ['','',' ',' ',''], $preset);
                        $preset = array_map(function ($v) {
                            return trim($v);
                        }, explode(' ', $preset));
                    }
                    if ($file) {
                        $srcset = \Bnomei\Srcset::srcset($file, $preset, boolval($tag->lazy), $prefix);
                        if ($tag->link && $tag->class) {
                            return '<a href="'.url($tag->link).'" class="'.$tag->class.'">'.$srcset.'</a>';
                        } elseif ($tag->link) {
                            return '<a href="'.url($tag->link).'">'.$srcset.'</a>';
                        } else {
                            return $srcset.PHP_EOL;
                        }
                    }
                    return '';
                } catch (Exception $ex) {
                    return $ex->getMessage();
                }
            }
        ]
    ]
]);
