<?php
/**
 * 开源替代目录 - 一键安装向导
 */

session_start();

// 安装步骤
$step = (int)($_GET['step'] ?? 1);
$errors = [];
$config = $_SESSION['install_config'] ?? [];

// 检查是否已安装
if (file_exists(__DIR__ . '/config/config.php')) {
    $currentConfig = require __DIR__ . '/config/config.php';
    try {
        $pdo = new PDO(
            "mysql:host={$currentConfig['db']['host']};dbname={$currentConfig['db']['name']};charset={$currentConfig['db']['charset']}",
            $currentConfig['db']['user'],
            $currentConfig['db']['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('alternatives', $tables)) {
            $alreadyInstalled = true;
        }
    } catch (Exception $e) {
        $alreadyInstalled = false;
    }
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        $config = [
            'db_host'     => trim($_POST['db_host']),
            'db_name'     => trim($_POST['db_name']),
            'db_user'     => trim($_POST['db_user']),
            'db_pass'     => trim($_POST['db_pass']),
            'site_name'   => trim($_POST['site_name']),
            'site_tagline'=> trim($_POST['site_tagline']),
        ];
        
        // 验证输入
        if (empty($config['db_host'])) $errors[] = '请输入数据库主机';
        if (empty($config['db_name'])) $errors[] = '请输入数据库名称';
        if (empty($config['db_user'])) $errors[] = '请输入数据库用户名';
        
        if (empty($errors)) {
            $_SESSION['install_config'] = $config;
            $step = 3;
        }
    } elseif ($step === 3) {
        $config = $_SESSION['install_config'];
        $step = 4; // 直接跳转到完成步骤
    }
}

