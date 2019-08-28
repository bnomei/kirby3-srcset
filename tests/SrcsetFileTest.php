<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Bnomei\SrcsetFile;
use Kirby\Cms\File;
use Kirby\Image\Image;
use PHPUnit\Framework\TestCase;

final class SrcsetFileTest extends TestCase
{
    /*
     * @var Kirby\Cms\File
     */
    private $file;

    public function setUp(): void
    {
        $this->file = page('home')->image('test2000.png');
    }

    public function testConstucts()
    {
        $this->assertInstanceOf(File::class, $this->file);

        $srcfile = new SrcsetFile($this->file);
        $this->assertInstanceOf(SrcsetFile::class, $srcfile);
    }

    public function testSizes() {
        $srcfile = new SrcsetFile($this->file);
        $this->assertEquals('', $srcfile->sizes());

        // this is a not a list
        $srcfile = new SrcsetFile($this->file, '100');
        $this->assertEquals('100', $srcfile->sizes());

        $srcfile = new SrcsetFile($this->file, '320 640 960');
        $this->assertEquals('{"0":320,"1":640,"2":960}', $srcfile->sizes());

        $srcfile = new SrcsetFile($this->file, '[320, 640, 960]');
        $this->assertEquals('{"0":320,"1":640,"2":960}', $srcfile->sizes());

        $srcfile = new SrcsetFile($this->file, '320px, 640px, 960px');
        $this->assertEquals('{"0":320,"1":640,"2":960}', $srcfile->sizes());

        $srcfile = new SrcsetFile($this->file, 'default');
        $this->assertEquals('default', $srcfile->sizes());

        $srcfile = new SrcsetFile($this->file, function() { return 'default'; });
        $this->assertEquals('default', $srcfile->sizes());

        $srcfile = new SrcsetFile($this->file, [320, 1200]);
        $this->assertEquals('{"0":320,"1":1200}', $srcfile->sizes());

        $srcfile = new SrcsetFile($this->file, [
            800  => '1x',
            1600 => '1.5x'
        ]);
        $this->assertEquals('{"800":"1x","1600":"1.5x"}', $srcfile->sizes());
    }

    public function testSrc()
    {
        $srcfile = new SrcsetFile($this->file);
        $this->assertRegExp('/^\/media\/pages\/home\/.*\/test2000.png$/', $srcfile->src());

        $srcfile = new SrcsetFile($this->file, null, 500);
        $this->assertRegExp('/^\/media\/pages\/home\/.*\/test2000-500x.png$/', $srcfile->src());

        $srcfile = new SrcsetFile($this->file, null, 500, 100);
        $this->assertRegExp('/^\/media\/pages\/home\/.*\/test2000-500x100.png$/', $srcfile->src());

        $srcfile = new SrcsetFile($this->file, null, 500, 100, 20);
        $this->assertRegExp('/^\/media\/pages\/home\/.*\/test2000-500x100-q20.png$/', $srcfile->src());
    }

    public function testSrcset()
    {
        $srcfile = new SrcsetFile($this->file, [320, 1200]);
        $this->assertRegExp('/^.*\/(test2000-320x.png 320w, ).*(test2000-1200x.png 1200w)$/', $srcfile->srcset());

        $srcfile = new SrcsetFile($this->file, 'breakpoints');
        $this->assertRegExp('/^.*(576w).*(768w).*(992w).*(1200w)$/', $srcfile->srcset());
    }

    public function testRatio()
    {
        $srcfile = new SrcsetFile($this->file, [320, 1200]);
        $this->assertEquals(
            strval(round($srcfile->image()->dimensions()->ratio()*100, 2)),
            $srcfile->ratio()
        );
    }

    public function testImage()
    {
        $srcfile = new SrcsetFile($this->file, [320, 1200]);
        $this->assertInstanceOf(Image::class, $srcfile->image());
    }
}
