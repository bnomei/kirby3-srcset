<?php
    // $file, $lazy, $isLazy, $preset, $sources, $img, $autoSizes, $prefix, $class, $imgclass

    $firstSource = null;
    foreach ($sources as $s) {
        $firstSource = $s;
        break;
    }
?>
<img class="srcset <?= $lazy ?> <?= $class ?> <?= $imgclass ?>" <?= ($autoSizes?'data-sizes="auto"':'') ?> data-preset="<?= $preset ?>" <?= ($isLazy?$prefix:'') ?>srcset="<?= $firstSource['srcset'] ?>" <?= ($isLazy?$prefix:'') ?>src="<?= $img['src'] ?>" alt="<?= $img['alt'] ?>" />
