<?php /** 开源工具详情页 */
$color = $tool['color'] ?? '#10b981';
?>
<section class="detail-hero" style="--brand: <?= e($color) ?>">
    <div class="container detail-hero-inner">
        <nav class="crumbs">
            <a href="<?= url('/') ?>">目录</a>
            <span class="sep">/</span>
            <span class="current"><?= e($tool['name']) ?></span>
        </nav>
        <div class="detail-head">
            <span class="avatar xl" style="background: <?= e($color) ?>; color: <?= text_on($color) ?>">
                <?= e(initial_of($tool['name'])) ?>
            </span>
            <div>
                <div class="tool-eyebrow">开源项目</div>
                <h1 class="detail-name"><?= e($tool['name']) ?></h1>
                <p class="detail-desc"><?= e($tool['description']) ?></p>
                <div class="detail-meta">
                    <?php if (!empty($tool['language'])): ?><span class="chip chip-lang"><?= e($tool['language']) ?></span><?php endif; ?>
                    <?php if (!empty($tool['license'])): ?><span class="chip"><?= e($tool['license']) ?></span><?php endif; ?>
                    <?php if (!empty($tool['stars'])): ?>
                        <span class="chip chip-star"><svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 2l2.9 6.3 6.9.7-5.1 4.6 1.4 6.8L12 17.8 5.9 20.4l1.4-6.8L2.2 9l6.9-.7L12 2z"/></svg><?= format_count((int)$tool['stars']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="detail-actions">
                    <?php if (!empty($tool['website'])): ?>
                        <a class="btn" href="<?= e($tool['website']) ?>" target="_blank" rel="noopener nofollow">访问官网</a>
                    <?php endif; ?>
                    <?php if (!empty($tool['repo_url'])): ?>
                        <a class="btn btn-ghost" href="<?= e($tool['repo_url']) ?>" target="_blank" rel="noopener nofollow">查看源码</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container section">
    <div class="section-head left">
        <h2><?= e($tool['name']) ?> 可以替代</h2>
        <p>以下是它能够替换的商业软件</p>
    </div>
    <?php if ($alts): ?>
        <div class="software-grid">
            <?php foreach ($alts as $a): ?>
                <?php $a['alt_count'] = 0; ?>
                <?= View::partial('partials/software_card', ['a' => $a]) ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty"><p>暂无关联的商业软件。</p></div>
    <?php endif; ?>
</section>
