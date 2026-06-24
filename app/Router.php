<?php
// 极简路由器：解析 REQUEST_URI 并分发到控制器
declare(strict_types=1);

class Router
{
    private array $routes = [];
    private array $notFound;

    public function get(string $pattern, callable $handler): void
    {
        $this->routes[] = ['GET', $pattern, $handler];
    }

    public function setNotFound(callable $handler): void
    {
        $this->notFound = $handler;
    }

    /** 从 REQUEST_URI 中提取去掉查询串后的路径 */
    private function path(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        // 兼容子目录部署：去掉脚本所在目录
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $base = str_replace('\\', '/', dirname($scriptName));
        if ($base !== '/' && strpos($path, $base) === 0) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . ltrim($path, '/');
        return $path === '/.' ? '/' : $path;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = $this->path();

        foreach ($this->routes as [$m, $pattern, $handler]) {
            if ($m !== $method) continue;
            $regex = $this->compile($pattern);
            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $handler($params);
                return;
            }
        }
        http_response_code(404);
        ($this->notFound)();
    }

    /** 将 {param} 转为命名捕获组 */
    private function compile(string $pattern): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]\w*)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $pattern . '$#';
    }
}
