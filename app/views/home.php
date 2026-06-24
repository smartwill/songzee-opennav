<?php /** 首页 */ ?>
<section class="hero">
    <div class="container">
        <div class="hero-badges">
            <span class="pill pill-live"><span class="dot"></span>开源软件替代品目录</span>
            <span class="pill"><?= number_format($stats['software']) ?>+ 商业软件 · <?= number_format($stats['tools']) ?>+ 开源项目</span>
        </div>
        <h1 class="hero-title">
            用<span class="grad">开源</span>替代<br>
            你常用的商业软件
        </h1>
        <p class="hero-sub">
            发现闭源软件背后更自由、更安全、更省钱的免费开源替代方案。
            浏览目录、按分类筛选，找到属于你的那一款。
        </p>

        <form class="hero-search" action="<?= url('/search') ?>" method="get">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="q" placeholder="搜索任意软件，如 Notion、Photoshop、Slack…" autocomplete="off" autofocus>
            <button type="submit">搜索替代</button>
        </form>

        <div class="hero-suggest">
            <span>热门：</span>
            <?php foreach (['notion','figma','slack','photoshop','google-analytics','trello'] as $slug): ?>
                <a href="<?= url('/' . $slug) ?>">#<?= e($slug) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="hero-stats">
            <div class="stat"><b><?= number_format($stats['software']) ?>+</b><span>商业软件</span></div>
            <div class="stat-sep"></div>
            <div class="stat"><b><?= number_format($stats['tools']) ?>+</b><span>开源项目</span></div>
            <div class="stat-sep"></div>
            <div class="stat"><b><?= number_format($stats['cats']) ?></b><span>主题分类</span></div>
        </div>
    </div>
</section>

<!-- 分类导航 -->
<section class="container section">
    <div class="section-head">
        <h2>按分类浏览</h2>
        <p>从你最关心的领域开始，找到值得替换的闭源软件</p>
    </div>
    <div class="cat-grid">
        <?php foreach ($categories as $c): ?>
            <a class="cat-card" href="<?= url('/category/' . $c['slug']) ?>">
                <span class="cat-count"><?= (int)$c['software_count'] ?></span>
                <span class="cat-name"><?= e($c['name']) ?></span>
                <span class="cat-desc"><?= e(excerpt($c['description'], 40)) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- 字母索引目录 -->
<section class="container section" id="directory">
    <div class="section-head">
        <h2>所有商业软件</h2>
        <p>共 <?= number_format($stats['software']) ?> 款，点击查看它们的开源替代</p>
    </div>

    <div class="alpha-index" id="alphaIndex">
        <?php foreach (array_keys($grouped) as $letter): ?>
            <a href="#letter-<?= e($letter) ?>"><?= e($letter) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="directory">
        <?php foreach ($grouped as $letter => $items): ?>
            <div class="letter-block" id="letter-<?= e($letter) ?>">
                <div class="letter-sticky"><?= e($letter) ?></div>
                <div class="software-grid">
                    <?php foreach ($items as $a): ?>
                        <?= View::partial('partials/software_card', ['a' => $a]) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- 精选开源项目 -->
<section class="container section">
    <div class="section-head">
        <h2>社区精选开源项目</h2>
        <p>最受开发者欢迎的开源替代工具</p>
    </div>
    <div class="tool-grid">
        <?php foreach ($featured as $t): ?>
            <?= View::partial('partials/tool_card', ['t' => $t]) ?>
        <?php endforeach; ?>
    </div>
</section>
