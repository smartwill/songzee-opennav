<?php
// 前端控制器（单一入口）
declare(strict_types=1);

// PHP 内置服务器场景：对真实存在的静态资源直接放行，避免走前端控制器
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($requestUri !== '/' && $requestUri !== '/index.php' && is_file(__DIR__ . $requestUri)) {
    return false;
}

require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/View.php';
require __DIR__ . '/app/Router.php';
require __DIR__ . '/app/Controllers.php';
require __DIR__ . '/app/repositories/Repositories.php';

$router = new Router();

// 固定路由优先
$router->get('/',          [Controllers::class, 'home']);
$router->get('/about',     [Controllers::class, 'about']);
$router->get('/search',    [Controllers::class, 'search']);
$router->get('/category/{slug}', [Controllers::class, 'category']);
$router->get('/tool/{slug}',     [Controllers::class, 'tool']);

// 兜底：/{slug} -> 商业软件详情（注册在最后）
$router->get('/{slug}',    [Controllers::class, 'detail']);

$router->setNotFound([Controllers::class, 'notFound']);
$router->dispatch();
