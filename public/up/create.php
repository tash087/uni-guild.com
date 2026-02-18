<?php
// PHP GDライブラリのチェック（念のため）
if (!extension_loaded('gd')) {
    die("GD Library is not installed. Please enable GD in your PHP configuration.");
}

// ヘッダーをPNG画像として設定
header('Content-Type: image/png');

// 16進数カラーをRBGに変換するヘルパー関数
function hex_to_rgb($hex) {
    if (strlen($hex) !== 6) return [0, 0, 0]; // 不正なHEXコードは黒に
    return [
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2))
    ];
}

// =======================================================
// 1. パラメータの取得とサニタイズ
// =======================================================
$color_hex = isset($_GET['color']) ? strtoupper(preg_replace('/[^0-9A-F]/', '', $_GET['color'])) : 'FFFFFF';
$bgcolor_hex = isset($_GET['bgcolor']) ? strtoupper(preg_replace('/[^0-9A-F]/', '', $_GET['bgcolor'])) : '506414';

// 描画するテキストデータ (最大4行)
$texts = [];
for ($i = 1; $i <= 4; $i++) {
    $texts[] = [
        // テキストは特殊文字をエスケープ
        'text' => isset($_GET["text$i"]) ? htmlspecialchars($_GET["text$i"]) : '',
        'size' => isset($_GET["size$i"]) ? max(5, min(50, (float)$_GET["size$i"])) : 13.5, // サイズ制限
        'x' => isset($_GET["x$i"]) ? (int)$_GET["x$i"] : 5,
        'y' => isset($_GET["y$i"]) ? (int)$_GET["y$i"] : (30 * $i), 
    ];
}

// =======================================================
// 2. 画像の生成と色の設定
// =======================================================
$width = 128;
$height = 128;

$image = imagecreatetruecolor($width, $height);

// 背景色の割り当てと塗りつぶし
$bg_rgb = hex_to_rgb($bgcolor_hex);
$background_color = imagecolorallocate($image, $bg_rgb[0], $bg_rgb[1], $bg_rgb[2]);
imagefill($image, 0, 0, $background_color);

// 文字色の割り当て
$text_rgb = hex_to_rgb($color_hex);
$text_color = imagecolorallocate($image, $text_rgb[0], $text_rgb[1], $text_rgb[2]);

// =======================================================
// 3. テキストの描画
// =======================================================

// ★★★ ！！！重要！！！ フォントファイルのパスを指定してください ★★★
// Webサーバー上にアップロードしたTTFフォントファイルへの絶対パスまたは相対パスを指定してください。
// 例: $font_file = __DIR__ . '/fonts/mcf_font.ttf';
$font_file = './arial.ttf'; // このパスは環境に合わせて修正が必要です。

$default_font_used = false;

foreach ($texts as $line) {
    if (!empty($line['text'])) {
        if (file_exists($font_file)) {
            // TTFフォントを使ってテキストを描画
            imagettftext(
                $image,
                $line['size'],
                0,
                $line['x'],
                $line['y'],
                $text_color,
                $font_file,
                $line['text']
            );
        } else {
            // フォントファイルがない場合、GDのデフォルトフォントで代替 (y座標を調整)
            if (!$default_font_used) {
                // 初回のみ警告を出す（画像内に描画すると画像が崩れるため注意）
                // 実際にはログなどに記録すべきですが、ここでは簡略化
                error_log("Warning: Font file not found at " . $font_file);
                $default_font_used = true;
            }
            // GDのデフォルトフォントサイズ5を使用 (Y座標はimagestring用に調整)
            imagestring($image, 5, $line['x'], $line['y'] - 12, $line['text'], $text_color);
        }
    }
}

// =======================================================
// 4. 画像の出力とメモリ解放
// =======================================================

imagepng($image);
imagedestroy($image);

?>