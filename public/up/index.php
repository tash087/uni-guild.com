<?php

// =======================================================
// 設定
// =======================================================
$upload_dir = 'uploads/'; // 画像を保存するディレクトリ
$max_file_size = 50 * 1024; // 最大ファイルサイズ (50KB)
$max_width = 128; // 最大幅
$max_height = 128; // 最大高さ
$display_limit = 100; // 表示するファイルの最大件数

// アップロードディレクトリが存在しない場合は作成
if (!is_dir($upload_dir)) {
    // 0777 は開発環境向け。本番環境ではより制限されたパーミッション推奨
    mkdir($upload_dir, 0777, true);
}

// =======================================================
// アップロード処理 (複数ファイル対応に変更)
// =======================================================
$message = ''; // ユーザーへのメッセージ

if (isset($_POST['submit'])) {
    if (isset($_FILES['fileToUpload'])) {
        
        $files = $_FILES['fileToUpload'];
        $uploaded_count = 0;
        $error_messages = [];

        // 複数ファイルアップロードのためのループ処理
        // files['name']が配列の場合、ループで一つずつ処理する
        for ($i = 0; $i < count($files['name']); $i++) {
            
            // エラーがないファイルのみ処理を継続
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                // ファイルが選択されていない、またはエラーが発生した場合
                if ($files['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                     $error_messages[] = 'エラー: ファイルのアップロードに失敗しました (' . htmlspecialchars($files['name'][$i]) . ')';
                }
                continue;
            }

            $tmp_name = $files['tmp_name'][$i];
            $original_name = $files['name'][$i];

            // 1. ファイル形式とサイズチェック
            $file_type = mime_content_type($tmp_name);
            if ($file_type !== 'image/png') {
                $error_messages[] = '<font color="red"><b>エラー: PNGファイルのみアップロード可能です (' . htmlspecialchars($original_name) . ')。</b></font>';
            } elseif ($files['size'][$i] > $max_file_size) {
                $error_messages[] = '<font color="red"><b>エラー: ファイルサイズが大きすぎます (' . htmlspecialchars($original_name) . ')。最大' . round($max_file_size/1024, 1) . 'KBです。</b></font>';
            } else {
                // 2. 画像サイズのチェック
                list($width, $height) = getimagesize($tmp_name);
                if ($width > $max_width || $height > $max_height) {
                    $error_messages[] = '<font color="red"><b>エラー: 画像サイズが ' . $max_width . 'x' . $max_height . ' ピクセルを超えています (' . htmlspecialchars($original_name) . ')。</b></font>';
                } else {
                    // 3. ファイル名の決定と保存
                    // time() + $i とすることで、複数ファイル同時アップロード時のファイル名重複を避ける
                    $new_filename_base = time() + $i; 
                    $new_filename = $new_filename_base . '.png';
                    $destination = $upload_dir . $new_filename;

                    if (move_uploaded_file($tmp_name, $destination)) {
                        $uploaded_count++;
                    } else {
                        $error_messages[] = '<font color="red"><b>エラー: ファイルの保存に失敗しました (' . htmlspecialchars($original_name) . ')。</b></font>';
                    }
                }
            }
        } // for ループ終了
        
        // 最終的なメッセージの組み立て
        if ($uploaded_count > 0) {
            $message .= '<font color="green"><b>アップロード成功: ' . $uploaded_count . '個のファイルをアップロードしました。</b></font><br>';
        }
        if (!empty($error_messages)) {
            $message .= implode('<br>', $error_messages);
        }
        if ($uploaded_count === 0 && empty($error_messages)) {
             $message = '<font color="red"><b>エラー: ファイルが選択されていないか、すべてのアップロードに失敗しました。</b></font>';
        }
        
    } else if (isset($_POST['submit'])) {
        $message = '<font color="red"><b>エラー: ファイルが選択されていないか、アップロードに失敗しました。</b></font>';
    }
}


