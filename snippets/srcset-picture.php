<?php
    // $file, $lazy, $isLazy, $preset, $sources, $img, $autoSizes
?>
<picture class="srcset" data-preset="<?= $preset ?>"><?php foreach ($sources as $s): ?><source <?= ($isLazy?'data-':'') ?>srcset="<?= $s['srcset'] ?>" type="<?= $s['type'] ?>" /><?php endforeach; ?> <img class="<?= $lazy ?>" <?= ($autoSizes?'data-sizes="auto"':'') ?> <?= ($isLazy?'data-':'') ?>src="<?= $img['src'] ?>" alt="<?= $img['alt'] ?>" /></picture>
