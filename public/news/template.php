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
    <title><?php echo $title; ?> - 箱庭クラフト</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../images/server_icon.png" type="image/png">
    <script src="../js/include.js" defer></script>
    <script src="../js/day-count.js"></script>
    <script src="../js/slider.js" defer></script>
    <style>
        /* =========================================
           ニュース詳細ページのレイアウト修正
           ========================================= */
        
        /* ヘッダーの裏にコンテンツが潜り込むのを防ぐ */
        .section-padding {
            padding-top: calc(var(--header-height) + 40px) !important;
        }

        .news-detail-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 40px 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); /* ページに馴染むよう軽い影を追加 */
        }

        .news-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
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
            background: var(--primary-color); /* 共通変数のオレンジを使用 */
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
        }

        /* タイトル崩れの修正 */
        .news-title {
            font-size: clamp(1.5rem, 5vw, 2.2rem); 
            line-height: 1.4 !important; 
            color: #333;
            margin: 10px 0;
            word-wrap: break-word; 
            overflow: visible !important; 
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

        /* 本文内のリンク色を調整 */
        .news-content a {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .news-footer {
            margin-top: 50px;
            text-align: center;
            padding-top: 30px;
            border-top: 2px dashed #e0ddd9; /* 共通デザインに合わせる */
        }
/* =========================================
           共通スライドショー（カルーセル）スタイル
           ========================================= */
        .event-slider-container {
            position: relative;
            max-width: 100%;
            margin: 25px auto;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            background: #1a1a1a; /* 画像の隙間が目立たないよう黒背景 */
        }
        .event-slider {
            display: flex;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
        }
        .event-slide {
            min-width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .event-slide img {
            width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
        }
        /* 操作ボタン */
        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.4);
            color: white;
            border: none;
            padding: 20px 12px;
            cursor: pointer;
            font-size: 1.5rem;
            z-index: 10;
            transition: background 0.3s;
            outline: none;
        }
        .slider-btn:hover { background: rgba(0, 0, 0, 0.7); }
        .btn-prev { left: 0; border-radius: 0 8px 8px 0; }
        .btn-next { right: 0; border-radius: 8px 0 0 8px; }
        
        /* インジケーター（下のドット） */
        .slider-dots {
            text-align: center;
            padding: 15px;
            background: #fff;
            border-bottom: 1px solid #eee;
        }
        .dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin: 0 6px;
            background: #ccc;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
        }
        .dot.active {
            background: var(--primary-color);
            transform: scale(1.3);
        }

        /* スマホ向けの調整 */
        @media (max-width: 768px) {
            .slider-btn { padding: 15px 8px; font-size: 1.2rem; }
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
                <?php echo $content; ?>
            </section>

            <footer class="news-footer">
                <a href="../index.html" class="btn-nav btn-outline-nav">トップへ戻る</a>
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