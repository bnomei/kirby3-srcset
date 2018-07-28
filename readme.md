# Kirby 3 Srcset

![GitHub release](https://img.shields.io/github/release/bnomei/kirby3-srcset.svg?maxAge=1800) ![License](https://img.shields.io/github/license/mashape/apistatus.svg) ![Kirby Version](https://img.shields.io/badge/Kirby-3%2B-black.svg)

Kirby 3 Plugin for creating image srcset using picture element

This plugin is free but if you use it in a commercial project please consider to [make a donation 🍻](https://www.paypal.me/bnomei/5).

## Notice

- You will need a Picture Polyfill for [IE11 support](https://caniuse.com/#search=picture). This plugin does not provide this.
- This plugin is only a quick hack until @fabianmichael ports his [awesome imageset plugin](https://github.com/fabianmichael/kirby-imageset) for Kirby 3
- Javascript library for lazy loading is not included since that should be part of the websites build chain.

## Usage

```php
    echo $page->image('ukulele.jpg')->srcset();
    echo $page->image('ukulele.jpg')->srcset('default');
    echo $page->image('ukulele.jpg')->srcset('breakpoints');
```

**non-lazy**
```html
<picture class="srcset" data-preset="default">
    <source srcset="http://../media/pages/home/test-320x160-q90.jpg 320w, http://../media/pages/home/test-640x320-q90.jpg 640w" type="image/jpeg" />
    <noscript><img src="http://../media/pages/home/test-640x320-q90.jpg" alt="test.jpg" /></noscript>
</picture>
```

**lazy**
```html
<picture class="srcset lazy" data-preset="default">
    <source data-srcset="http://../media/pages/home/test-320x160-q90.jpg 320w, http://../media/pages/home/test-640x320-q90.jpg 640w" type="image/jpeg" />
    <noscript><img src="http://../media/pages/home/test-640x320-q90.jpg" alt="test.jpg" /></noscript>
</picture>
```

## Performance

All srcset images are created as regular thumbs but at the time the php script requests them. There is no jobs logik like the awesome imagekit has.
Also if you have an optimzer plugin like my [imageoptim thumbs](https://github.com/bnomei/kirby3-thumb-imageoptim) installed the optimization will done one after another. this can take a while and even cause an timeout multiple times.

Solution? Use fewer widths in presets, add images one by one and wait for imagekit.

## Options explained
```php
'lazy' => false, // bool or class-name, for lozad or lazysizes etc.
// override preset array to create your own list of widths
'presets' => [
    'default' => [320], // will generate original and 320px width thumb
    'breakpoints' => [576, 768, 992, 1200], // common breakpoints
],
// be default only a source of same type a original will be added
// but setting mime types here you could add more but...
'types' => [],
// ... you have to create the variants yourself. there might be
// another plugin that can do that one day.
// then you can call it here and use the mime type/
'resize' => function ($file, $width, $type) {
    // NOTE: override and do something with $type
    return $file->resize($width);
}
```


## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/kirby3-srcset/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.

