<?php
    // $file, $lazy, $isLazy, $preset, $sources, $img, $autoSizes, $prefix, $class, $imgclass
?>
<picture class="srcset <?= $class ?>" data-preset="<?= $preset ?>"><?php foreach ($sources as $s): ?><source <?= ($isLazy?$prefix:'') ?>srcset="<?= $s['srcset'] ?>" type="<?= $s['type'] ?>" /><?php endforeach; ?> <img class="<?= $lazy ?> <?= $imgclass ?>" <?= ($autoSizes?'data-sizes="auto"':'') ?> <?= ($isLazy?$prefix:'') ?>src="<?= $img['src'] ?>" alt="<?= $img['alt'] ?>" /></picture>
