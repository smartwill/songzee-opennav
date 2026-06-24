<?php /** 商业软件详情页 */ ?>
<?php $color = $alt['color'] ?? '#f97316'; ?>

<section class="detail-hero" style="--brand: <?= e($color) ?>">
    <div class="container detail-hero-inner">
        <nav class="crumbs">
            <a href="<?= url('/') ?>">目录</a>
            <span class="sep">/</span>
            <?php if (!empty($alt['category_slug'])): ?>
                <a href="<?= url('/category/' . $alt['category_slug']) ?>"><?= e($alt['category_name']) ?></a>
                <span class="sep">/</span>
            <?php endif; ?>
            <span class="current"><?= e($alt['name']) ?></span>
        </nav>

        <div class="detail-head">
            <span class="avatar xl" style="background: <?= e($color) ?>; color: <?= text_on($color) ?>">
                <?= e(initial_of($alt['name'])) ?>
            </span>
            <div>
                <h1 class="detail-name"><?= e($alt['name']) ?></h1>
                <?php if (!empty($alt['tagline'])): ?>
                    <p class="detail-tag"><?= e($alt['tagline']) ?></p>
                <?php endif; ?>
                <p class="detail-desc"><?= e($alt['description']) ?></p>
                <div class="detail-actions">
                    <?php if (!empty($alt['website'])): ?>
                        <a class="btn" href="<?= e($alt['website']) ?>" target="_blank" rel="noopener nofollow">
                            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 3h7v7"/><path d="M10 14 21 3"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>
                            访问官网
                        </a>
                    <?php endif; ?>
                    <span class="count-badge"><?= count($tools) ?> 个开源替代</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container section">
    <?php if ($tools): ?>
        <div class="section-head left">
            <h2><?= e($alt['name']) ?> 的开源替代</h2>
            <p>以下是功能相近、可自由使用的开源软件</p>
        </div>
        <div class="tool-grid">
            <?php foreach ($tools as $t): ?>
                <?= View::partial('partials/tool_card', ['t' => $t]) ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty">
            <p>暂未收录开源替代，欢迎贡献。</p>
        </div>
    <?php endif; ?>
</section>

<?php if ($related): ?>
<section class="container section">
    <div class="section-head left">
        <h2>同分类下的其他软件</h2>
        <p>同样属于「<?= e($alt['category_name'] ?? '') ?>」</p>
    </div>
    <div class="software-grid">
        <?php foreach ($related as $a): ?>
            <?= View::partial('partials/software_card', ['a' => $a]) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
