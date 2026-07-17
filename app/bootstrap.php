<?php
// 引导文件：加载配置、建立数据库连接、定义全局辅助函数
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// 加载配置：站点配置 + 数据库配置（数据库配置不入库）
$config = require BASE_PATH . '/config/config.php';
$dbConfig = require BASE_PATH . '/config/db.php';
$config['db'] = $dbConfig['db'];

/**
 * 获取全局配置
 */
function config(string $key = null)
{
    global $config;
    if ($key === null) return $config;
    $keys = explode('.', $key);
    $val = $config;
    foreach ($keys as $k) {
        $val = $val[$k] ?? null;
    }
    return $val;
}

/**
 * 数据库连接（单例 PDO）
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $c = config('db');
    $dsn = "mysql:host={$c['host']};dbname={$c['name']};charset={$c['charset']}";
    $pdo = new PDO($dsn, $c['user'], $c['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

/* ----------------------------- 通用辅助函数 ----------------------------- */

/** HTML 转义输出 */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/** 站点 URL */
function url(string $path = ''): string
{
    return '/' . ltrim($path, '/');
}

/** 静态资源 URL */
function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
}

/** 将数字格式化为紧凑形式：1200 -> 1.2k */
function format_count(int $n): string
{
    if ($n >= 1000000) return round($n / 1000000, 1) . 'M';
    if ($n >= 1000) return round($n / 1000, 1) . 'k';
    return (string)$n;
}

/** 取首字母（用于无 Logo 时的字母头像） */
function initial_of(string $name): string
{
    return mb_strtoupper(mb_substr(trim($name), 0, 1));
}

/** 计算颜色对比文字（黑/白） */
function text_on(string $hex): string
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $luminance = (0.299*$r + 0.587*$g + 0.114*$b) / 255;
    return $luminance > 0.6 ? '#1a1a1a' : '#ffffff';
}

/** 将文本截断到指定长度 */
function excerpt(?string $text, int $limit = 120): string
{
    $text = (string)$text;
    if (mb_strlen($text) <= $limit) return $text;
    return mb_substr($text, 0, $limit) . '…';
}
