<?php /** 商业软件卡片
 * 可用变量: $a (软件行，含 slug,name,tagline,alt_count,category_slug,category_name,color)
 * 可选: $lazy
 */
$color = $a['color'] ?? '#f97316';
$lazy = $lazy ?? false;
?>
<a class="software-card" href="<?= url('/' . $a['slug']) ?>" style="--brand: <?= e($color) ?>">
    <span class="avatar" style="background: <?= e($color) ?>; color: <?= text_on($color) ?>">
        <?= e(initial_of($a['name'])) ?>
    </span>
    <span class="sc-body">
        <span class="sc-name"><?= e($a['name']) ?></span>
        <?php if (!empty($a['alt_count'])): ?>
        <span class="sc-count"><?= (int)$a['alt_count'] ?> 个替代</span>
        <?php endif; ?>
    </span>
</a>
