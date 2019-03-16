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
        'pictureElementWithSrcset' => function ($preset = 'default', $lazy = null, $prefix = null, $class = null, $imgclass = null, $snippet = 'plugin-srcset-picture') {
            return \Bnomei\Srcset::srcset($this, $preset, $lazy, $prefix. $class, $imgclass, $snippet);
        },
        'picSrcset' => function ($preset = 'default', $lazy = null, $prefix = null, $class = null, $imgclass = null, $snippet = 'plugin-srcset-picture') {
            return \Bnomei\Srcset::srcset($this, $preset, $lazy, $prefix. $class, $imgclass, $snippet);
        },
        'imgSrcset' => function ($preset = 'default', $lazy = null, $prefix = null, $class = null, $imgclass = null, $snippet = 'plugin-srcset-img') {
            return \Bnomei\Srcset::srcset($this, $preset, $lazy, $prefix, $class, $imgclass, $snippet);
        },
        'imgElementWithSrcset' => function ($preset = 'default', $lazy = null, $prefix = null, $class = null, $imgclass = null, $snippet = 'plugin-srcset-img') {
            return \Bnomei\Srcset::srcset($this, $preset, $lazy, $prefix, $class, $imgclass, $snippet);
        },
        // 'srcset' => function() { return null; } // #10, Removed since Kirby 3.1.0 added this File-Method with different results
    ],
    'tags' => [
        'srcset' => [
            // https://getkirby.com/docs/reference/text/kirbytags/image
            'attr' => ['preset', 'lazy', 'prefix', 'class', 'imgclass', 'link', 'linkclass', 'target', 'rel', 'snippet'],
            'html' => function ($tag) {
                try {
                    $file = Kirby::instance()->file($tag->value, $tag->parent());
                    if ($file) {
                        $preset = (string) $tag->preset;
                        if (\Kirby\Toolkit\Str::contains($preset, ' ') || \Kirby\Toolkit\Str::contains($preset, ',')) {
                            $preset = str_replace(['[',']',',','  ','px'], ['','',' ',' ',''], $preset);
                            $preset = array_map(function ($v) {
                                return trim($v);
                            }, explode(' ', $preset));
                        }
                        $prefix = (string) $tag->prefix;
                        $class = $tag->class ? trim($tag->class) : null;
                        $imgclass = $tag->imgclass ? trim($tag->imgclass) : null;
                        $snippet = $tag->snippet ? trim($tag->snippet) : 'plugin-srcset-img';
                        $srcset = \Bnomei\Srcset::srcset($file, $preset, boolval($tag->lazy), $prefix, $class, $imgclass, $snippet);
                        if ($tag->link) {
                            $attr = [
                                'href' => trim($tag->link),
                            ];
                            if ($tag->linkclass) {
                                $attr['class'] = trim($tag->linkclass);
                            }
                            if ($tag->target) {
                                $attr['target'] = trim($tag->target);
                            }
                            if ($tag->rel) {
                                $attr['rel'] = trim($tag->rel);
                            }
                            // wrap $srcset in array to avoid encoding
                            // https://github.com/getkirby/kirby/blob/master/src/Toolkit/Html.php#L367
                            return \Kirby\Toolkit\Html::tag('a', [$srcset], $attr);
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
