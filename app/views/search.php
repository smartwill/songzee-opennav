<?php /** 搜索页 */
extract($results); // alternatives, tools, categories
$total = count($alternatives) + count($tools) + count($categories);
?>
<section class="container section page-head">
    <h1 class="page-title">搜索</h1>
    <form class="hero-search inline" action="<?= url('/search') ?>" method="get">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        <input type="text" name="q" placeholder="输入软件名称或关键词…" value="<?= e($q) ?>" autofocus>
        <button type="submit">搜索</button>
    </form>
    <?php if ($q !== ''): ?>
        <p class="page-sub">关于 “<strong><?= e($q) ?></strong>” 找到 <?= $total ?> 条结果</p>
    <?php endif; ?>
</section>

<?php if ($q === ''): ?>
    <section class="container section">
        <div class="empty"><p>输入关键词开始搜索商业软件或开源工具。</p></div>
    </section>
<?php else: ?>
    <?php if (!empty($alternatives)): ?>
    <section class="container section">
        <div class="section-head left"><h2>商业软件</h2></div>
        <div class="software-grid">
            <?php foreach ($alternatives as $a): ?>
                <?= View::partial('partials/software_card', ['a' => $a]) ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($tools)): ?>
    <section class="container section">
        <div class="section-head left"><h2>开源工具</h2></div>
        <div class="tool-grid">
            <?php foreach ($tools as $t): ?>
                <?= View::partial('partials/tool_card', ['t' => $t]) ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($categories)): ?>
    <section class="container section">
        <div class="section-head left"><h2>分类</h2></div>
        <div class="cat-grid">
            <?php foreach ($categories as $c): ?>
                <a class="cat-card" href="<?= url('/category/' . $c['slug']) ?>">
                    <span class="cat-name"><?= e($c['name']) ?></span>
                    <span class="cat-desc"><?= e(excerpt($c['description'], 40)) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($total === 0): ?>
    <section class="container section">
        <div class="empty"><p>没有找到与 “<?= e($q) ?>” 相关的结果，换个关键词试试？</p></div>
    </section>
    <?php endif; ?>
<?php endif; ?>
