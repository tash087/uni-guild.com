<?php
include '../news/template.php';

$password = "news"; // あなた専用のパスワード
$json_file = 'news.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['pass'] === $password) {
    // 1. データの準備
    $id = time(); // ユニークなIDとしてタイムスタンプを使用
    $title = htmlspecialchars($_POST['title']);
    $date = date("Y.m.d");
    $category = htmlspecialchars($_POST['category']);
    $author = htmlspecialchars($_POST['author']);
    $content = $_POST['content']; // 本文
    $file_path = "../news/post_{$id}.html";

    // 2. 詳細ページ(HTML)を物理的に作成
    $html_content = generate_news_html($title, $date, $category, $author, $content);
    file_put_contents($file_path, $html_content);

    // 3. JSONにデータを追加（トップページ表示用）
    $current_data = json_decode(file_get_contents($json_file), true) ?: [];
    array_unshift($current_data, [
        "date" => $date,
        "category" => $category,
        "title" => $title,
        "url" => $file_path // 生成したファイルへのリンク
    ]);
    file_put_contents($json_file, json_encode($current_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    echo "<p style='color:green;'>成功！記事が生成されました： <a href='{$file_path}'>確認する</a></p>";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ニュース作成ツール</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 20px auto; line-height: 1.6; }
        input, textarea, select { width: 100%; margin-bottom: 15px; padding: 8px; box-sizing: border-box; }
        button { background: #007bff; color: #white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>📰 ニュース投稿パネル</h1>
    <form method="POST">
        <label>管理パスワード:</label>
        <input type="password" name="pass" required>
        
        <label>タイトル:</label>
        <input type="text" name="title" required placeholder="例：サーバーをVer1.21に更新しました">
        
        <label>カテゴリ:</label>
        <select name="category">
            <option>お知らせ</option>
            <option>アップデート</option>
            <option>イベント</option>
            <option>重要</option>
        </select>

        <label>投稿者名:</label>
        <input type="text" name="author" value="tash087">

        <label>本文 (HTMLタグも使えます):</label>
        <textarea name="content" rows="10" required placeholder="ここにニュースの詳細を書いてください"></textarea>
        
        <button type="submit">記事を生成して公開</button>
    </form>
</body>
</html>