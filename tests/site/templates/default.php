<html>
    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.1/normalize.css" integrity="sha256-WAgYcAck1C1/zEl5sBl5cfyhxtLgKGdpI3oKyJffVRI=" crossorigin="anonymous">
        <style>
            figure { margin:0; width: 100%; }
            img[data-sizes="auto"] { display: block; width: 100%; }
            /* class name must matches the value of `ratio` setting. */
            .lazysrcset-ratio {
                position: relative;
            }
            .lazysrcset-ratio > img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: block;
            }
            .lazysrcset-ratio > img:after {
                content: '';
                width: 100%;
                height: 0;
                display: block;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/lazysizes@5.1.1/lazysizes.min.js" integrity="sha256-6zKmNZVeImc0d1Y55vm4So/0W5mbwWiPS4zJt3F4t2A=" crossorigin="anonymous"></script>
    </head>
    <body class="<?= option('debug') ? 'debug':'' ?>">
        <article>
            <?= $page->textplain()->kirbytext() ?>
        </article>
        <article>
            <?= $page->text()->blocks() ?>
        </article>
    </body>
</html>
