<?php /** 关于页 */ ?>
<section class="container section page-head">
    <h1 class="page-title">关于「开源替代」</h1>
    <p class="page-sub">让每个人都能轻松找到自由软件</p>
</section>

<section class="container section prose">
    <div class="about-grid">
        <div class="about-text">
            <h2>我们相信软件自由</h2>
            <p>「开源替代」是一个开源软件目录，帮助你在闭源、商业软件之外，发现更自由、更透明、更尊重隐私的替代方案。无论是笔记、设计、开发、通讯还是云存储，你都能在这里找到由社区驱动、可自由使用的优秀开源项目。</p>
            <p>我们收录了 <?= number_format($stats['software']) ?> 款商业软件与 <?= number_format($stats['tools']) ?> 个开源项目，并将它们一一对应，让你在最短的时间内完成选型。</p>

            <h2>为什么选择开源</h2>
            <ul>
                <li><strong>自由与控制</strong>：你可以自由使用、研究、修改和分享。</li>
                <li><strong>隐私优先</strong>：数据掌握在自己手中，无需担心被追踪。</li>
                <li><strong>零成本</strong>：绝大多数开源软件完全免费，没有订阅陷阱。</li>
                <li><strong>社区驱动</strong>：由全球开发者共同维护，持续进化。</li>
            </ul>
        </div>
        <aside class="about-stats">
            <div class="about-stat"><b><?= number_format($stats['software']) ?>+</b><span>商业软件</span></div>
            <div class="about-stat"><b><?= number_format($stats['tools']) ?>+</b><span>开源项目</span></div>
            <div class="about-stat"><b>15</b><span>主题分类</span></div>
            <div class="about-stat"><b>100%</b><span>免费使用</span></div>
        </aside>
    </div>
</section>
