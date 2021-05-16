<?php

namespace Kirby\Editor;

use Throwable;

$foundEditor = false;
foreach ([realpath(__DIR__ . '/../../../../'), realpath(__DIR__ . '/../../../tests/site/plugins/')] as $dir) {
    if (is_dir($dir . '/editor')) {
        foreach (['Block', 'Blocks'] as $lib) {
            if (! class_exists("Kirby\Editor\{$lib}")) {
                load([
                    "Kirby\\Editor\\{$lib}" => $dir . "/editor/lib/{$lib}.php",
                ]);
            }
            $foundEditor = true;
            break;
        }
    }
}

if (! $foundEditor) {
    class Block
    {
    }
}

// TODO: no coverage since no real tests
// @codeCoverageIgnore
final class SrcsetBlock extends Block
{
    public function controller(): array
    {
        $data = parent::controller();
        $data['image'] = $image = $this->image();
        $data['src'] = $image ? $image->url() : $this->attrs()->src();
        // TODO: does this need to load custom attr?
        return $data;
    }

    public function image()
    {
        try {
            return \image($this->attrs()->id()->value());
            //return $this->kirby()->api()->parent($this->attrs()->guid()->value());
        } catch (Throwable $e) {
            return null;
        }
    }

    public function ratio(): ?float
    {
        if ($image = $this->image()) {
            return $image->ratio();
        }
        return null;
    }

    public function isEmpty(): bool
    {
        return empty($this->image()) === true && $this->attrs()->src()->isEmpty();
    }

    public function markdown(): string
    {
        $image = $this->image();
        $attrs = [
            'lazysrcset' => $image ? $image->id() : $this->attrs()->src(),

            'alt' => $this->attrs()->alt(),
            'caption' => $this->attrs()->caption(),
            'class' => $this->attrs()->css(), // class is PHP keyword
            'height' => $this->attrs()->height(),
            'imgclass' => $this->attrs()->imgclass(),
            'link' => $this->attrs()->link(),
            'linkclass' => $this->attrs()->linkclass(),
            'rel' => $this->attrs()->rel(),
            'target' => $this->attrs()->target(),
//            'text' => $this->attrs()->text(),
            'title' => $this->attrs()->title(),
            'width' => $this->attrs()->width(),
            'sizes' => $this->attrs()->sizes(),
            'lazy' => $this->attrs()->lazy(),
            'prefix' => $this->attrs()->prefix(),
            'autosizes' => $this->attrs()->autosizes(),
            'quality' => $this->attrs()->quality(),
            'figure' => $this->attrs()->figure(),
        ];
        return \kirbyTagMaker($attrs) . PHP_EOL . PHP_EOL;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        if ($image = $this->image()) {
            $data['attrs'] = array_merge($data['attrs'] ?? [], [
                'guid' => $image->panelUrl(true),
                'id' => $image->id(),
                'ratio' => $image->ratio(),
                'src' => $image->url(),
            ]);
        } else {
            unset($data['attrs']['guid']);
        }
        return $data;
    }
}
