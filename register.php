<?php
require_once("db_open.php");
require_once("models/users.php");
require_once("layout.php");
require_once("util.php");

// ログインしていたらトップページに投げる
if (get_if_set("user_id", $_SESSION)) {
	header("Location: .", true, 303);
	return;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$name = get_if_set("name", $_POST);
	$nickname = get_if_set("nickname", $_POST);
	$password = get_if_set("password", $_POST);
	$email = get_if_set("email", $_POST);
	$icon = get_if_set("icon", $_FILES);

	$_SESSION["name"] = $name;
	$_SESSION["nickname"] = $nickname;
	$_SESSION["email"] = $email;

	if (!$name || mb_strlen($name) < 1 || mb_strlen($name) > 20) {
		$_SESSION["error"] = "名前は1~20文字で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}

	if (!$nickname || mb_strlen($nickname) < 1 || mb_strlen($nickname) > 20) {
		$_SESSION["error"] = "ニックネームは1~20文字で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}

	if (!$password || mb_strlen($password) < 6) {
		$_SESSION["error"] = "パスワードは6文字以上で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}

	if (!$email || mb_strlen($email) < 1 || mb_strlen($email) > 120) {
		$_SESSION["error"] = "メールは1~120文字で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$_SESSION["error"] = "メールに間違いがあります";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}

	if (user_exists($dbh, $email)) {
		$_SESSION["error"] = "メールは既に存在しています";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}

	if (get_if_set("tmp_name", $icon)) {
		$imagesize = getimagesize($icon["tmp_name"]);
		if ($imagesize === false) {
			$_SESSION["error"] = "アイコンのアップロードエラー";
			header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
			return;
		}
		if ($icon["size"] > 500000) {
			$_SESSION["error"] = "アイコンのサイズが大きすぎます";
			header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
			return;
		}
		$extension = array_search(mime_content_type($icon["tmp_name"]), [
			"png" => "image/png",
			"jpg" => "image/jpeg",
			"gif" => "image/gif",
		]);
		if ($extension === false) {
			$_SESSION["error"] = "アップロード不可能な拡張子です";
			header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
			return;
		}
		
		$icon_filename = time() . "_" . uniqid() . "_" . sha1_file($icon["tmp_name"]) . "." . $extension;
		move_uploaded_file($icon["tmp_name"], "profile_pictures/" . $icon_filename);
	} else {
		$icon_filename = "man.png";
	}

	$hashed_password = password_hash($password, PASSWORD_ARGON2I);
	register($dbh, $name, $nickname, $email, $hashed_password, $icon_filename);

	$_SESSION["name"] = null;
	$_SESSION["nickname"] = null;
	$_SESSION["email"] = null;
	$_SESSION["error"] = null;

	header("Location: index.php", true, 303);
} else {
	$name = htmlspecialchars(get_if_set("name", $_SESSION, ""), ENT_QUOTES);
	$nickname = htmlspecialchars(get_if_set("nickname", $_SESSION, ""), ENT_QUOTES);
	$email = htmlspecialchars(get_if_set("email", $_SESSION, ""), ENT_QUOTES);

	$error = htmlspecialchars(get_if_set("error", $_SESSION, ""));

	$content = <<< ___EOF___
		<div class="fullcenter">
			<div class="form-top px-8 md:px-16 py-8">
				<h1 class="text-2xl font-bold">新規登録</h1>
				<p class="mb-2 text-red-600 font-bold underline decoration-wavy">{$error}</p>
				<img class="mx-auto mb-4 w-20 aspect-square object-cover object-center rounded-full" id="preview" src="profile_pictures/man.png">
				<form method="POST" class="flex flex-col gap-4" enctype="multipart/form-data">
					<!-- 名前 -->
					<input type="text" id="name" name="name" placeholder="名前" value="$name">
					<!-- ニックネーム -->
					<input type="text" id="nickname" name="nickname" placeholder="ニックネーム" value="$nickname">
					<!-- メール -->
					<input type="text" name="email" placeholder="メール" value="$email">
					<!-- パスワード -->
					<input type="password" name="password" placeholder="パスワード">
					<div class="flex flex-col md:flex-row md:gap-2 items-left text-left">
						<label>アイコン</label>
						<input type="file" id="icon" name="icon" accept="image/png, image/jpeg, image/gif" class="flex-grow">
					</div>
					<!-- 送信 -->
					<input type="submit" class="button font-bold bg-amber-200 hover:bg-amber-300 active:bg-amber-400 transition-all" value="登録完了">
				</form>
				<hr class="my-4 border-black">
				<a href="login_page.php" class="linkbutton block bg-blue-200 hover:bg-blue-300 active:bg-blue-400 border-blue-500 border-2 p-1 rounded-lg transition-all">ログインに戻る</a>
			</div>
		</div>
		<script src="js/icon_preview.js"></script>
	___EOF___;

	echo str_replace("<!-- CONTENT -->", $content, $guest_html);

	$_SESSION["error"] = null;
}
