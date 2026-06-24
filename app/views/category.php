<?php /** 分类页 */ ?>
<section class="container section page-head">
    <nav class="crumbs">
        <a href="<?= url('/') ?>">目录</a>
        <span class="sep">/</span>
        <span class="current"><?= e($cat['name']) ?></span>
    </nav>
    <h1 class="page-title"><?= e($cat['name']) ?></h1>
    <p class="page-sub"><?= e($cat['description']) ?></p>
    <span class="count-pill"><?= count($items) ?> 款软件</span>
</section>

<section class="container section">
    <?php if ($items): ?>
        <div class="software-grid">
            <?php foreach ($items as $a): ?>
                <?= View::partial('partials/software_card', ['a' => $a]) ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty"><p>该分类下暂无收录。</p></div>
    <?php endif; ?>
</section>
