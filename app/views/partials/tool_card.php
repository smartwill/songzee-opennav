<?php /** 开源工具卡片
 * 可用变量: $t (工具行：name,slug,description,website,repo_url,stars,license,language,color)
 */
$color = $t['color'] ?? '#10b981';
?>
<article class="tool-card" style="--brand: <?= e($color) ?>">
    <div class="tc-head">
        <span class="avatar lg" style="background: <?= e($color) ?>; color: <?= text_on($color) ?>">
            <?= e(initial_of($t['name'])) ?>
        </span>
        <div class="tc-title">
            <h3><a href="<?= url('/tool/' . $t['slug']) ?>"><?= e($t['name']) ?></a></h3>
            <div class="tc-meta">
                <?php if (!empty($t['language'])): ?>
                    <span class="chip chip-lang"><?= e($t['language']) ?></span>
                <?php endif; ?>
                <?php if (!empty($t['license'])): ?>
                    <span class="chip"><?= e($t['license']) ?></span>
                <?php endif; ?>
                <?php if (!empty($t['stars'])): ?>
                    <span class="chip chip-star">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 2l2.9 6.3 6.9.7-5.1 4.6 1.4 6.8L12 17.8 5.9 20.4l1.4-6.8L2.2 9l6.9-.7L12 2z"/></svg>
                        <?= format_count((int)$t['stars']) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <p class="tc-desc"><?= e(excerpt($t['description'], 150)) ?></p>
    <div class="tc-foot">
        <?php if (!empty($t['website'])): ?>
            <a class="btn btn-sm" href="<?= e($t['website']) ?>" target="_blank" rel="noopener nofollow">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M2 12h20M12 2c2.5 2.7 4 6.5 4 10s-1.5 7.3-4 10c-2.5-2.7-4-6.5-4-10s1.5-7.3 4-10z"/></svg>
                官网
            </a>
        <?php endif; ?>
        <?php if (!empty($t['repo_url'])): ?>
            <a class="btn btn-sm btn-ghost" href="<?= e($t['repo_url']) ?>" target="_blank" rel="noopener nofollow">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 .5A11.5 11.5 0 0 0 .5 12 11.5 11.5 0 0 0 8.4 23c.6.1.8-.3.8-.6v-2c-3.3.7-4-1.6-4-1.6-.5-1.4-1.3-1.8-1.3-1.8-1.1-.7 0-.7 0-.7 1.2 0 1.9 1.2 1.9 1.2 1 1.8 2.8 1.3 3.5 1 .1-.8.4-1.3.8-1.6-2.7-.3-5.5-1.3-5.5-6 0-1.3.5-2.4 1.2-3.2 0-.3-.5-1.5.2-3.1 0 0 1-.3 3.3 1.2a11.5 11.5 0 0 1 6 0c2.3-1.5 3.3-1.2 3.3-1.2.7 1.6.2 2.8.1 3.1.8.8 1.2 1.9 1.2 3.2 0 4.6-2.8 5.6-5.5 5.9.4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6A11.5 11.5 0 0 0 23.5 12 11.5 11.5 0 0 0 12 .5z"/></svg>
                仓库
            </a>
        <?php endif; ?>
    </div>
</article>
