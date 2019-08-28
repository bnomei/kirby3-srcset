<?php

declare(strict_types=1);

namespace Bnomei;

use Kirby\Cms\KirbyTag;
use Kirby\Toolkit\A;

final class Srcset
{
    /*
     * @var array
     */
    private $options;

    /*
     * @var string
     */
    private $text;

    /*
     * @var misc
     */
    private $parent;

    /*
     * @var string
     */
    public const PLACEHOLDER = 'https://srcset.net/placeholder.jpg';

    /**
     * SrcsetTag constructor.
     * @param null $data
     */
    public function __construct($data = null, array $options = [])
    {
        if (is_a($data, 'Kirby\Cms\KirbyTag')) {
            $this->parent = $data->parent();
            $options = $this->dataFromTag($data);
        } elseif (is_a($data, 'Kirby\Cms\File') || is_a($data, 'Kirby\Cms\FileVersion')) {
            $this->parent = $data->parent();
            $options['value'] = $data->filename();
        } elseif (is_array($data)) {
            $this->parent = A::get($data, 'parent');
            $options = $data;
        }

        $defaults = [
            'lazy' => option('bnomei.srcset.lazy'),
            'prefix' => option('bnomei.srcset.prefix'),
            'autosizes' => option('bnomei.srcset.autosizes'),
            'figure' => option('bnomei.srcset.figure') === true,
            'ratio' => option('bnomei.srcset.ratio'),
            'quality' => intval(option('thumbs.quality', 80)),
        ];
        $this->options = $this->normalizeData(array_merge($defaults, $options));

        if ($this->parent) {
            $this->options['file'] = $this->parent->file((string)A::get($this->options, 'value'));
        }
        $this->text = $this->imageKirbytagFromData($this->options);
        $this->text = $this->applySrcset($this->text, $this->options);
        $this->text = $this->applyRatio($this->text, $this->options);
    }

    /**
     * @param null $key
     * @return array|mixed
     */
    public function option($key = null)
    {
        if ($key) {
            return A::get($this->options, $key);
        }

        return $this->options;
    }

    public static function kirbytagAttrs()
    {
        return ['sizes', 'lazy', 'prefix', 'autosizes', 'quality', 'figure', 'ratio'];
    }

    /**
     * @return array
     */
    public function attrKeys(): array
    {
        return array_merge(
            ['value'],
            KirbyTag::$types['image']['attr'],
            self::kirbytagAttrs()
        );
    }

    /**
     * @param array $data
     * @return array
     */
    public function normalizeData(array $data)
    {
        $norm = [];
        foreach ($data as $key => $val) {
            if (is_string($val) && $val === 'true') {
                $val = true;
            }
            if (is_string($val) && $val === 'false') {
                $val = false;
            }
            if (is_string($val) && $val === 'null') {
                $val = null;
            }
            if (is_null($val)) {
                continue;
            }
            $norm[$key] = $val;
        }
        return $norm;
    }

    /**
     * @param KirbyTag $tag
     * @return array
     */
    public function dataFromTag(KirbyTag $tag)
    {
        $data = [];
        foreach ($this->attrKeys() as $attr) {
            $val = $tag->$attr;
            if (is_null($val)) {
                continue;
            }
            $data[$attr] = $tag->$attr;
        }
        return $data;
    }

    /**
     * @param array $data
     * @return string
     */
    public function imageKirbytagFromData(array $data = []): string
    {
        $attrs = ['(image: ' . self::PLACEHOLDER]; // A::get($data, 'value')
        $lazy = A::get($data, 'lazy');
        if ($lazy) {
            $data['imgclass'] = trim(A::get($data, 'imgclass', '') . ' ' . $lazy);
        }
        if (A::get($data, 'ratio') && !A::get($data, 'caption')) {
            $data['class'] = trim(A::get($data, 'class', '') . ' ' . A::get($data, 'ratio'));
        }
        foreach (KirbyTag::$types['image']['attr'] as $attr) {
            $val = A::get($data, $attr);
            if (!$val || strlen(strval($val)) === 0) {
                continue;
            }
            $attrs[] = $attr . ': ' . $val;
        }
        $attrs[] = ')';
        $text = implode(' ', $attrs);

        $text = kirby()->kirbytags($text, $data);
        if (!A::get($data, 'figure')) {
            // <figure> </figure> and <figure class="" ...> </figure>
            $text = preg_replace('/<(\/?)(figure[^>]*)>/',  '', $text);
        }
        return $text;
    }

    /**
     * @param string $text
     * @param array $data
     * @return string
     */
    public function applySrcset(string $text, array $data = []): string
    {
        $srcfile = new SrcsetFile(
            A::get($data, 'file'),
            A::get($data, 'sizes'),
            A::get($data, 'width'),
            A::get($data, 'heigth'),
            A::get($data, 'quality')
        );

        $srcset = [
            A::get($data, 'prefix') . 'src' => $srcfile->src(),
            A::get($data, 'prefix') . 'srcset' => $srcfile->srcset(),
        ];
        if (A::get($data, 'debug')) {
            $srcset['data-thumb-srcset'] = $srcfile->sizes();
        }
        $autosizes = A::get($data, 'autosizes');
        if ($autosizes === true) {
            $autosizes = 'auto';
        }
        if ($autosizes) {
            $srcset['data-sizes'] = $autosizes;
        }

        $attrs = [];
        foreach ($srcset as $key => $value) {
            $attrs[] = '' . $key . '="' . $value . '"';
        }

        $text = str_replace(
            'src="' . self::PLACEHOLDER . '"',
            implode(' ', $attrs),
            $text
        );

        return $text;
    }

    /**
     * @param string $text
     * @param array $data
     * @return string
     */
    public function applyRatio(string $text, array $data = []): string
    {
        if (!A::get($data, 'figure') || !A::get($data, 'ratio')) {
            return $text;
        }

        $srcfile = new SrcsetFile(
            A::get($data, 'file'),
            A::get($data, 'sizes'),
            A::get($data, 'width'),
            A::get($data, 'heigth'),
            A::get($data, 'quality')
        );
        $ratio = $srcfile->ratio();

        $text = '<style>.'.A::get($data, 'ratio').'[data-ratio="'.$ratio.'"]{padding-bottom:'.$ratio.'%;}</style>' . $text;

        if (A::get($data, 'caption')) {
            $text = str_replace(
                ['><img', '><figcaption>'],
                ['><div class="'.A::get($data, 'ratio').'" data-ratio="'.$ratio.'"><img', '></div><figcaption>'],
                $text
            );
        } else {
            $text = str_replace(
                '<figure',
                '<figure data-ratio="'.$ratio.'"',
                $text
            );
        }

        return $text;
    }

    /**
     * @return string
     */
    public function html(): string
    {
        return trim(strval($this->text));
    }
}
