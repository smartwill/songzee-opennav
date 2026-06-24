<?php
// 数据访问层：集中所有 SQL 查询
declare(strict_types=1);

/** 分类相关查询 */
class CategoryRepository
{
    public static function all(): array
    {
        return db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
    }

    public static function withCounts(): array
    {
        $sql = 'SELECT c.*, COUNT(a.id) AS software_count
                FROM categories c
                LEFT JOIN alternatives a ON a.category_id = c.id
                GROUP BY c.id
                ORDER BY software_count DESC, c.name';
        return db()->query($sql)->fetchAll();
    }

    public static function bySlug(string $slug): ?array
    {
        $stmt = db()->prepare('SELECT * FROM categories WHERE slug = ?');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}

/** 商业软件（被替代对象）查询 */
class AlternativeRepository
{
    /** 按首字母分组返回全部软件（含替代数量） */
    public static function groupedAlpha(): array
    {
        $sql = 'SELECT a.*, c.slug AS category_slug, c.name AS category_name,
                       (SELECT COUNT(*) FROM alternative_tool at WHERE at.alternative_id = a.id) AS alt_count
                FROM alternatives a
                LEFT JOIN categories c ON c.id = a.category_id
                ORDER BY a.name';
        $rows = db()->query($sql)->fetchAll();
        $grouped = [];
        foreach ($rows as $r) {
            $letter = mb_strtoupper(mb_substr($r['name'], 0, 1));
            if (preg_match('/^[0-9]/', $r['name'])) $letter = '#';
            $grouped[$letter][] = $r;
        }
        ksort($grouped);
        return $grouped;
    }

    public static function all(): array
    {
        return db()->query('SELECT * FROM alternatives ORDER BY name')->fetchAll();
    }

    public static function count(): int
    {
        return (int)db()->query('SELECT COUNT(*) FROM alternatives')->fetchColumn();
    }

    public static function bySlug(string $slug): ?array
    {
        $stmt = db()->prepare('SELECT a.*, c.slug AS category_slug, c.name AS category_name
                               FROM alternatives a
                               LEFT JOIN categories c ON c.id = a.category_id
                               WHERE a.slug = ?');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** 获取某商业软件的所有开源替代工具 */
    public static function tools(int $alternativeId): array
    {
        $sql = 'SELECT t.* FROM tools t
                JOIN alternative_tool at ON at.tool_id = t.id
                WHERE at.alternative_id = ?
                ORDER BY t.featured DESC, t.stars DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute([$alternativeId]);
        return $stmt->fetchAll();
    }

    /** 同分类下的相关软件 */
    public static function related(int $categoryId, int $excludeId, int $limit = 6): array
    {
        $sql = 'SELECT a.*, (SELECT COUNT(*) FROM alternative_tool at WHERE at.alternative_id = a.id) AS alt_count
                FROM alternatives a
                WHERE a.category_id = ? AND a.id <> ?
                ORDER BY alt_count DESC, a.name
                LIMIT ' . (int)$limit;
        $stmt = db()->prepare($sql);
        $stmt->execute([$categoryId, $excludeId]);
        return $stmt->fetchAll();
    }

    public static function byCategory(int $categoryId): array
    {
        $sql = 'SELECT a.*, (SELECT COUNT(*) FROM alternative_tool at WHERE at.alternative_id = a.id) AS alt_count
                FROM alternatives a
                WHERE a.category_id = ?
                ORDER BY a.name';
        $stmt = db()->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }
}

/** 开源工具查询 */
class ToolRepository
{
    public static function count(): int
    {
        return (int)db()->query('SELECT COUNT(*) FROM tools')->fetchColumn();
    }

    public static function featured(int $limit = 8): array
    {
        $sql = 'SELECT * FROM tools WHERE featured = 1 ORDER BY stars DESC LIMIT ' . (int)$limit;
        return db()->query($sql)->fetchAll();
    }

    public static function bySlug(string $slug): ?array
    {
        $stmt = db()->prepare('SELECT * FROM tools WHERE slug = ?');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** 该工具可替代的商业软件列表 */
    public static function alternatives(int $toolId): array
    {
        $sql = 'SELECT a.*, c.slug AS category_slug
                FROM alternatives a
                JOIN alternative_tool at ON at.alternative_id = a.id
                LEFT JOIN categories c ON c.id = a.category_id
                WHERE at.tool_id = ?
                ORDER BY a.name';
        $stmt = db()->prepare($sql);
        $stmt->execute([$toolId]);
        return $stmt->fetchAll();
    }

    /** 搜索 */
    public static function search(string $q): array
    {
        $q = '%' . $q . '%';
        $stmt = db()->prepare('SELECT * FROM tools WHERE name LIKE ? OR description LIKE ? ORDER BY stars DESC LIMIT 20');
        $stmt->execute([$q, $q]);
        return $stmt->fetchAll();
    }
}

/** 跨实体搜索 */
class SearchService
{
    public static function query(string $q): array
    {
        $like = '%' . $q . '%';
        $alts = db()->prepare('SELECT a.*, c.slug AS category_slug,
                                      (SELECT COUNT(*) FROM alternative_tool at WHERE at.alternative_id = a.id) AS alt_count
                               FROM alternatives a
                               LEFT JOIN categories c ON c.id = a.category_id
                               WHERE a.name LIKE ? OR a.tagline LIKE ? OR a.description LIKE ?
                               ORDER BY alt_count DESC, a.name');
        $alts->execute([$like, $like, $like]);

        $tools = db()->prepare('SELECT * FROM tools WHERE name LIKE ? OR description LIKE ? ORDER BY stars DESC LIMIT 30');
        $tools->execute([$like, $like]);

        $cats = db()->prepare('SELECT * FROM categories WHERE name LIKE ? OR description LIKE ? ORDER BY name');
        $cats->execute([$like, $like]);

        return [
            'alternatives' => $alts->fetchAll(),
            'tools'        => $tools->fetchAll(),
            'categories'   => $cats->fetchAll(),
        ];
    }
}
