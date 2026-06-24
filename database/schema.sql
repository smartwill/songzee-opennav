-- 开源软件替代品目录 - 数据库结构 (MySQL 5.7+)
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS alternative_tool;
DROP TABLE IF EXISTS tools;
DROP TABLE IF EXISTS alternatives;
DROP TABLE IF EXISTS categories;

-- 分类
CREATE TABLE categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  icon VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cat_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 商业/闭源软件（被替代的对象）
CREATE TABLE alternatives (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL,
  tagline VARCHAR(255) DEFAULT NULL,
  description TEXT,
  website VARCHAR(255) DEFAULT NULL,
  logo_url VARCHAR(255) DEFAULT NULL,
  color VARCHAR(20) DEFAULT NULL,
  category_id INT UNSIGNED DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_alt_slug (slug),
  KEY idx_alt_cat (category_id),
  CONSTRAINT fk_alt_cat FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 开源工具
CREATE TABLE tools (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL,
  description TEXT,
  website VARCHAR(255) DEFAULT NULL,
  repo_url VARCHAR(255) DEFAULT NULL,
  stars INT UNSIGNED DEFAULT 0,
  forks INT UNSIGNED DEFAULT 0,
  license VARCHAR(60) DEFAULT NULL,
  language VARCHAR(60) DEFAULT NULL,
  logo_url VARCHAR(255) DEFAULT NULL,
  color VARCHAR(20) DEFAULT NULL,
  featured TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_tool_slug (slug),
  KEY idx_tool_stars (stars)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 商业软件 <-> 开源工具 多对多
CREATE TABLE alternative_tool (
  alternative_id INT UNSIGNED NOT NULL,
  tool_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (alternative_id, tool_id),
  KEY idx_at_tool (tool_id),
  CONSTRAINT fk_at_alt FOREIGN KEY (alternative_id) REFERENCES alternatives(id) ON DELETE CASCADE,
  CONSTRAINT fk_at_tool FOREIGN KEY (tool_id) REFERENCES tools(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
