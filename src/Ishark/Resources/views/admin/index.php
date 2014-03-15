<?php $this->layout('admin::layout') ?>

<h1>Admin</h1>
<h2><?php echo count($this->images) ?> Images / <?php echo $this->filesize ?> GB</h2>

<ul class="list-unstyled list-inline">
    <?php

    foreach ($this->images as $image) {
        $thumb = str_replace('.', '.thumb.', $image);
        ?>
        <li>
            <a class="thumbnail" href="http://<?php echo $this->domain ?>/<?php echo $image ?>" target="_blank">
                <img class="lazy" data-original="<?php echo $thumb ?>" style="width: 100px;height: 100px">
            </a>
        </li>
    <?php
    }
    ?>
</ul>