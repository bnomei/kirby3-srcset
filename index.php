<?php

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('bnomei/srcset', [
    'options' => [
        'lazy' => 'lazyload',
        'prefix' => 'data-',
        'autosizes' => 'auto',
        'figure' => true,
        'ratio' => 'lazysrcset-ratio',
    ],
    'snippets' => [
        'editor/srcset' => __DIR__ . '/snippets/srcset.php',
    ],
    'fileMethods' => [
        'lazysrcset' => function ($options = null) {
            if ($this === null) {
                return \Kirby\Cms\Html::img('', ['alt' => 'lazysrcset can not create srcset from null']);
            }
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
                \Bnomei\Srcset::kirbytagAttrs()
            ),
            'html' => function ($tag) {
                if ($tag === null) {
                    return \Kirby\Cms\Html::img('', ['alt' => 'lazysrcset can not create srcset from null']);
                }
                $srcsetTag = new \Bnomei\Srcset($tag);
                return $srcsetTag->html();
            },
        ],
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'bnomei/srcset/options',
                'action'  => function () {
                    return \Bnomei\Srcset::defaultOptions();
                }
            ],
        ],
    ],
    'translations' => [
        'de'    => require __DIR__ . '/i18n/de.php',
        'en'    => require __DIR__ . '/i18n/en.php',
        'es'    => require __DIR__ . '/i18n/es.php',
        'fr'    => require __DIR__ . '/i18n/fr.php',
        'it'    => require __DIR__ . '/i18n/it.php',
        'lt'    => require __DIR__ . '/i18n/lt.php',
        'nl'    => require __DIR__ . '/i18n/nl.php',
        'pt_BR' => require __DIR__ . '/i18n/pt_BR.php',
        'pt_PT' => require __DIR__ . '/i18n/pt_PT.php',
        'ru'    => require __DIR__ . '/i18n/ru.php',
        'sv_SE' => require __DIR__ . '/i18n/sv_SE.php',
        'tr'    => require __DIR__ . '/i18n/tr.php',
    ],
]);
