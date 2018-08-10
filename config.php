<?php

Kirby::plugin('bnomei/srcset', [
    'options' => [
        'lazy' => false, // bool or class-name
        'presets' => [
            'default' => [320],
            'breakpoints' => [576, 768, 992, 1200],
        ],
        'types' => [],
        'resize' => function ($file, $width, $type) {
            // NOTE: override and do something with $type
            return $file->resize($width);
        }
    ],
    'snippets' => [
        'plugin-srcset' => __DIR__ . '/snippets/srcset.php',
    ],
    'fileMethods' => [
        'srcset' => function ($preset = 'default', $lazy = null) {
            return \Bnomei\Srcset::srcset($this, $preset, $lazy);
        }
    ],
    'tags' => [
        'srcset' => [
            'attr' => ['preset', 'lazy'],
            'html' => function ($tag) {
                try {
                    $file = Kirby::instance()->file($tag->value, $tag->parent());
                    $preset = (string) $tag->preset;
                    if(\Kirby\Toolkit\Str::contains($preset, ' ') || \Kirby\Toolkit\Str::contains($preset, ',')) {
                        $preset = str_replace(['[',']',',','  ','px'], ['','',' ',' ',''], $preset);
                        $preset = array_map(function ($v) {
                                return trim($v);
                            }, explode(' ', $preset));
                    }
                    if ($file) {
                       return \Bnomei\Srcset::srcset($file, $preset, boolval($tag->lazy)); 
                    }
                    return '';
                } catch(Exception $ex) {
                    return $ex->getMessage();
                }
            }
        ]
    ]
]);
