<?php
    // $file, $lazy, $isLazy, $preset, $sources, $img
?>
<picture class="srcset <?= $lazy ?>" data-preset="<?= $preset ?>">
    <?php foreach ($sources as $s): ?>
    <source <?= ($isLazy?'data-':'') ?>srcset="<?= $s['srcset'] ?>" type="<?= $s['type'] ?>" />
    <?php endforeach; ?>
    <?= ($isLazy?'<noscript>':'') ?><img src="<?= $img['src'] ?>" alt="<?= $img['alt'] ?>" /><?= ($isLazy?'</noscript>':'') ?>
</picture>
