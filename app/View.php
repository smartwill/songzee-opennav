<?php
// 视图渲染辅助
declare(strict_types=1);

class View
{
    /** 在布局中渲染页面视图 */
    public static function render(string $view, array $data = [], string $title = ''): void
    {
        $content = self::partial($view, $data);
        $siteName = config('site.name');
        $pageTitle = $title ? ($title . ' · ' . $siteName) : ($siteName . ' — ' . config('site.tagline'));
        $q = $_GET['q'] ?? '';
        require BASE_PATH . '/app/views/layout.php';
    }

    /** 渲染局部视图并返回字符串 */
    public static function partial(string $view, array $data = []): string
    {
        $file = BASE_PATH . '/app/views/' . $view . '.php';
        if (!is_file($file)) return '';
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string)ob_get_clean();
    }
}
