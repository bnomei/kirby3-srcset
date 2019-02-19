<?php
    // $file, $lazy, $isLazy, $preset, $sources, $img, $prefix
?>
<picture class="srcset" data-preset="<?= $preset ?>"><?php foreach ($sources as $s): ?><source <?= ($isLazy?$prefix:'') ?>srcset="<?= $s['srcset'] ?>" type="<?= $s['type'] ?>" /><?php endforeach; ?> <img class="<?= $lazy ?>" <?= ($isLazy?$prefix:'') ?>src="<?= $img['src'] ?>" alt="<?= $img['alt'] ?>" /></picture>
