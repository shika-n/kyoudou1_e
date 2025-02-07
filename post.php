<?php
require_once("db_open.php");
require_once("util.php");
require_once("layout.php");
require_once("models/posts.php");
require_once("models/tags.php");
require_once("models/categories.php");

if (!is_authenticated()) {
	redirect_to(Pages::k_login);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$title = trim(get_if_set("title", $_POST, ""));
	$content = trim(get_if_set("content", $_POST, ""));
	$tags = get_if_set("tags", $_POST, []);
	$image = get_if_set("image", $_FILES);
	$category = trim(get_if_set("categoryId", $_POST, ""));

	$_SESSION["title"] = $title;
	$_SESSION["content"] = $content;
	$_SESSION["category"] = $category;

	if (!$title || mb_strlen($title) > 20) {
		$_SESSION["error"] = "タイトルは1~20文字まで入力してください";
		redirect_back();
	}
	if (!$content || mb_strlen($content) > 8192) {
		$_SESSION["error"] = "コンテンツは1~8192文字まで入力してください";
		redirect_back();
	}

	if (get_if_set("tmp_name", $image)) {
		$is_uploadable = check_uploadable_image($image);
		if ($is_uploadable !== true) {
			$_SESSION["error"] = $is_uploadable;
			redirect_back();
		}
		$image_filename = get_unique_image_name($image);
		move_uploaded_file($image["tmp_name"], "post_images/" . $image_filename);
	} else {
		$image_filename = null;
	}

	if (count($tags) > 10) {
		$_SESSION["error"] = "タグは10個まで入力してください";
		redirect_back();
	}
	foreach ($tags as $tag) {
		$tag = trim($tag);
		if (mb_strlen($tag) < 1 || mb_strlen($tag) > 20) {
			$_SESSION["error"] = "タグは1~20文字まで入力してください";
			redirect_back();
		}
	}

	$_SESSION["title"] = null;
	$_SESSION["content"] = null;
	$_SESSION["error"] = null;
	$_SESSION["category"] = null;

	$db_err = false;
	$dbh->beginTransaction();

	$post_id = post($dbh, $_SESSION["user_id"], $title, $content, $image_filename, $category);
	$db_err = $db_err || $post_id === false;

	foreach ($tags as $tag) {
		$tag = trim($tag);
		$tag_id = get_tag_id_or_create($dbh, $tag);
		$db_err = $db_err || $tag_id === false;
		if ($tag_id !== false) {
			$db_err = $db_err || !tag_post($dbh, $post_id, $tag_id);
		}
	}
	
	if ($db_err) {
		$dbh->rollBack();
		$_SESSION["error"] = "データベースエラー";
		redirect_back();
	} else {
		$dbh->commit();
		redirect_to(Pages::k_index);
	}
} else {
	$title = htmlspecialchars(get_if_set("title", $_SESSION, ""), ENT_QUOTES);
	$content = htmlspecialchars(get_if_set("content", $_SESSION, ""), ENT_QUOTES);
	$error = htmlspecialchars(get_if_set("error", $_SESSION, ""));
	$category_list = get_categories($dbh);
	$select_options = "";
	foreach ($category_list as $category) {
		$select_options .= <<<___EOF___
		<button type="button" id="{$category['category_id']}" class="category flex items-center justify-center p-3 text-center border rounded-lg cursor-pointer transition select-none relative aspect-square w-full break-words overflow-hidden text-ellipsis whitespace-normal" onclick="selectCategory(this)">
			{$category['category_name']}
		</button>
		___EOF___;
	}
	
	$content = <<< ___EOF___
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
			}

			.form-container button:not(.chips,.category):hover {
				background-color: #0056b3;
			}
		</style>
		<form method="POST" class="form-container flex flex-col gap-4" enctype="multipart/form-data">
			<label for="title">投稿内容</label>
			<p class="mb-2 text-red-600 font-bold underline decoration-wavy">{$error}</p>
			<input type="text" id="title" name="title" placeholder="タイトル" value="$title">
			<div class="relative">
				<textarea id="post_content" name="content" rows="5" placeholder="コンテンツ">$content</textarea>
				<div class="absolute bottom-2 right-1 pr-5">
					<p id="charCounter" class="pb-5"></p>
				</div>
			</div>
			
			<input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif" class="flex-grow">

			<div id="chipsField" class="flex flex-wrap items-center gap-1 text-sm border border-gray-300 p-2 rounded-md">
				<label for="chipInput">タグ</label>
				<input id="chipInput" placeholder="タグを入力してください" maxlength="20" class="flex-grow h-fit focus:outline-none">
				<script src="js/chip_input.js"></script>
			</div>
	
			<div class="text-center">
				<button type="button" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700" onclick="openCategoryWindow()">Choose Category</button>
				<p class="mt-4 text-lg">Selected Category: <span id="selectedCategory" class="font-semibold">その他</span></p>
				<input type="hidden" id="categoryId" name="categoryId" value="99">
			</div>
			<div class="hidden fixed top-0 left-0 w-screen h-screen flex items-center justify-center bg-black/50 z-50 backdrop-blur-md" id="categoryWindow">
				<div class="bg-white p-6 rounded-lg shadow-lg w-96">
					<p>カテゴリーを選択してください</p>
					<p>カテゴリーを選択することで、記事が見つけやすくなります！</p>
					<div class="grid grid-cols-3 gap-3 mb-4">
						<!-- SELECT OPTIONS -->
					</div>
					<div class="flex justify-between">
						<button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" onclick="chooseCategory()">Choose</button>
						<button type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600" onclick="cancelSelection()">Cancel</button>
					</div>
				</div>
			</div>
			<button type="submit">投稿</button>
		</form>
		<script src="js/char_counter.js"></script>
		<script src="js/add_category.js"></script>
	___EOF___;
	$content = str_replace("<!-- SELECT OPTIONS -->", $select_options, $content);
	$_SESSION["error"] = null;
	$html = str_replace("<!-- CONTENT -->", $content, $html);
	echo $html;
}
