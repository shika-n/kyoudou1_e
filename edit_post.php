<?php
require_once("db_open.php");
require_once("util.php");
require_once("layout.php");
require_once("models/posts.php");
require_once("util.php");

if (!is_authenticated()) {
	redirect_to(Pages::k_login);
}

// セッションのユーザーID取得
$target_user_id = $_SESSION['user_id'];

// 初期化
$title = "";
$content = "";
$image = "";
$error = "";
$is_a_comment = false;

// $post_id = $_GET["post_id"];
$post_id = get_if_set("post_id", $_GET);

// データベースからユーザーの投稿内容を取得
$sql = "SELECT * FROM posts WHERE user_id = :user_id AND post_id = :post_id";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':user_id', $target_user_id, PDO::PARAM_INT);
$stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
$stmt->execute();

if ($record = $stmt->fetch()) {
    $title = htmlspecialchars($record['title'], ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($record['content'], ENT_QUOTES, 'UTF-8');
    $image = htmlspecialchars($record['image'], ENT_QUOTES, 'UTF-8');
    if (isset($record['reply_to'])) {
        $is_a_comment = true;
    }
}

// POSTリクエスト処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRFトークンの確認
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("不正なリクエストです。");
    }

    // フォームからの入力値を取得
    $title = trim(get_if_set("title", $_POST, ""));
    $content = trim(get_if_set("content", $_POST, ""));

    // 入力チェック

    if ((!$title || mb_strlen($title) < 1 || mb_strlen($title) > 20) && !$is_a_comment) {
        $error = "タイトルは1~20文字まで入力してください";
    } elseif (!$content || mb_strlen($content) < 1 || mb_strlen($content) > 8192) {
        $error = "コンテンツは1~8192文字まで入力してください";
    } else {
        // ファイルアップロード処理
        if (!empty($_FILES['image']['name'])) {
            $upload_dir = "post_images/";
            $image_filename = get_unique_image_name($_FILES['image']);
            $uploaded_file = $upload_dir . $image_filename;

            if (check_uploadable_image($_FILES['image']) !== true) {
                $error = "アップロードされたファイルは無効です。";
            } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $uploaded_file)) {
                $error = "画像のアップロードに失敗しました。";
            } else {
                $image = $image_filename;
            }
        }

        // エラーがなければデータベース更新
        if (empty($error)) {
            if (edit_post($dbh, $target_user_id, $post_id, $title, $content, $image)) {
                $_SESSION["info"] = "投稿を更新しました。";
				redirect_to(Pages::k_index);
            } else {
                $error = "更新に失敗しました。";
            }
        }
    }

	$title = htmlspecialchars($title, ENT_QUOTES, "UTF-8");
	$content = htmlspecialchars($content, ENT_QUOTES, "UTF-8");
}



// CSRFトークン生成
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// HTML出力部分

$content = <<< ___EOF___
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>投稿編集</title>
    <style>
				position: absolute;
				left: 10px;
				top: 10px;
				font-size: 20px;
				cursor: pointer;
			}

			.form-container {
				max-width: 800px;
				margin: 0 auto;
				background: #fff;
				padding: 20px;
				border-radius: 5px;
			}

			.form-container label {
				display: block;
				margin-bottom: 10px;
				font-weight: bold;
			}

			.form-container input[type="text"],
			.form-container textarea {
				width: 100%;
				padding: 10px;
				margin-bottom: 20px;
				border: 1px solid #ccc;
				border-radius: 5px;
				box-sizing: border-box;
			}

			.form-container button {
				width: 100%;
				padding: 10px;
				background-color: #007BFF;
				color: #fff;
				border: none;
				border-radius: 5px;
				font-size: 16px;
				cursor: pointer;
			}

			.form-container button:hover {
				background-color: #0056b3;
			}
		</style>
</head>
<body>
    <div class="form-container">
        <h1>投稿編集</h1>
        <?php if (!empty($error)): ?>
            <p class="error-message"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="$csrf_token">

            <!-- TITLE INPUT -->

            <label for="content">コンテンツ</label>
            <textarea id="content" name="content" rows="5" maxlength="255" required>$content</textarea>

            <!-- IMAGE INPUT -->
           
            <button type="button" onclick="showDialog()">保存</button>
			<div id="dialog-panel" class="hidden fixed top-0 left-0 w-screen h-screen flex items-center justify-center bg-black/50 z-50 backdrop-blur-md">
				<div class="bg-white w-fit h-fit p-4 rounded-xl">
					<p>本当に更新しますか？</p>
					<div class="flex gap-2">
						<button type="submit" onclick="hideDialog()" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg mt-4 flex-1 min-w-32">保存</button>
						<button type="button" onclick="hideDialog()" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg mt-4 flex-1 min-w-32">キャンセル</button>
					</div>
				</div>
				<script src="js/dialog.js"></script>
			</div>
        </form>
    </div>
</body>
</html>
___EOF___;
$title_input = <<< ___EOF___
<label for="title">タイトル</label>
<input type="text" id="title" name="title" value="$title" maxlength="20" required>
___EOF___;
$image_input = <<< ___EOF___
<label for="image">画像 (任意)</label>
<input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif">
___EOF___;
if (!$is_a_comment) {
    $content = str_replace("<!-- TITLE INPUT -->", $title_input, $content);
    $content = str_replace("<!-- IMAGE INPUT -->", $image_input, $content);
}
$html = str_replace("<!-- CONTENT -->", $content, $html);
	echo $html;
