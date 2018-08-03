<?php
    // $file, $lazy, $isLazy, $preset, $sources, $img
?>
<picture class="srcset" data-preset="<?= $preset ?>">
    <?php foreach ($sources as $s): ?>
    <source <?= ($isLazy?'data-':'') ?>srcset="<?= $s['srcset'] ?>" type="<?= $s['type'] ?>" />
    <?php endforeach; ?>
    <img class="<?= $lazy ?>" <?= ($isLazy?'data-':'') ?>src="<?= $img['src'] ?>" alt="<?= $img['alt'] ?>" />
</picture>
