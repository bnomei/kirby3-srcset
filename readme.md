# Kirby 3 Srcset

![GitHub release](https://img.shields.io/github/release/bnomei/kirby3-srcset.svg?maxAge=1800) ![License](https://img.shields.io/github/license/mashape/apistatus.svg) ![Kirby Version](https://img.shields.io/badge/Kirby-3%2B-black.svg)

Kirby 3 Plugin for creating image srcset using picture or image element

This plugin is free but if you use it in a commercial project please consider to [make a donation 🍻](https://www.paypal.me/bnomei/3).

## Notice

- Why the picture element? Because having multiple `sources` with different mime types can improve pagespeed. For example: This is the only way to use `webp` and have a fallack to `jpg` for browsers that [do not support it](https://caniuse.com/#feat=webp).
- You will need a Picture Polyfill for [IE11 support](https://caniuse.com/#search=picture). This plugin does not provide this.
- This plugin is only a quick hack until @fabianmichael ports his [awesome imageset plugin](https://github.com/fabianmichael/kirby-imageset) for Kirby 3
- Javascript library for lazy loading is not included since that should be part of the websites build chain.
- A `sizes` attribute is not defined since js lib [lazysizes](https://github.com/aFarkas/lazysizes) will create these on-the-fly based on actual screen size of image.


## Usage

```php
    echo $page->image('ukulele.jpg')->srcset();
    echo $page->image('ukulele.jpg')->srcset('default'); // 320 and original file size
    echo $page->image('ukulele.jpg')->srcset('breakpoints'); // 576, 768, 992, 1200
    echo $page->image('ukulele.jpg')->srcset([320, 640, 960]);

    // choosing if lazy is possible global or override on call
    // default: null => config value, true => will be flagged for lazyloading
    echo $page->image('ukulele.jpg')->srcset('breakpoints', true); // null, true, false, 'classname'
```

**non-lazy**
```html
<picture class="srcset" data-preset="default">
    <source srcset="http://../media/pages/home/test-320x160-q90.jpg 320w, http://../media/pages/home/test-640x320-q90.jpg 640w, http://../media/pages/home/test-960x480-q90.jpg 960w" type="image/jpeg" />
    <img src="http://../media/pages/home/test-960x480-q90.jpg" alt="test.jpg or img.alt.fieldname" />
</picture>
```

**lazy**
```html
<picture class="srcset" data-preset="default">
    <source data-srcset="http://../media/pages/home/test-320x160-q90.jpg 320w, http://../media/pages/home/test-640x320-q90.jpg 640w, http://../media/pages/home/test-960x480-q90.jpg 960w" type="image/jpeg" />
    <img class="lazyload" data-src="http://../media/pages/home/test-960x480-q900.jpg" alt="test.jpg or img.alt.fieldname" />
</picture>
```

## Usage Kirby Tag

```
# like image tag
(image: myfile.jpg)

# supports preset as string and lazy options
(srcset: myfile.jpg)
(srcset: myfile.jpg lazy: true)
(srcset: myfile.jpg preset: default)
(srcset: myfile.jpg preset: breakpoints lazy: true)

# preset option takes various formats
# string, number(s), with and without px, comma and brackets
(srcset: myfile.jpg preset: breakpoints)
(srcset: myfile.jpg preset: 320 640 960)
(srcset: myfile.jpg preset: [320, 640, 960])
(srcset: myfile.jpg preset: 320px, 640px, 960px)

# but NOT ranges (like imageset does)
(srcset: myfile.jpg preset: 320-960, 3) // fails!
```


## FAQ

- [Override default image tag](https://github.com/bnomei/kirby3-srcset/issues/2)

## Options explained
```php
'lazy' => false, // bool or class-name, for lozad or lazysizes etc. true => 'lazyload'
'autosizes' => false, // if true will add `data-sizes="auto"`
'snippet' => 'plugin-srcset-picture', // or plugin-srcset-img
// override preset array to create your own list of widths
'presets' => [
    'default' => [0, 320], // will generate original (value = 0) and 320px width thumb
    'breakpoints' => [576, 768, 992, 1200], // common breakpoints
],
// https://github.com/k-next/kirby/blob/master/src/Toolkit/Mime.php
// by default only a source of same type a original will be added
// but setting mime types here you could add more but...
'types' => [],
// ... you have to create the variants yourself. there might be
// another plugin that can do that one day.
// then you can call it here and use the mime type/
'resize' => function (\Kirby\Cms\File $file, int $width, string $type) {
    // NOTE: override and do something with $type (mime type string)
    // return $file->yourImageConverter($width, $type);
    return $file->resize($width);
}
```

## Other Settings

**autosizes**
- default: `false` Please [read the manual](https://github.com/aFarkas/lazysizes#markup-api) before activating this setting. 

> TIP: You might need to add CSS as well.
```css
img[data-sizes="auto"] { display: block; width: 100%; }
```

**img.alt.fieldname**
- default: `caption` will call $file->caption()` if exists or use `$file->filename()`

**types.addsource**
- default: `false` will not automatically add mime-type of source to registered types. But if no type is set (empty array) the mime type of source is added.


## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/kirby3-srcset/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.