// =======================================================
// ファイル一覧の取得とソート (変更なし)
// =======================================================
$file_list = glob($upload_dir . '*.png');
$files_data = [];
foreach ($file_list as $file_path) {
    $filename = basename($file_path);
    $timestamp = (int)pathinfo($filename, PATHINFO_FILENAME);
    
    // ImageMapコマンド用のファイル名 (拡張子なし)
    $imagemap_filename = pathinfo($filename, PATHINFO_FILENAME);
    
    $files_data[$timestamp] = [
        'path' => $file_path,
        'filename' => $filename,
        'size_bytes' => filesize($file_path),
        'image_size' => getimagesize($file_path),
        'timestamp' => $timestamp,
        'imagemap_filename' => $imagemap_filename
    ];
}

// タイムスタンプで降順ソート (最新のファイルが先頭 = 画像No: 1 になる)
krsort($files_data);

// 表示制限
$files_to_display = array_slice($files_data, 0, $display_limit);

// =======================================================
// HTML出力開始 (フォーム部分のみ変更)
// =======================================================
?>

<html>
    <head>
        <title>ゆにくら！[画像アップローダー]</title>
        <style>
            /* オリジナルのCSS */
            .red { background-color: red!important; }
            .practice-float { font-size: 11px; }
            .practice-float p { background-color: #eef; overflow: hidden; margin: 10px 0; padding: 5px }
            .practice-float .floated { float: left; margin-right: 10px; margin-bottom: 10px; width: 100px; height: 100px; }
            .practice-float .floated.right { float: right; }
        </style>
<script src="../js/script.js"></script>
<script src="../js/include.js"></script>
<link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
<div id="header-placeholder"></div>
        <div style="display:inline-flex">
            <form action="./index.php" method="post" enctype="multipart/form-data">
                <input type="file" name="fileToUpload[]" id="fileToUpload" multiple>
                <input type="submit" value="画像ファイルをアップロード(Upload)" name="submit">
            </form>
            <div style="width:100px"></div>
            <form action="./index.php" method="get">
                <input type="submit" value="更新(Refresh)">
            </form>
        </div>
        
        <?php if ($message): ?>
            <div style="margin-top: 10px;"><?php echo $message; ?></div>
        <?php endif; ?>

        <div>
            <font color="red"><b>・1人10～20枚(目標は10枚)を目安にして下さい。</b></font>
        </div>
        <div>
            <font color="red"><b>・不要になった地図は必ず剥がしてください。</b></font>
        </div>
        <div>
            <font color="red"><b>・鯖の許容数を超えた場合はマップを全クリアし、再度、ポスターを貼付け要請をします。</b></font>
        </div>
        <div>
            ・看板を作成する場合は<a href="create.html">看板画像作成ツール</a>をご利用いただけます。
        </div>
        <div>・拡張子はpngのファイルを指定してください。</div>
        <div>・画像サイズの縦横は最大<?php echo $max_width; ?>x<?php echo $max_height; ?>ピクセルにしてください。</div>
        <div>・最新の<?php echo $display_limit; ?>件の画像ファイルを表示しています。</div>
        <div>
            ・最新の1000件を表示したい場合は<a href="./?limit=1000">/up/?limit=1000</a>のようにしてアクセスしてください。
        </div>

        <hr/>
        
        <section class="practice-float">
        <?php
        $file_count = 0; // 画像Noのカウンタを初期化
        foreach ($files_to_display as $data) {
            $file_count++; // 画像Noをインクリメント
            $upload_date = date('Y/m/d H:i:s', $data['timestamp']);
            $image_width = $data['image_size'][0];
            $image_height = $data['image_size'][1];
            $file_size_kb = round($data['size_bytes'] / 1024);
            $img_path = './' . $data['path']; 
        ?>
            <p>
                <a name="<?php echo $img_path; ?>"></a>
                <a href="<?php echo $img_path; ?>" target="_popup">
                    <img class="floated" src="<?php echo $img_path; ?>" style="float:left">
                </a>
                画像No：<?php echo $file_count; ?>　画像サイズ:<?php echo $image_width; ?>x<?php echo $image_height; ?>　ファイルサイズ:<?php echo $file_size_kb; ?>KB　アップロード日時：<?php echo $upload_date; ?><br/>
                <input type="text" size="100" value="/imagemap <?php echo $data['imagemap_filename']; ?>">
                <br/>
            </p>
        <?php
        }
        if ($file_count === 0) {
            echo '<p>現在、画像ファイルはアップロードされていません。</p>';
        }
        ?>
        </section>
        <hr/>
<div id="footer-placeholder"></div>
    </body>
</html>