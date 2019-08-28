<?php

namespace Kirby\Editor;

use Throwable;

// TODO: no coverage since no real tests
// @codeCoverageIgnore

if (!class_exists('Kirby\Editor\Block')) {
    class Block { }
}

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
            return $this->kirby()->api()->parent($this->attrs()->guid()->value());
        } catch (Throwable $e) {
            return null;
        }
    }

    public function isEmpty(): bool
    {
        return empty($this->image()) === true && $this->attrs()->src()->isEmpty();
    }

    public function markdown(): string
    {
        $image = $this->image();
        $attrs = [
            'image' => $image ? $image->id() : $this->attrs()->src(), // TODO: lazysrcset
            'alt' => $this->attrs()->alt(),
            'link' => $this->attrs()->link(),
            'class' => $this->attrs()->css(),
            'caption' => $this->attrs()->caption(),
            // TODO: link Kirby\Text\KirbyTag::$types['image']['attr']
            // TODO: link \Bnomei\Srcset::kirbytagAttrs()
        ];
        return kirbyTagMaker($attrs) . PHP_EOL . PHP_EOL;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        if ($image = $this->image()) {
            $data['attrs'] = array_merge($data['attrs'] ?? [], [
                'guid' => $image->panelUrl(true),
                'ratio' => $image->ratio(),
                'src' => $image->url(),
            ]);
        } else {
            unset($data['attrs']['guid']);
        }
        return $data;
    }
}
