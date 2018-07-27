# Kirby 3 Srcset

Kirby 3 Plugin for creating image srcset using picture element

## Notice

> You will need a Picture Polyfill for [IE11 support](https://caniuse.com/#search=picture). This plugin does not provide this.

> This plugin is only a quick hack until @fabianmichael ports his [awesome imageset plugin](https://github.com/fabianmichael/kirby-imageset) for Kirby 3

## Usage

```php
    echo $page->image('ukulele.jpg')->srcset();
    echo $page->image('ukulele.jpg')->srcset('default');
    echo $page->image('ukulele.jpg')->srcset('breakpoints');
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

