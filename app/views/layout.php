<?php /** 布局模板 */ ?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="发现商业软件的开源替代品。浏览笔记、设计、开发、通讯、云存储等分类下，数十款闭源软件的免费开源替代方案。">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <script>
        // 防止主题闪烁：在渲染前应用已存主题
        (function () {
            try {
                var t = localStorage.getItem('theme');
                if (!t) t = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', t);
            } catch (e) {}
        })();
    </script>
</head>
<body>
<div class="bg-grid" aria-hidden="true"></div>
<div class="bg-glow" aria-hidden="true"></div>

<header class="site-header" id="siteHeader">
    <div class="container header-inner">
        <a href="<?= url('/') ?>" class="brand" aria-label="返回首页">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 12l9 4 9-4"/><path d="M3 17l9 4 9-4"/></svg>
            </span>
            <span class="brand-text"><?= e(config('site.name')) ?></span>
        </a>

        <nav class="main-nav" id="mainNav">
            <a href="<?= url('/') ?>">浏览</a>
            <a href="<?= url('/category/note-taking') ?>">分类</a>
            <a href="<?= url('/about') ?>">关于</a>
        </nav>

        <form class="nav-search" action="<?= url('/search') ?>" method="get" role="search">
            <svg class="search-ico" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="q" placeholder="搜索软件，如 Notion、Photoshop…" value="<?= e($q ?? '') ?>" autocomplete="off" id="navSearchInput">
            <span class="kbd">/</span>
        </form>

        <div class="header-actions">
            <button class="icon-btn theme-toggle" id="themeToggle" aria-label="切换主题" title="切换明暗主题">
                <svg class="ico-sun" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                <svg class="ico-moon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
            </button>
            <button class="icon-btn menu-toggle" id="menuToggle" aria-label="菜单" aria-expanded="false">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
        </div>
    </div>
</header>

<main class="site-main">
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <span class="brand-mark sm" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 12l9 4 9-4"/><path d="M3 17l9 4 9-4"/></svg>
            </span>
            <div>
                <strong><?= e(config('site.name')) ?></strong>
                <p><?= e(config('site.tagline')) ?>。一个为开发者与隐私倡导者打造的开源软件目录。</p>
            </div>
        </div>
        <div class="footer-links">
            <div class="footer-col">
                <h4>探索</h4>
                <a href="<?= url('/') ?>">所有软件</a>
                <a href="<?= url('/category/note-taking') ?>">笔记与知识库</a>
                <a href="<?= url('/category/design') ?>">设计与原型</a>
                <a href="<?= url('/category/ide') ?>">开发工具</a>
            </div>
            <div class="footer-col">
                <h4>项目</h4>
                <a href="<?= url('/about') ?>">关于本站</a>
                <a href="<?= url('/search') ?>">搜索</a>
            </div>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>© <?= date('Y') ?> <?= e(config('site.name')) ?> · <?= e(config('site.company')) ?> | <a href="https://www.songzee.com.cn" target="_blank" rel="noopener noreferrer">服装PLM系统</a></span>
        <span>本站数据为演示用途，感谢<a href="https://www.songzee.com.cn" target="_blank" rel="noopener noreferrer">Songzee</a>提供服务</span>
        <span>
            <?php if ($icp = config('site.icp_beian')): ?><a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener"><?= e($icp) ?></a><?php endif; ?>
            <?php if ($mps = config('site.mps_beian')): ?><?php if ($icp) echo ' · '; ?><a href="http://www.beian.gov.cn/portal/registerSystemInfo" target="_blank" rel="noopener"><?= e($mps) ?></a><?php endif; ?>
        </span>
    </div>
</footer>

<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
