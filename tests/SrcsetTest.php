<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Bnomei\Srcset;
use Kirby\Cms\Dir;
use Kirby\Cms\File;
use PHPUnit\Framework\TestCase;

final class SrcsetTest extends TestCase
{
    private $file;
    private $data;
    public function setUp(): void
    {
        $this->file = page('home')->file('test2000.png');
        $this->data = ['parent' => page('home')];
        Dir::remove(kirby()->roots()->media());
    }

    public function testConstucts()
    {
        $this->assertInstanceOf(File::class, $this->file);

        $srcset = new Srcset($this->file);
        $this->assertInstanceOf(Srcset::class, $srcset);
    }

    public function testOption()
    {
        $srcset = new Srcset([
            'value' => 'test2000.png',
            'parent' => page('home'),
        ]);

        $this->assertIsArray($srcset->option());
        $this->assertEquals('test2000.png', $srcset->option('value'));
    }

    public function testImageFromData()
    {
        $srcset = new Srcset([
            'value' => 'test2000.png',
            'parent' => page('home'),
        ]);

        $this->assertRegExp(
            '/(test2000-q42\.png).*(test2000-320x\.png 320w).*(test2000-1200x.png 1200w)/',
            $srcset->html()
        );

        $srcset = new Srcset([
            'value' => 'test2000.png',
            'parent' => page('home'),
            'prefix' => false,
            'debug' => true,
        ]);

        $this->assertNotRegExp(
            '/data-src/',
            $srcset->html()
        );

        $srcset = new Srcset([
            'value' => 'test2000.png',
            'parent' => page('home'),
            'figure' => false,
        ]);

        $this->assertNotRegExp(
            '/^<figure.*<\/figure>$/',
            $srcset->html()
        );

        $srcset = new Srcset([
            'value' => 'test2000.png',
            'parent' => page('home'),
            'lazy' => 'lazyload',
            'imgclass' => 'c-image',
            'prefix' => 'data-flickity-lazyload-',
            'autosizes' => true,
        ]);

        $this->assertRegExp(
            '/(class="c-image lazyload").*(data-flickity-lazyload-src).*(test2000-q42\.png).*(data-flickity-lazyload-srcset).*(test2000-320x\.png 320w).*(test2000-1200x.png 1200w).*(data-sizes="auto")/',
            $srcset->html()
        );
    }

    public function testSrcsetTag()
    {
        $srcset = new Srcset([
            'value' => 'test2000.png',
            'parent' => page('home'),
            'imgclass' => 'c-image',
            'lazy' => 'lazyload',
            'prefix' => 'data-',
            'autosizes' => 'auto',
            'figure' => true,
            'ratio' => 'custom-class',
        ]);
        $text = $srcset->html();

        $textFromTag = kirby()->kirbytags('(lazysrcset: test2000.png sizes: default imgclass: c-image ratio: custom-class)', $this->data);
        $this->assertEquals($text, $textFromTag);

        $srcset = new Srcset([
            'value' => 'test2000.png',
            'parent' => page('home'),
            'sizes' => 'default',
            'lazy' => 'false',
            'prefix' => '',
            'autosizes' => 'null',
            'figure' => 'true',
            'ratio' => false,
        ]);
        $text = $srcset->html();
        $textFromTag = kirby()->kirbytags('(lazysrcset: test2000.png sizes: default figure: true lazy: false ratio: false autosizes: prefix: )', $this->data);
        $this->assertEquals($text, $textFromTag);

        $srcset = new Srcset([
            'value' => 'test2000.png',
            'parent' => page('home'),
            'sizes' => 'default',
            'lazy' => 'false',
            'prefix' => '',
            'autosizes' => 'null',
            'figure' => 'true',
            'ratio' => 'lazysrcset-ratio',
            'caption' => 'some caption'
        ]);
        $text = $srcset->html();
        $textFromTag = kirby()->kirbytags('(lazysrcset: test2000.png sizes: default figure: true lazy: false ratio: lazysrcset-ratio autosizes: prefix: caption: some caption)', $this->data);
        $this->assertEquals($text, $textFromTag);
    }

    public function testFigure()
    {
        $srcset = new Srcset([
            'value' => 'test2000.png',
            'parent' => page('home'),
            'lazy' => 'lazyload',
            'class' => 'c-figure',
            'autosizes' => true,
            'figure' => true,
            'ratio' => 'my-ratio-class',
        ]);

        $this->assertStringStartsWith(
            '<style>.my-ratio-class[data-ratio="100"]{padding-bottom:100%;}</style><figure data-ratio="100" class="c-figure my-ratio-class">',
            $srcset->html()
        );
    }
}
