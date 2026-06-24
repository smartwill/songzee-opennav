<?php
// 页面控制器：首页 / 详情 / 分类 / 搜索 / 工具 / 关于 / 404
declare(strict_types=1);

class Controllers
{
    /** 首页 */
    public static function home(): void
    {
        $grouped     = AlternativeRepository::groupedAlpha();
        $categories  = CategoryRepository::withCounts();
        $featured    = ToolRepository::featured(10);
        $stats = [
            'software' => AlternativeRepository::count(),
            'tools'    => ToolRepository::count(),
            'cats'     => count($categories),
        ];
        View::render('home', compact('grouped', 'categories', 'featured', 'stats'));
    }

    /** 商业软件详情页 */
    public static function detail(array $params): void
    {
        $alt = AlternativeRepository::bySlug($params['slug']);
        if (!$alt) {
            self::notFound();
            return;
        }
        $tools    = AlternativeRepository::tools((int)$alt['id']);
        $related  = $alt['category_id']
            ? AlternativeRepository::related((int)$alt['category_id'], (int)$alt['id'])
            : [];
        View::render('detail', compact('alt', 'tools', 'related'),
            $alt['name'] . ' 的开源替代');
    }

    /** 分类页 */
    public static function category(array $params): void
    {
        $cat = CategoryRepository::bySlug($params['slug']);
        if (!$cat) {
            self::notFound();
            return;
        }
        $items = AlternativeRepository::byCategory((int)$cat['id']);
        View::render('category', compact('cat', 'items'), $cat['name']);
    }

    /** 搜索页 */
    public static function search(): void
    {
        $q = trim($_GET['q'] ?? '');
        $results = $q !== '' ? SearchService::query($q) : ['alternatives' => [], 'tools' => [], 'categories' => []];
        View::render('search', compact('q', 'results'),
            $q !== '' ? ('搜索：' . $q) : '搜索');
    }

    /** 开源工具详情页 */
    public static function tool(array $params): void
    {
        $tool = ToolRepository::bySlug($params['slug']);
        if (!$tool) {
            self::notFound();
            return;
        }
        $alts = ToolRepository::alternatives((int)$tool['id']);
        View::render('tool', compact('tool', 'alts'),
            $tool['name']);
    }

    /** 关于页 */
    public static function about(): void
    {
        $stats = [
            'software' => AlternativeRepository::count(),
            'tools'    => ToolRepository::count(),
        ];
        View::render('about', compact('stats'), '关于');
    }

    /** 404 */
    public static function notFound(): void
    {
        http_response_code(404);
        View::render('404', [], '页面未找到');
    }
}
