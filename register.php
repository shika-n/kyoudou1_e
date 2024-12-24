<?php
require_once("db_open.php");
require_once("models/users.php");
require_once("util.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$name = get_if_set("name", $_POST);
	$nickname = get_if_set("nickname", $_POST);
	$password = get_if_set("password", $_POST);
	$email = get_if_set("email", $_POST);

	$_SESSION["name"] = $name;
	$_SESSION["nickname"] = $nickname;
	$_SESSION["email"] = $email;

	if (!$name || mb_strlen($name) < 1) {
		$_SESSION["error"] = "名前は1文字以上で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}

	if (!$nickname || mb_strlen($nickname) < 1) {
		$_SESSION["error"] = "ニックネームは1文字以上で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}

	if (!$password || mb_strlen($password) < 6) {
		$_SESSION["error"] = "パスワードは6文字以上で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}

	if (!$email || mb_strlen($email) < 1) {
		$_SESSION["error"] = "メールは1文字以上で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
	}

	if (user_exists($dbh, $email)) {
		$_SESSION["error"] = "メールは既に存在しています";
		header("Location: {$_SERVER['HTTP_REFERER']}#submit-form", true, 303);
		return;
	}

	$hashed_password = password_hash($password, PASSWORD_ARGON2I);
	
	register($dbh, $name, $nickname, $email, $hashed_password);
} else {
	echo $_SESSION["error"];
	$name = htmlspecialchars(get_if_set("name", $_SESSION, ""), ENT_QUOTES);
	$nickname = htmlspecialchars(get_if_set("nickname", $_SESSION, ""), ENT_QUOTES);
	$email = htmlspecialchars(get_if_set("email", $_SESSION, ""), ENT_QUOTES);

	echo <<< ___EOF
		<form method="POST">
			<input type="text" name="name" placeholder="name" value="$name">
			<input type="text" name="nickname" placeholder="nickname" value="$nickname">
			<input type="text" name="email" placeholder="email" value="$email">
			<input type="password" name="password" placeholder="password">
			<input type="submit" value="登録">
		</form>
	___EOF;
}
