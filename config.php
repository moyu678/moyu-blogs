<?php
session_start();
date_default_timezone_set('Asia/Shanghai');

define('DB_HOST', 'localhost');
define('DB_NAME', 'moyu_blog');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('BLOG_NAME', '摸鱼博客');
define('BLOG_TAGLINE', '在忙碌的间隙，记录生活的温度');
define('BASE_PATH', __DIR__);
define('UPLOAD_DIR', BASE_PATH . '/uploads');
define('UPLOAD_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/uploads');

if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            if (basename($_SERVER['SCRIPT_NAME']) !== 'install.php') { header('Location: install.php'); exit; }
            return null;
        }
    }
    return $pdo;
}

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function slug_en(string $s): string { $s = strtolower(preg_replace('/[^a-z0-9]/', '-', trim($s))); return trim(preg_replace('/-+/', '-', $s), '-') ?: 'post-' . time(); }
function truncate(string $s, int $l = 120): string { $s = strip_tags($s); return mb_strlen($s) <= $l ? $s : mb_substr($s, 0, $l) . '…'; }
function time_ago(string $dt): string { $d = time() - strtotime($dt); if ($d < 60) return '刚刚'; if ($d < 3600) return floor($d / 60) . ' 分钟前'; if ($d < 86400) return floor($d / 3600) . ' 小时前'; if ($d < 2592000) return floor($d / 86400) . ' 天前'; return date('Y-m-d', strtotime($dt)); }

function setting(string $k, string $d = ''): string {
    static $c = [];
    if (isset($c[$k])) return $c[$k];
    try { $s = db()->prepare("SELECT setting_value FROM settings WHERE setting_key=?"); $s->execute([$k]); $r = $s->fetch(); $c[$k] = $r ? $r['setting_value'] : $d; } catch (Exception $e) { $c[$k] = $d; }
    return $c[$k];
}

function set_setting(string $k, string $v): void {
    db()->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)")->execute([$k, $v]);
}

function is_admin(): bool { return !empty($_SESSION['frost_admin']); }
function require_admin(): void { if (!is_admin()) { header('Location: admin.php?action=login'); exit; } }
function redirect(string $u): void { header("Location: $u"); exit; }
function flash(string $k): ?string { $v = $_SESSION['flash'][$k] ?? null; unset($_SESSION['flash'][$k]); return $v; }
function set_flash(string $k, string $m): void { $_SESSION['flash'][$k] = $m; }

function upload_image(array $f): ?string {
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 10 * 1024 * 1024) return null;
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif','webp','svg'])) return null;
    $n = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    return move_uploaded_file($f['tmp_name'], UPLOAD_DIR . '/' . $n) ? $n : null;
}

function get_posts(string $t = 'all', string $s = 'published', ?int $cid = null, int $lim = 20, int $off = 0): array {
    $sql = "SELECT p.*,c.name AS cat_name,c.slug AS cat_slug,c.color AS cat_color FROM posts p LEFT JOIN categories c ON p.category_id=c.id WHERE p.status=?";
    $p = [$s];
    if ($t !== 'all') { $sql .= " AND p.type=?"; $p[] = $t; }
    if ($cid !== null) { $sql .= " AND p.category_id=?"; $p[] = $cid; }
    $sql .= " ORDER BY p.is_featured DESC, p.published_at DESC LIMIT $lim OFFSET $off";
    $st = db()->prepare($sql); $st->execute($p); return $st->fetchAll();
}

function get_post(string $slug): ?array {
    $st = db()->prepare("SELECT p.*,c.name AS cat_name,c.slug AS cat_slug,c.color AS cat_color FROM posts p LEFT JOIN categories c ON p.category_id=c.id WHERE p.slug=?");
    $st->execute([$slug]); $r = $st->fetch(); return $r ?: null;
}

function count_posts(string $s = 'published', ?int $cid = null): int {
    $sql = "SELECT COUNT(*) FROM posts WHERE status=?"; $p = [$s];
    if ($cid !== null) { $sql .= " AND category_id=?"; $p[] = $cid; }
    $st = db()->prepare($sql); $st->execute($p); return (int)$st->fetchColumn();
}

function get_categories(): array {
    return db()->query("SELECT c.*,(SELECT COUNT(*) FROM posts WHERE category_id=c.id AND status='published') AS post_count FROM categories c ORDER BY sort_order ASC,id ASC")->fetchAll();
}

function get_total_views(): int { try { return (int)db()->query("SELECT COALESCE(SUM(view_count),0) FROM posts")->fetchColumn(); } catch (Exception $e) { return 0; } }

function url(string $p = ''): string {
    static $b = null;
    if ($b === null) { $d = dirname($_SERVER['SCRIPT_NAME']); $b = ($d === '/' || $d === '\\') ? '' : rtrim($d, '/\\'); }
    return $b . ($p === '' || $p === '/' ? '/' : '/' . ltrim($p, '/'));
}

function get_wishes(int $lim = 20, int $off = 0): array {
    try { $st = db()->prepare("SELECT * FROM wishes WHERE status='visible' ORDER BY created_at DESC LIMIT $lim OFFSET $off"); $st->execute(); return $st->fetchAll(); } catch (Exception $e) { return []; }
}

function count_wishes(): int {
    try { return (int)db()->query("SELECT COUNT(*) FROM wishes WHERE status='visible'")->fetchColumn(); } catch (Exception $e) { return 0; }
}

function avatar_color(string $n): string {
    $c = ['#3b6ef5','#059669','#d97706','#8b5cf6','#ec4899','#dc2626','#0891b2','#7c3aed','#0d9488','#ea580c'];
    return $c[crc32($n) % count($c)];
}