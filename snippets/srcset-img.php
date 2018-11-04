<?php
    // $file, $lazy, $isLazy, $preset, $sources, $img, $autoSizes

    $firstSource = null;
    foreach ($sources as $s) {
        $firstSource = $s;
        break;
    }
?>
<img class="srcset <?= $lazy ?>" <?= ($autoSizes?'data-sizes="auto"':'') ?> data-preset="<?= $preset ?>" <?= ($isLazy?'data-':'') ?>srcset="<?= $firstSource['srcset'] ?>" <?= ($isLazy?'data-':'') ?>src="<?= $img['src'] ?>" alt="<?= $img['alt'] ?>" />
