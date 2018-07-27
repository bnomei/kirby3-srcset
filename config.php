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
    ]
]);
