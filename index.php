<?php

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('bnomei/srcset', [
    'options' => [
        'lazy' => 'lazyload',
        'prefix' => 'data-',
        'autosizes' => 'auto',
        'figure' => true,
    ],
    'fileMethods' => [
        'lazysrcset' => function ($options = null) {
            if (is_string($options)) {
                $options = ['srcset' => $options];
            }
            $lazySrcset = new \Bnomei\Srcset($this, $options);
            return $lazySrcset->html();
        },
    ],
    'tags' => [
        'lazysrcset' => [
            'attr' => array_merge(
                Kirby\Text\KirbyTag::$types['image']['attr'],
                ['sizes', 'lazy', 'prefix', 'autosizes', 'quality', 'figure']
            ),
            'html' => function ($tag) {
                $srcsetTag = new \Bnomei\Srcset($tag);
                return $srcsetTag->html();
            },
        ],
    ],
]);
