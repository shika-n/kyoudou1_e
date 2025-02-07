<?php
require_once("db_open.php");
require_once("util.php");
require_once("layout.php");
require_once("models/posts.php");
require_once("models/tags.php");
require_once("util.php");
require_once("models/categories.php");
require_once("templates.php");

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
$category_list = get_categories($dbh);
$select_options = "";
foreach ($category_list as $category) {
	$select_options .= <<<___EOF___
	<button type="button" id="{$category['category_id']}" class="category flex items-center justify-center p-3 text-center border rounded-lg cursor-pointer transition select-none relative aspect-square w-full break-words overflow-hidden text-ellipsis whitespace-normal" onclick="selectCategory(this)">
		{$category['category_name']}
	</button>
	___EOF___;
}

// $post_id = $_GET["post_id"];
$post_id = get_if_set("post_id", $_GET);

// データベースからユーザーの投稿内容を取得
$record = get_post_by_id($dbh, $_SESSION["user_id"], $post_id);

if ($record) {
	if ($record["user_id"] != $_SESSION["user_id"]) {
		redirect_to(Pages::k_profile);
	}

    $title = htmlspecialchars($record['title'], ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($record['content'], ENT_QUOTES, 'UTF-8');
    $image = htmlspecialchars($record['image'], ENT_QUOTES, 'UTF-8');
	$category_ids = explode(",", $record['category_ids']);
	$categories = htmlspecialchars($record['categories'], ENT_QUOTES, "UTF-8");
    if (isset($record['reply_to'])) {
        $is_a_comment = true;
    }

	$hidden_category_input_html = "";
	foreach ($category_list as $category) {
		if (array_search($category["category_id"], $category_ids) !== false) {
			$hidden_category_input_html .= <<< ___EOF___
				<input type="hidden" name="categoryIds[]" value="{$category["category_id"]}">
			___EOF___;
		}
	}

	$tags_html = "";
	if ($record["tags"]) {
		$tags = explode(",", get_if_set("tags", $record));
		foreach ($tags as $key => $value) {
			$tags_html .= chip(htmlspecialchars($value, ENT_QUOTES, "UTF-8"));
		}
	}

	$image_position = get_if_set("image_position", $record);
	$image_position_above_checked = $image_position == 0 ? "checked" : "";
	$image_position_bottom_checked = $image_position == 1 ? "checked" : "";
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
	$category_ids = get_if_set("categoryIds", $_POST, [99]);
	$tags = get_if_set("tags", $_POST, []);
	$image_position = get_if_set("image_position", $_POST, "above");

	if ($image_position == "above") {
		$image_position = 0;
	} else {
		$image_position = 1;
	}

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

		if (count($tags) > 10) {
			$error = "タグは10個まで入力してください";
		}
		foreach ($tags as $tag) {
			$tag = trim($tag);
			if (mb_strlen($tag) < 1 || mb_strlen($tag) > 20) {
				$error = "タグは1~20文字まで入力してください";
			}
		}

        // エラーがなければデータベース更新
        if (empty($error)) {
			$dbh->beginTransaction();

			$db_category_ids = array_column(get_categories($dbh), "category_id");
			foreach ($category_ids as $category_id) {
				if (array_search($category_id, $db_category_ids) === false) {
					$_SESSION["error"] = "カテゴリーエラー";
					$dbh->rollBack();
					redirect_back();
				}
			}

            if (edit_post($dbh, $target_user_id, $post_id, $title, $content, $image, 99,$image_position)) {
				$db_err = false;
				$tag_ids = [];
				foreach ($tags as $tag) {
					$tag = trim($tag);
					$tag_id = get_tag_id_or_create($dbh, $tag);
					$db_err = $db_err || $tag_id === false;
					if ($tag_id !== false) {
						$tag_ids[] = $tag_id;
						$db_err = $db_err || !tag_post($dbh, $post_id, $tag_id);
					}
				}
				$db_err = $db_err || !remove_unlisted_tag($dbh, $post_id, $tag_ids);
				$db_err = $db_err || !set_post_categories($dbh, $post_id, $category_ids);
				if ($db_err) {
					$error = "タグの更新失敗しました";
					$dbh->rollBack();
				} else {
					$dbh->commit();
					$_SESSION["info"] = "投稿を更新しました。";
					redirect_to(Pages::k_index);
				}
            } else {
                $error = "更新に失敗しました。";
				$dbh->rollBack();
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
				font-weight: bold;
			}

			.form-container input[type="text"]:not(.chips,.category),
			.form-container textarea {
				width: 100%;
				padding: 10px;
				border: 1px solid #ccc;
				border-radius: 5px;
				box-sizing: border-box;
			}

			.form-container button:not(.chips,.category) {
				width: 100%;
				padding: 10px;
				background-color: #007BFF;
				color: #fff;
				border: none;
				border-radius: 5px;
				font-size: 16px;
				cursor: pointer;
				transition: all 0.1s;
			}

			.form-container button:hover:not(.chips,.category) {
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
		<form method="POST" class="form-container flex flex-col gap-4" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="$csrf_token">

            <!-- TITLE INPUT -->

            <label for="post_content">コンテンツ</label>
            <textarea id="post_content" name="content" rows="5" maxlength="8192" required>$content</textarea>

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
<div class="flex">
	<input type="file" id="image-select" accept="image/png, image/jpeg, image/gif" class="hidden">
	<button type="button" id="select-image-button" onclick="selectImage()" class="max-w-32">画像を投入</button>
	<script src="js/upload_image.js"></script>
</div>
<!--
<label for="image">画像 (任意)</label>
<input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif">
<fieldset>
	<legend>画像の表示位置を選んでください</legend>
	<div style="display: flex; gap: 20px; align-items: center;">
		<label>
			<input type="radio" id="above" name="image_position" value="above" $image_position_above_checked>
			テキストの上
		</label>
		<label>
			<input type="radio" id="below" name="image_position" value="below" $image_position_bottom_checked>
			テキストの下
		</label>
	</div>
</fieldset>
-->
<div>
	<div id="chipsField" class="flex flex-wrap items-center gap-1 text-sm border border-gray-300 p-2 rounded-md">
		<label for="search-field">タグ</label>
		$tags_html
		<input id="search-field" placeholder="タグを入力してください" maxlength="20" class="flex-grow h-fit focus:outline-none">
	</div>
	<div class="relative">
		<ol id="suggestion-list" class="hidden absolute p-1 bg-white/30 rounded-md border border-gray-400 shadow-xl backdrop-blur-md text-sm"></ol>
	</div>
	<script src="js/tag_search_complete.js"></script>
	<script src="js/chip_input.js"></script>
</div>
<div class="text-center">
	<button type="button" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700" onclick="openCategoryWindow()">カテゴリーを選択</button>
	<p class="mt-4 text-lg">カテゴリー: <span id="selectedCategory" class="font-semibold">{$categories}</span></p>
	<div id="hiddenCategoryInputs">
		$hidden_category_input_html
	</div>
</div>
<div class="hidden fixed top-0 left-0 w-screen h-screen flex items-center justify-center bg-black/50 z-50 backdrop-blur-md" id="categoryWindow">
	<div class="bg-white p-6 rounded-lg shadow-lg w-96">
		<div class="text-center text-lg mb-2">
			<p>カテゴリーを選択してください</p>
		</div>
		<div class="grid grid-cols-3 gap-3 mb-4">
			<!-- SELECT OPTIONS -->
		</div>
		<div class="flex justify-between gap-2">
			<button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" onclick="chooseCategory()">追加</button>
			<button type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600" onclick="cancelSelection()">戻る</button>
		</div>
	</div>
</div>
<script src="js/add_category.js"></script>
___EOF___;
if (!$is_a_comment) {
	$image_input = str_replace("<!-- SELECT OPTIONS -->", $select_options, $image_input);
    $content = str_replace("<!-- TITLE INPUT -->", $title_input, $content);
    $content = str_replace("<!-- IMAGE INPUT -->", $image_input, $content);
}
$html = str_replace("<!-- CONTENT -->", $content, $html);
	echo $html;
