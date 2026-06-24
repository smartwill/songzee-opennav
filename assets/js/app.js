// 开源替代 — 前端交互
(function () {
    'use strict';

    /* ---------- 主题切换 ---------- */
    var root = document.documentElement;
    var toggle = document.getElementById('themeToggle');
    if (toggle) {
        toggle.addEventListener('click', function () {
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            try { localStorage.setItem('theme', next); } catch (e) {}
        });
    }

    /* ---------- 头部滚动状态 ---------- */
    var header = document.getElementById('siteHeader');
    var onScroll = function () {
        if (!header) return;
        if (window.scrollY > 8) header.classList.add('scrolled');
        else header.classList.remove('scrolled');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* ---------- 移动端菜单 ---------- */
    var menuBtn = document.getElementById('menuToggle');
    var nav = document.getElementById('mainNav');
    if (menuBtn && nav) {
        menuBtn.addEventListener('click', function () {
            var open = nav.classList.toggle('open');
            menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        nav.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                nav.classList.remove('open');
                menuBtn.setAttribute('aria-expanded', 'false');
            });
        });
    }

    /* ---------- 键盘快捷键：/ 聚焦搜索 ---------- */
    var searchInput = document.getElementById('navSearchInput');
    document.addEventListener('keydown', function (e) {
        if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            var target = searchInput || document.querySelector('.hero-search input');
            if (target) { e.preventDefault(); target.focus(); }
        }
    });

    /* ---------- 字母索引：当前字母高亮 ---------- */
    var alphaLinks = document.querySelectorAll('.alpha-index a');
    var letterBlocks = document.querySelectorAll('.letter-block');
    if (alphaLinks.length && letterBlocks.length) {
        var markActive = function () {
            var offset = 150, current = null;
            letterBlocks.forEach(function (b) {
                if (b.getBoundingClientRect().top - offset <= 0) current = b.id;
            });
            alphaLinks.forEach(function (a) {
                var isActive = a.getAttribute('href') === '#' + (current || '');
                a.style.background = isActive ? 'var(--accent)' : '';
                a.style.color = isActive ? 'var(--accent-ink)' : '';
            });
        };
        window.addEventListener('scroll', markActive, { passive: true });
        markActive();
    }
})();
