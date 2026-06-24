# ai-read.md — AI 快速上手手册

> 本文件供 AI 助手快速熟悉本项目。普通开发者也可参考。
> 最后更新：2026-06-24

## 一句话概览

一个**开源软件替代品目录**网站（openalternative.co 的克隆/中文版）。
技术栈：**PHP 8（原生，无框架）+ MySQL + 手写 CSS/JS**，零构建步骤。

---

## 1. 项目目标

帮助用户发现「商业/闭源软件 → 开源替代」的映射。
- 例：Notion 的开源替代是 AppFlowy / Logseq / Outline 等。
- 形态：按字母排序的软件目录 + 分类导航 + 搜索 + 详情页。

## 2. 技术栈

| 层 | 技术 | 说明 |
|----|------|------|
| 后端 | PHP 8+ | 原生，无框架，`php -S` 即可跑 |
| 数据库 | MySQL 5.7+ | PDO 连接，utf8mb4 |
| 前端 | 原生 HTML/CSS/JS | 无 npm/构建；字体走 Google Fonts CDN |
| 服务器 | Apache（正式）/ PHP 内置服务器（预览） | `.htaccess` 做 URL 重写 |

## 3. 本地环境（已配置）

```
数据库：MySQL @ 127.0.0.1
库名  ：daohang_com
账号  ：daohang_com
密码  ：daohang_com123
访问域名：http://daohang.com   （本地已改 hosts）
```

> 配置文件：[config/config.php](config/config.php)。改库连接信息去这里。

## 4. 目录结构

```
tool-nav/
├── index.php                      # 单一入口 + 前端控制器（含静态资源放行）
├── .htaccess                      # Apache URL 重写
├── config/
│   └── config.php                 # 数据库与站点配置
├── app/
│   ├── bootstrap.php              # DB 连接 + 全局辅助函数(e/url/asset/format_count…)
│   ├── Router.php                 # 极简路由器（{param} 命名捕获）
│   ├── View.php                   # 模板渲染（layout + partial）
│   ├── Controllers.php            # 所有页面控制器（静态方法）
│   ├── repositories/
│   │   └── Repositories.php       # 数据访问层（Category/Alternative/Tool Repo + SearchService）
│   └── views/
│       ├── layout.php             # 全站布局（头部/导航/页脚/主题脚本）
│       ├── home.php               # 首页
│       ├── detail.php             # 商业软件详情页
│       ├── category.php           # 分类页
│       ├── search.php             # 搜索页
│       ├── tool.php               # 开源工具详情页
│       ├── about.php              # 关于页
│       ├── 404.php
│       └── partials/
│           ├── software_card.php  # 商业软件卡片
│           └── tool_card.php      # 开源工具卡片
├── assets/
│   ├── css/style.css              # 全部样式（CSS 变量主题系统）
│   └── js/app.js                  # 主题切换/菜单/快捷键/索引高亮
├── database/
│   ├── schema.sql                 # 建表 DDL
│   └── seed.sql                   # 种子数据
├── .trae/documents/               # PRD 与技术架构文档
└── ai-read.md                     # 本文件
```

## 5. 数据模型

4 张表，关系为「分类 1—N 商业软件 N—N 开源工具」：

- `categories(id, name, slug, description, icon)`
- `alternatives(id, name, slug, tagline, description, website, logo_url, color, category_id, created_at)` — **被替代的商业软件**
- `tools(id, name, slug, description, website, repo_url, stars, forks, license, language, logo_url, color, featured, created_at)` — **开源工具**
- `alternative_tool(alternative_id, tool_id)` — 多对多映射表

**种子数据量**：15 分类 / 54 商业软件 / 66 开源工具 / 128 映射。

> 重新建库：`php import.php`（如需重新生成，参考下方脚本）。
> 或手动：`database/schema.sql` + `database/seed.sql`，注意先 `SET FOREIGN_KEY_CHECKS=0`。

## 6. 路由表

| 路由 | 控制器方法 | 说明 |
|------|-----------|------|
| `GET /` | `Controllers::home` | 首页 |
| `GET /{slug}` | `Controllers::detail` | 商业软件详情（兜底路由，放最后） |
| `GET /category/{slug}` | `Controllers::category` | 分类页 |
| `GET /tool/{slug}` | `Controllers::tool` | 开源工具详情 |
| `GET /search?q=` | `Controllers::search` | 搜索 |
| `GET /about` | `Controllers::about` | 关于 |
| 其他 | `Controllers::notFound` | 404 |

> 注意：`/{slug}` 是兜底路由，必须注册在 `/about`、`/search`、`/category/*`、`/tool/*` **之后**，否则会误匹配。

## 7. 运行方式

**预览（PHP 内置服务器）：**
```bash
php -S localhost:8237 index.php
# 访问 http://localhost:8237/
```

**正式（Apache + 已配 hosts）：**
直接访问 `http://daohang.com`，`.htaccess` 已处理伪静态。

## 8. 设计规范

- **主色**：暖橙红 `#f97316`（深色模式 `#fb923c`），基底暖灰/深石板灰
- **字体**：标题 Sora / 正文 Plus Jakarta Sans / 数字 JetBrains Mono
- **主题**：CSS 变量 + `[data-theme]`，JS 记忆 localStorage，防闪烁脚本在 `<head>`
- **布局**：粘性顶栏 + 卡片网格，圆角 14px，柔和投影，卡片悬浮抬升
- **图标**：全部内联 SVG（Star/Git/External/Search/Sun/Moon）
- **背景**：固定网格纹理 + Hero 区橙色光晕

## 9. 关键约定（改代码前必读）

1. **单入口**：所有请求经 `index.php`，新增路由在 [index.php](index.php) 注册。
2. **数据访问统一走 Repository**：`app/repositories/Repositories.php`，控制器不直接写 SQL。
3. **模板用 `View::render(view, data, title)`**：自动套 layout；局部用 `View::partial()`。
4. **转义**：输出用户/DB 内容一律用 `e()`，URL 用 `url()`/`asset()`。
5. **PHP 内置服务器下静态资源**：`index.php` 顶部有 `return false` 放行真实文件，别删。
6. **无构建**：CSS/JS 直接改文件即生效，无需编译。

## 10. 常见任务指引

- **加一款商业软件**：往 `alternatives` 插入，再在 `alternative_tool` 关联已有 tool。
- **加一个开源工具**：往 `tools` 插入，再关联 alternatives。
- **加分类**：往 `categories` 插入。
- **加页面**：① `Controllers.php` 加静态方法 → ② `index.php` 注册路由 → ③ `app/views/` 加模板。
- **改配色**：改 `assets/css/style.css` 顶部 `:root` 与 `[data-theme="dark"]` 的变量。

## 11. 验证清单（回归测试参考）

最小冒烟测试：用 PHP 脚本 curl 以下路由，断言 HTTP 状态与关键内容存在。
- `/`（200，含品牌名/字母索引/卡片）
- `/notion`（200，含「的开源替代」+ AppFlowy）
- `/category/design`（200）
- `/tool/penpot`（200，含「可以替代」+ Figma）
- `/search?q=notes`（200）
- `/about`（200）
- `/nope`（404）
- `/assets/css/style.css`、`/assets/js/app.js`（200，静态放行生效）

## 12. 已知限制 / 待办

- Logo 用首字母色块占位，未接入真实图标。
- Star/forks 为静态种子数，未实时拉取 GitHub API。
- 无后台管理界面（数据靠 SQL 维护）。
- 搜索为 SQL LIKE，未做分词/高亮。
