<?php
require_once("db_open.php");
require_once("util.php");
require_once("layout.php");
require_once("models/posts.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$title = trim(get_if_set("title", $_POST, ""));
	$content = trim(get_if_set("content", $_POST, ""));
	$image = get_if_set("image", $_FILES);

	$_SESSION["title"] = $title;
	$_SESSION["content"] = $content;

	if (!$title || mb_strlen($title) > 20) {
		$_SESSION["error"] = "タイトルは1~20文字まで入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}
	if (!$content || mb_strlen($content) > 255) {
		$_SESSION["error"] = "コンテンツは1~255文字まで入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}

	if (get_if_set("tmp_name", $image)) {
		$is_uploadable = check_uploadable_image($image);
		if ($is_uploadable !== true) {
			$_SESSION["error"] = $is_uploadable;
			header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
			return;
		}
		$image_filename = get_unique_image_name($image);
		move_uploaded_file($image["tmp_name"], "post_images/" . $image_filename);
	} else {
		$image_filename = null;
	}

	$_SESSION["title"] = null;
	$_SESSION["content"] = null;
	$_SESSION["error"] = null;

	post($dbh, $_SESSION["user_id"], $title, $content, $image_filename);
	header("Location: .", true, 303);
} else {
	$title = htmlspecialchars(get_if_set("title", $_SESSION, ""), ENT_QUOTES);
	$content = htmlspecialchars(get_if_set("content", $_SESSION, ""), ENT_QUOTES);
	$error = htmlspecialchars(get_if_set("error", $_SESSION, ""));
	
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
		<form method="POST" class="form-container flex flex-col" enctype="multipart/form-data">
			<label for="title">投稿内容</label>
			<p class="mb-2 text-red-600 font-bold underline decoration-wavy">{$error}</p>
			<input type="text" id="title" name="title" placeholder="タイトル" value="$title">
			<div class="relative">
				<textarea id="content" name="content" rows="5" placeholder="コンテンツ">$content</textarea>
				<div class="absolute bottom-2 right-1 pr-5">
					<p id="charCounter" class="pb-5"></p>
				</div>
			</div>
			<input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif" class="flex-grow">
			<button type="submit">投稿</button>
		</form>
		<script src="js/char_counter.js"></script>
	___EOF___;

	$_SESSION["error"] = null;
	$html = str_replace("<!-- CONTENT -->", $content, $html);
	echo $html;
}