// 步骤4：执行安装
if ($step === 4 && isset($config)) {
    $installSuccess = false;
    $installMessage = '';
    
    try {
        // 连接数据库
        $pdo = new PDO(
            "mysql:host={$config['db_host']};charset=utf8mb4",
            $config['db_user'],
            $config['db_pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // 创建数据库（如果不存在）
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['db_name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$config['db_name']}`");
        
        // 执行 schema
        $schema = file_get_contents(__DIR__ . '/database/schema.sql');
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        foreach ($statements as $stmt) {
            if ($stmt) $pdo->exec($stmt);
        }
        
        // 执行 seed
        $seed = file_get_contents(__DIR__ . '/database/seed.sql');
        $statements = array_filter(array_map('trim', explode(';', $seed)));
        foreach ($statements as $stmt) {
            if ($stmt && stripos($stmt, 'SET ') !== 0) $pdo->exec($stmt);
        }
        
        // 生成配置文件
        $configContent = <<<EOF
<?php
return [
    'db' => [
        'host'    => '{$config['db_host']}',
        'name'    => '{$config['db_name']}',
        'user'    => '{$config['db_user']}',
        'pass'    => '{$config['db_pass']}',
        'charset' => 'utf8mb4',
    ],
    'site' => [
        'name'    => '{$config['site_name']}',
        'tagline' => '{$config['site_tagline']}',
        'url'     => '/',
        'assets'  => '/assets',
    ],
];
EOF;
        file_put_contents(__DIR__ . '/config/config.php', $configContent);
        
        $installSuccess = true;
        $installMessage = '安装成功！';
        
        // 清理安装会话
        unset($_SESSION['install_config']);
        
    } catch (Exception $e) {
        $installMessage = '安装失败: ' . $e->getMessage();
    }
}

?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>开源替代目录 - 安装向导</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            color: white;
            padding: 32px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .header p {
            opacity: 0.8;
            font-size: 14px;
        }
        .steps {
            display: flex;
            justify-content: center;
            gap: 12px;
            padding: 20px 32px;
            border-bottom: 1px solid #e5e7eb;
        }
        .step {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.3s;
        }
        .step.active {
            background: #f97316;
            color: white;
        }
        .step.done {
            background: #10b981;
            color: white;
        }
        .content {
            padding: 32px;
        }
        .content h2 {
            font-size: 18px;
            margin-bottom: 16px;
            color: #1f2937;
        }
        .content p {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
        }
        .form-group input[type="password"] {
            font-family: monospace;
        }
        .form-group.hint {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: #f97316;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn:hover {
            background: #ea580c;
            transform: translateY(-1px);
        }
        .btn:disabled {
            background: #d1d5db;
            cursor: not-allowed;
            transform: none;
        }
        .errors {
            background: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }
        .errors p {
            color: #dc2626;
            margin: 0;
            font-size: 14px;
        }
        .checklist {
            list-style: none;
            padding: 0;
        }
        .checklist li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .checklist li:last-child {
            border-bottom: none;
        }
        .checklist .icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        .checklist .icon.success {
            background: #dcfce7;
            color: #16a34a;
        }
        .checklist .icon.error {
            background: #fee2e2;
            color: #dc2626;
        }
        .checklist .icon.warning {
            background: #fef3c7;
            color: #d97706;
        }
        .success-box {
            text-align: center;
            padding: 40px 20px;
        }
        .success-box .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
        }
        .success-box h2 {
            margin-bottom: 10px;
        }
        .success-box p {
            margin-bottom: 24px;
        }
        .success-box .btn {
            max-width: 200px;
            margin: 0 auto;
        }
        .already-installed {
            text-align: center;
            padding: 40px;
        }
        .already-installed h2 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>开源替代目录</h1>
            <p>一键安装向导</p>
        </div>
        
        <?php if (isset($alreadyInstalled) && $alreadyInstalled): ?>
            <div class="already-installed">
                <h2>站点已安装</h2>
                <p>检测到站点已安装并正常运行。</p>
                <a href="/" class="btn" style="display:inline-block;">访问站点</a>
            </div>
        <?php else: ?>
            <div class="steps">
                <div class="step <?= $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' ?>">1</div>
                <div class="step <?= $step >= 2 ? ($step > 2 ? 'done' : 'active') : '' ?>">2</div>
                <div class="step <?= $step >= 3 ? ($step > 3 ? 'done' : 'active') : '' ?>">3</div>
                <div class="step <?= $step >= 4 ? 'active' : '' ?>">4</div>
            </div>
            
            <div class="content">
                <?php if ($step === 1): ?>
                    <h2>欢迎使用安装向导</h2>
                    <p>本向导将帮助您快速安装「开源替代目录」网站。</p>
                    
                    <h3 style="margin-top:24px; font-size:16px;">环境要求检查</h3>
                    <ul class="checklist">
                        <?php
                        $phpVersion = phpversion();
                        $phpOk = version_compare($phpVersion, '8.0.0', '>=');
                        ?>
                        <li>
                            <span class="icon <?= $phpOk ? 'success' : 'error' ?>">
                                <?= $phpOk ? '✓' : '✗' ?>
                            </span>
                            <span>PHP 版本 >= 8.0.0 (当前: <?= $phpVersion ?>)</span>
                        </li>
                        
                        <?php
                        $pdoOk = extension_loaded('pdo_mysql');
                        ?>
                        <li>
                            <span class="icon <?= $pdoOk ? 'success' : 'error' ?>">
                                <?= $pdoOk ? '✓' : '✗' ?>
                            </span>
                            <span>PDO MySQL 扩展</span>
                        </li>
                        
                        <?php
                        $rewriteOk = isset($_SERVER['HTTP_MOD_REWRITE']) || function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules());
                        ?>
                        <li>
                            <span class="icon <?= $rewriteOk ? 'success' : 'warning' ?>">
                                <?= $rewriteOk ? '✓' : '!' ?>
                            </span>
                            <span>URL 重写模块 (Apache mod_rewrite 或 Nginx try_files)</span>
                        </li>
                        
                        <?php
                        $writeOk = is_writable(__DIR__ . '/config');
                        ?>
                        <li>
                            <span class="icon <?= $writeOk ? 'success' : 'error' ?>">
                                <?= $writeOk ? '✓' : '✗' ?>
                            </span>
                            <span>config 目录可写</span>
                        </li>
                    </ul>
                    
                    <?php if ($phpOk && $pdoOk && $writeOk): ?>
                        <a href="?step=2" class="btn" style="margin-top:24px;">开始安装</a>
                    <?php else: ?>
                        <p style="color:#dc2626; margin-top:20px;">请先修复以上问题，然后重新加载页面。</p>
                    <?php endif; ?>
                
                <?php elseif ($step === 2): ?>
                    <h2>配置数据库和站点信息</h2>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="errors">
                            <?php foreach ($errors as $error): ?>
                                <p>• <?= $error ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="?step=2">
                        <div class="form-group">
                            <label>数据库主机</label>
                            <input type="text" name="db_host" value="<?= htmlspecialchars($config['db_host'] ?? '127.0.0.1') ?>" placeholder="127.0.0.1">
                        </div>
                        
                        <div class="form-group">
                            <label>数据库名称</label>
                            <input type="text" name="db_name" value="<?= htmlspecialchars($config['db_name'] ?? '') ?>" placeholder="openalternative">
                        </div>
                        
                        <div class="form-group">
                            <label>数据库用户名</label>
                            <input type="text" name="db_user" value="<?= htmlspecialchars($config['db_user'] ?? '') ?>" placeholder="root">
                        </div>
                        
                        <div class="form-group">
                            <label>数据库密码</label>
                            <input type="password" name="db_pass" value="<?= htmlspecialchars($config['db_pass'] ?? '') ?>" placeholder="输入密码">
                        </div>
                        
                        <div class="form-group">
                            <label>站点名称</label>
                            <input type="text" name="site_name" value="<?= htmlspecialchars($config['site_name'] ?? '开源替代') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>站点描述</label>
                            <input type="text" name="site_tagline" value="<?= htmlspecialchars($config['site_tagline'] ?? '发现商业软件的开源替代品') ?>">
                        </div>
                        
                        <button type="submit" class="btn" style="margin-top:8px;">下一步</button>
                    </form>
                
                <?php elseif ($step === 3): ?>
                    <h2>确认安装配置</h2>
                    
                    <div style="background:#f9fafb; border-radius:8px; padding:20px; margin-bottom:20px;">
                        <h4 style="font-size:14px; color:#374151; margin-bottom:12px;">数据库配置</h4>
                        <p><strong>主机：</strong><?= htmlspecialchars($config['db_host']) ?></p>
                        <p><strong>数据库：</strong><?= htmlspecialchars($config['db_name']) ?></p>
                        <p><strong>用户名：</strong><?= htmlspecialchars($config['db_user']) ?></p>
                        <p><strong>密码：</strong>******</p>
                        
                        <h4 style="font-size:14px; color:#374151; margin-top:16px; margin-bottom:12px;">站点配置</h4>
                        <p><strong>站点名称：</strong><?= htmlspecialchars($config['site_name']) ?></p>
                        <p><strong>站点描述：</strong><?= htmlspecialchars($config['site_tagline']) ?></p>
                    </div>
                    
                    <form method="post" action="?step=3">
                        <button type="submit" class="btn">确认安装</button>
                    </form>
                
                <?php elseif ($step === 4): ?>
                    <div class="success-box">
                        <?php if ($installSuccess): ?>
                            <div class="icon">✓</div>
                            <h2>安装成功！</h2>
                            <p>「开源替代目录」已成功安装到您的服务器。</p>
                            <a href="/" class="btn">访问站点</a>
                        <?php else: ?>
                            <div class="icon" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">✗</div>
                            <h2>安装失败</h2>
                            <p><?= htmlspecialchars($installMessage) ?></p>
                            <a href="?step=2" class="btn">返回重试</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
