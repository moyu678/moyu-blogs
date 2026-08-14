<?php
session_start();
require_once __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
case 'wish_list':
    $pg = max(1, intval($_GET['page'] ?? 1)); $per = 20; $off = ($pg - 1) * $per;
    $w = get_wishes($per, $off); $t = count_wishes();
    echo json_encode(['success' => true, 'data' => ['wishes' => $w, 'total' => $t, 'pages' => max(1, ceil($t / $per)), 'current' => $pg]], JSON_UNESCAPED_UNICODE);
    break;

case 'wish_post':
    if (!intval(setting('wish_enabled', '1'))) { echo json_encode(['success' => false, 'error' => '留言暂未开放'], JSON_UNESCAPED_UNICODE); exit; }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success' => false, 'error' => '无效请求'], JSON_UNESCAPED_UNICODE); exit; }
    $nick = trim($_POST['nickname'] ?? ''); $content = trim($_POST['content'] ?? ''); $type = ($_POST['wish_type'] ?? '') === 'wish' ? 'wish' : 'message';
    if (mb_strlen($nick) < 1 || mb_strlen($nick) > 20) { echo json_encode(['success' => false, 'error' => '昵称 1-20 字'], JSON_UNESCAPED_UNICODE); exit; }
    if (mb_strlen($content) < 1 || mb_strlen($content) > 500) { echo json_encode(['success' => false, 'error' => '内容 1-500 字'], JSON_UNESCAPED_UNICODE); exit; }
    db()->prepare("INSERT INTO wishes (nickname,content,wish_type,avatar_color) VALUES (?,?,?,?)")->execute([$nick, $content, $type, avatar_color($nick)]);
    echo json_encode(['success' => true, 'data' => '发表成功'], JSON_UNESCAPED_UNICODE);
    break;

default:
    echo json_encode(['success' => false, 'error' => '未知操作'], JSON_UNESCAPED_UNICODE);
}