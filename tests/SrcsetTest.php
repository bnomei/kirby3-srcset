<?php

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
            '/^<figure>.*<\/figure>$/',
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
        ]);
        $text = $srcset->html();

        $textFromTag = kirby()->kirbytags('(lazysrcset: test2000.png sizes: default imgclass: c-image)', $this->data);
        $this->assertEquals($text, $textFromTag);

        $srcset = new Srcset([
            'value' => 'test2000.png',
            'parent' => page('home'),
            'sizes' => 'default',
            'lazy' => 'false',
            'prefix' => '',
            'autosizes' => 'null',
            'figure' => 'true',
        ]);
        $text = $srcset->html();
        $textFromTag = kirby()->kirbytags('(lazysrcset: test2000.png sizes: default figure: true lazy: false autosizes: prefix: )', $this->data);
        $this->assertEquals($text, $textFromTag);
    }
}
