<?php
require_once("db_open.php");
require_once("util.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$name = get_if_set("name", $_POST);
	$nickname = get_if_set("nickname", $_POST);
	$password = get_if_set("password", $_POST);
	$email = get_if_set("email", $_POST);

	if (!$name || mb_strlen($name) < 1) {
		$_SESSION["error"] = "名前は1文字以上で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}#submit-form", true, 303);
		return;
	}

	if (!$nickname || mb_strlen($nickname) < 1) {
		$_SESSION["error"] = "ニックネームは1文字以上で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}#submit-form", true, 303);
		return;
	}

	if (!$password || mb_strlen($password) < 6) {
		$_SESSION["error"] = "パスワードは6文字以上で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}#submit-form", true, 303);
		return;
	}

	if (!$email || mb_strlen($email) < 1) {
		$_SESSION["error"] = "メールは1文字以上で入力してください";
		header("Location: {$_SERVER['HTTP_REFERER']}#submit-form", true, 303);
		return;
	}

	$hashed_password = password_hash($password, PASSWORD_ARGON2I);

	$statement = $dbh->prepare("INSERT INTO users (name, nickname, email, password) VALUES (?, ?, ?, ?)");
	if ($statement->execute([$name, $nickname, $email, $hashed_password])) {
		echo "Success";
	} else {
		echo "Failed";
	}
} else {
	echo <<< ___EOF
		<form method="POST">
			<input type="text" name="name" placeholder="name">
			<input type="text" name="nickname" placeholder="nickname">
			<input type="text" name="email" placeholder="email">
			<input type="password" name="password" placeholder="password">
			<input type="submit" value="登録">
		</form>
	___EOF;
}
