<?php
// 自動生成されるHTMLのベース
function generate_news_html($title, $date, $category, $author, $content) {
    ob_start(); // バッファリング開始
    ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - 箱庭クラフト！</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../images/server_icon.png" type="image/png">
    <script src="../js/include.js" defer></script>
    <style>
        /* ニュース詳細ページの専用レイアウト修正 */
        .news-detail-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 40px 20px;
            border-radius: 15px;
        }

        .news-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            /* 高さを固定せず、中身に合わせて広がるように設定 */
            height: auto !important; 
            min-height: auto !important;
        }

        .news-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.95rem;
            color: #888;
        }

        .news-category-badge {
            background: #e67e22; /* 添付画像のオレンジ色に統一 */
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
        }

        /* タイトル崩れの修正 */
        .news-title {
            font-size: clamp(1.5rem, 5vw, 2.2rem); /* スマホとPCでサイズ自動調整 */
            line-height: 1.4 !important; /* 行間を広げて重なりを防止 */
            color: #333;
            margin: 10px 0;
            word-wrap: break-word; /* 長い単語を強制改行 */
            overflow: visible !important; /* はみ出し防止 */
        }

        .news-author-info {
            margin-top: 15px;
            color: #666;
            font-size: 0.9rem;
        }

        .news-content {
            line-height: 1.8;
            font-size: 1.05rem;
            color: #444;
        }

        /* 本文内の箇条書きを整える */
        .news-content ul {
            padding-left: 1.5rem;
            margin: 1.5rem 0;
        }
        .news-content li {
            margin-bottom: 0.8rem;
        }

        .news-footer {
            margin-top: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div id="header-placeholder"></div>
    
    <main class="container section-padding">
        <article class="news-detail-container">
            <header class="news-header">
                <div class="news-meta">
                    <span class="news-date"><?php echo $date; ?></span>
                    <span class="news-category-badge"><?php echo $category; ?></span>
                </div>
                
                <h1 class="news-title"><?php echo $title; ?></h1>
                
                <div class="news-author-info">
                    Posted by <?php echo $author; ?>
                </div>
            </header>

            <section class="news-content">
                <?php echo $content; // 既にHTMLタグが含まれているためnl2brは外して出力 ?>
            </section>

            <footer class="news-footer">
                <a href="../index.html" class="btn-outline">トップへ戻る</a>
            </footer>
        </article>
    </main>

    <div id="footer-placeholder"></div>
</body>
</html>
    <?php
    return ob_get_clean();
}
?>