<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/upload_service.php';

function view_handle_post($dbconn, int $userId): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return;
    }

    switch ($_POST['action']) {
        case 'add_comment':
            $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
            $text = trim($_POST['comment_text'] ?? '');
            if (!$postId || $text === '') {
                redirect_with_message('view.php', error: 'コメント内容を入力してください。');
            }

            $ok = pg_query_params(
                $dbconn,
                'INSERT INTO comments (post_id, user_id, comment_text) VALUES ($1, $2, $3)',
                [$postId, $userId, $text]
            );
            redirect_with_message('view.php', $ok ? 'コメントを投稿しました。' : null, $ok ? null : 'コメントの投稿に失敗しました。');

        case 'edit_comment':
            $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
            $text = trim($_POST['new_comment_text'] ?? '');
            if (!$commentId || $text === '') {
                redirect_with_message('view.php', error: 'コメント内容を入力してください。');
            }

            $ok = pg_query_params(
                $dbconn,
                'UPDATE comments SET comment_text = $1 WHERE id = $2 AND user_id = $3',
                [$text, $commentId, $userId]
            );
            redirect_with_message('view.php', $ok ? 'コメントを更新しました。' : null, $ok ? null : 'コメントの更新に失敗しました。');

        case 'delete_comment':
            $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
            if (!$commentId) {
                redirect_with_message('view.php', error: 'コメント ID が無効です。');
            }

            $ok = pg_query_params(
                $dbconn,
                'DELETE FROM comments WHERE id = $1 AND user_id = $2',
                [$commentId, $userId]
            );
            redirect_with_message('view.php', $ok ? 'コメントを削除しました。' : null, $ok ? null : 'コメントの削除に失敗しました。');

        case 'update_post':
            $postId = filter_input(INPUT_POST, 'edit_id', FILTER_VALIDATE_INT);
            if (!$postId) {
                redirect_with_message('view.php', error: '投稿 ID が無効です。');
            }

            $memo = trim($_POST['memo'] ?? '');
            $lat = filter_var(trim($_POST['lat'] ?? ''), FILTER_VALIDATE_FLOAT);
            $lng = filter_var(trim($_POST['lng'] ?? ''), FILTER_VALIDATE_FLOAT);
            $isPublic = isset($_POST['is_public']) ? 't' : 'f';

            if ($lat === false || $lng === false || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                redirect_with_message('view.php?edit_id=' . $postId, error: '位置情報が不正です。');
            }

            $params = [$memo, $lat, $lng, $isPublic];
            $sql = 'UPDATE location_diary SET memo = $1, lat = $2, lng = $3, is_public = $4';
            $index = 4;

            if (isset($_FILES['new_file_data']) && ($_FILES['new_file_data']['size'] ?? 0) > 0) {
                $upload = upload_store_image($_FILES['new_file_data']);
                if (!($upload['ok'] ?? false)) {
                    redirect_with_message('view.php?edit_id=' . $postId, error: $upload['error'] ?? '画像の保存に失敗しました。');
                }
                upload_delete_file(normalize_upload_filename($_POST['old_image_filename'] ?? null));
                $sql .= ', filename = $' . (++$index);
                $params[] = $upload['filename'];
            }

            $sql .= ' WHERE id = $' . (++$index) . ' AND user_id = $' . (++$index);
            $params[] = $postId;
            $params[] = $userId;

            $ok = pg_query_params($dbconn, $sql, $params);
            redirect_with_message('view.php', $ok ? '投稿を更新しました。' : null, $ok ? null : '投稿の更新に失敗しました。');
    }
}

function view_delete_post($dbconn, int $userId, int $postId): void
{
    $res = pg_query_params(
        $dbconn,
        'SELECT filename FROM location_diary WHERE id = $1 AND user_id = $2',
        [$postId, $userId]
    );

    if (!$res || !($row = pg_fetch_assoc($res))) {
        redirect_with_message('view.php', error: '投稿が見つからないか、削除権限がありません。');
    }

    $deleted = pg_query_params(
        $dbconn,
        'DELETE FROM location_diary WHERE id = $1 AND user_id = $2',
        [$postId, $userId]
    );

    if ($deleted) {
        upload_delete_file($row['filename'] ?? null);
        redirect_with_message('view.php', '投稿を削除しました。');
    }

    redirect_with_message('view.php', error: '投稿の削除に失敗しました。');
}

function view_load_posts($dbconn, int $userId, string $mode, string $search, int $page, int $perPage): array
{
    $params = [];
    $where = [];
    $index = 1;

    if ($mode === 'my_posts') {
        $where[] = 'ld.user_id = $' . $index++;
        $params[] = $userId;
    } else {
        $where[] = 'ld.is_public = TRUE';
    }

    if ($search !== '') {
        $where[] = '(ld.memo ILIKE $' . $index . ' OR ld.lat::text ILIKE $' . ($index + 1) . ' OR ld.lng::text ILIKE $' . ($index + 2) . ')';
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
        $index += 3;
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $countRes = pg_query_params($dbconn, 'SELECT COUNT(*) FROM location_diary ld ' . $whereSql, $params);
    $total = (int) pg_fetch_result($countRes, 0, 0);
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = min(max(1, $page), $totalPages);
    $offset = ($page - 1) * $perPage;

    $params[] = $perPage;
    $params[] = $offset;
    $sql = '
        SELECT ld.id, ld.lat, ld.lng, ld.memo, ld.filename, ld.created_at, ld.is_public,
               u.username AS author_username, u.id AS author_id
        FROM location_diary ld
        JOIN users u ON ld.user_id = u.id
        ' . $whereSql . '
        ORDER BY ld.created_at DESC
        LIMIT $' . $index++ . ' OFFSET $' . $index;

    $result = pg_query_params($dbconn, $sql, $params);

    return [
        'posts' => pg_fetch_all($result) ?: [],
        'total_pages' => $totalPages,
        'current_page' => $page,
    ];
}

function view_load_post_for_edit($dbconn, int $userId, int $postId): ?array
{
    if ($postId <= 0) {
        return null;
    }

    $res = pg_query_params(
        $dbconn,
        'SELECT memo, filename, lat, lng, is_public FROM location_diary WHERE id = $1 AND user_id = $2',
        [$postId, $userId]
    );

    return $res ? pg_fetch_assoc($res) ?: null : null;
}

function view_load_comments($dbconn, int $postId): array
{
    $res = pg_query_params(
        $dbconn,
        'SELECT c.id, c.comment_text, c.created_at, u.username AS comment_author_username, u.id AS comment_author_id
         FROM comments c
         JOIN users u ON c.user_id = u.id
         WHERE c.post_id = $1
         ORDER BY c.created_at ASC',
        [$postId]
    );

    return pg_fetch_all($res) ?: [];
}
