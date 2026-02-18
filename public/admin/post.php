<?php
session_start();
include '../news/template.php';

$password = "news"; // 管理パスワード
$json_file = 'news.json';
// 旧名称:news.php


// --- ログアウト処理 ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: post.php");
    exit;
}

// --- 1. パスワード認証処理 ---
if (isset($_POST['login_pass'])) {
    if ($_POST['login_pass'] === $password) {
        $_SESSION['news_auth'] = true;
    } else {
        $error = "パスワードが違います。";
    }
}

// --- 2. 記事投稿処理 (ログイン済みの場合のみ) ---
if (isset($_SESSION['news_auth']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $id = time(); 
    $title = htmlspecialchars($_POST['title']);
    $date = date("Y.m.d");
    $category = htmlspecialchars($_POST['category']);
    $author = htmlspecialchars($_POST['author']);
    $content = $_POST['content']; 
    $file_path = "../news/post_{$id}.html";

    // 詳細ページ作成
    $html_content = generate_news_html($title, $date, $category, $author, $content);
    file_put_contents($file_path, $html_content);

    // JSON更新
    $current_data = json_decode(file_get_contents($json_file), true) ?: [];
    array_unshift($current_data, [
        "date" => $date,
        "category" => $category,
        "title" => $title,
        "url" => $file_path
    ]);
    file_put_contents($json_file, json_encode($current_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    $success_msg = "成功！記事が公開されました： <a href='{$file_path}' target='_blank'>確認する</a>";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ニュース管理パネル</title>
    <style>
        body { font-family: sans-serif; max-width: 700px; margin: 50px auto; line-height: 1.6; background: #f4f7f6; padding: 20px; }
        .panel { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { font-size: 1.5rem; margin-bottom: 20px; text-align: center; }
        input, textarea, select { width: 100%; margin-bottom: 15px; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; background: #e67e22; color: white; border: none; padding: 12px; cursor: pointer; border-radius: 5px; font-weight: bold; font-size: 1rem; }
        button:hover { background: #d35400; }
        .msg { padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .error { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }

        /* ナビゲーション周りのスタイル */
        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .nav-links a { font-size: 0.85rem; color: #e67e22; text-decoration: none; font-weight: bold; }
        .nav-links a:hover { text-decoration: underline; }
        .logout-link { font-size: 0.8rem; color: #999; text-decoration: none; }
    </style>
</head>
<body>

<div class="panel">
    <?php if (!isset($_SESSION['news_auth'])): ?>
        <h1>🔐 管理者ログイン</h1>
        <?php if (isset($error)) echo "<div class='msg error'>$error</div>"; ?>
        <form method="POST">
            <label>パスワードを入力してください:</label>
            <input type="password" name="login_pass" required autofocus>
            <button type="submit">ログイン</button>
        </form>

    <?php else: ?>
        <div class="top-nav">
            <div class="nav-links">
                <a href="edit.php">過去記事の編集へ →</a>
            </div>
            <a href="?logout=1" class="logout-link">ログアウト</a>
        </div>

        <h1>📰 ニュース作成パネル</h1>
        
        <?php if (isset($success_msg)) echo "<div class='msg success'>$success_msg</div>"; ?>

        <form method="POST">
            <label>タイトル:</label>
            <input type="text" name="title" required placeholder="例：イベント開催のお知らせ">
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>カテゴリ:</label>
                    <select name="category">
                        <option>お知らせ</option>
                        <option>アップデート</option>
                        <option>イベント</option>
                        <option>重要</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label>投稿者名:</label>
                    <input type="text" name="author" value="tash087">
                </div>
            </div>

            <label>本文 (HTML/スライドショー用タグ使用可):</label>
            <textarea name="content" rows="12" required placeholder="詳細を入力してください..."></textarea>
            
            <button type="submit">記事を生成して公開</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>