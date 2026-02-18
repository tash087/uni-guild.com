<?php
session_start();

$password = "news"; // 管理パスワード
$news_dir = "../news/"; // ニュースファイルが保存されているディレクトリ

// --- ログアウト処理 ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: edit.php");
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

// ログインチェック
if (!isset($_SESSION['news_auth'])) {
    $show_login = true;
} else {
    $show_login = false;
    
    $file = $_GET['file'] ?? '';
    $target_path = $file ? realpath($news_dir . $file) : '';

    // セキュリティチェック：newsディレクトリ外のファイル操作を禁止
    if ($file && (strpos($target_path, realpath($news_dir)) !== 0 || !file_exists($target_path))) {
        die("不正なファイルパスです。");
    }

    // 保存処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
        file_put_contents($target_path, $_POST['content']);
        $success_msg = "✅ ファイル「" . htmlspecialchars($file) . "」を保存しました！";
    }

    // ファイル読み込み
    $current_content = ($file && file_exists($target_path)) ? file_get_contents($target_path) : '';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ニュース編集パネル</title>
    <style>
        /* post.php と共通のデザイン */
        body { font-family: sans-serif; max-width: 800px; margin: 50px auto; line-height: 1.6; background: #f4f7f6; padding: 20px; }
        .panel { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1, h2 { font-size: 1.5rem; margin-bottom: 20px; text-align: center; color: #333; }
        h3 { font-size: 1.1rem; color: #666; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        
        input[type="password"], textarea { width: 100%; margin-bottom: 15px; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 5px; }
        textarea { height: 450px; font-family: monospace; font-size: 14px; background: #f9f9f9; }
        
        button, .btn-link { width: 100%; background: #e67e22; color: white; border: none; padding: 12px; cursor: pointer; border-radius: 5px; font-weight: bold; font-size: 1rem; text-align: center; display: inline-block; text-decoration: none; }
        button:hover { background: #d35400; }
        .btn-cancel { background: #95a5a6; margin-top: 10px; }
        .btn-cancel:hover { background: #7f8c8d; }

        .msg { padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .error { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }
        
        .file-list-box { margin-bottom: 30px; max-height: 200px; overflow-y: auto; background: #f8f9fa; border: 1px solid #eee; padding: 10px; border-radius: 8px; }
        .file-list-box ul { list-style: none; padding: 0; margin: 0; }
        .file-list-box li { margin-bottom: 5px; }
        .file-list-box a { color: #2980b9; text-decoration: none; font-size: 0.95rem; }
        .file-list-box a:hover { text-decoration: underline; }

        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .nav-links a { font-size: 0.85rem; color: #e67e22; text-decoration: none; margin-left: 15px; font-weight: bold; }
        .nav-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="panel">
    <?php if ($show_login): ?>
        <h1>🔐 編集パネル ログイン</h1>
        <?php if (isset($error)) echo "<div class='msg error'>$error</div>"; ?>
        <form method="POST">
            <input type="password" name="login_pass" placeholder="パスワード" required autofocus>
            <button type="submit">ログイン</button>
        </form>

    <?php else: ?>
        <div class="top-nav">
            <div class="nav-links">
                <a href="post.php">← 新規作成へ戻る</a>
            </div>
            <div class="nav-links">
                <a href="?logout=1" style="color:#999;">ログアウト</a>
            </div>
        </div>

        <h2>📰 ニュース編集パネル</h2>
        
        <?php if (isset($success_msg)) echo "<div class='msg success'>$success_msg</div>"; ?>

        <div class="file-list-box">
            <h3>編集するファイルを選択</h3>
            <ul>
                <?php
                $files = glob($news_dir . "*.html");
                if ($files) {
                    // 日付（タイムスタンプ）が新しい順に並び替え
                    array_multisort(array_map('filemtime', $files), SORT_DESC, $files);
                    foreach ($files as $f) {
                        $name = basename($f);
                        $selected = ($file === $name) ? 'font-weight:bold; color:#d35400;' : '';
                        echo "<li><a href='?file=$name' style='$selected'>📄 $name</a></li>";
                    }
                } else {
                    echo "<li>ファイルが見つかりません</li>";
                }
                ?>
            </ul>
        </div>

        <?php if ($file): ?>
            <form method="POST">
                <h3>編集中のファイル: <span style="color:#e67e22;"><?php echo htmlspecialchars($file); ?></span></h3>
                <textarea name="content"><?php echo htmlspecialchars($current_content); ?></textarea>
                <button type="submit">内容を保存する</button>
                <a href="edit.php" class="btn-link btn-cancel">編集をキャンセル</a>
            </form>
        <?php else: ?>
            <p style="text-align:center; color:#999;">上のリストからファイルを選択してください。</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>