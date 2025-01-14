<?php
require_once("db_open.php");
require_once("layout.php");
require_once("util.php");
require_once("models/users.php");

session_start();

// ログインしていたらトップページに投げる
if (get_if_set("user_id", $_SESSION)) {
	header("Location: .", true, 303);
	return;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
	$email = get_if_set("email", $_POST);
    $password = get_if_set("password", $_POST);
    if ($email == NULL || $password == NULL) {
        #echo "email or password or both not input";
        $_SESSION["error"] = "メールとパスワードを入力してください";
        header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
    } elseif (mb_strlen($email) > 120) {
        #echo "email is too long, should be no longer than 120 characters";
        $_SESSION["error"] = "メールを120文字以下で入力してください";
        header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
    } else {
        $record = get_user_by_email($dbh, $email);
        if ($record != NULL && password_verify($password, $record['password'])) {
            $_SESSION["user_id"] = $record['user_id'];
            $_SESSION["name"] = $record['name'];
			$_SESSION["email"] = null;
			$_SESSION["error"] = null;
            // echo "logged in as {$_SESSION["name"]} id={$_SESSION["user_id"]}";
            header("Location: .", true, 303);
        } else {
            #echo "email/password is wrong";
            $_SESSION["error"] = "メールかパスワードが間違っています";
            header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		    return;
        }
    }

} else {
	$name = htmlspecialchars(get_if_set("name", $_SESSION, ""), ENT_QUOTES);
	$email = htmlspecialchars(get_if_set("email", $_SESSION, ""), ENT_QUOTES);

	$error = htmlspecialchars(get_if_set("error", $_SESSION, ""));
    
	$content = <<< ___EOF___
		<div class="fullcenter">
			<div class="form-top px-8 md:px-16 py-8">
				<h1 class="text-2xl font-bold">ログイン</h1>
				<p class="mb-2 text-red-600 font-bold underline decoration-wavy">{$error}</p>
				<form method="POST" class="flex flex-col gap-4">
					<input type="text" name="email" placeholder="メール" value="$email">
					<!-- パスワード -->
					<input type="password" name="password" placeholder="パスワード">
					<!-- 送信 -->
					<input type="submit" class="button font-bold bg-amber-200 hover:bg-amber-300 active:bg-amber-400 transition-all" value="ログイン">
				</form>
				<hr class="my-4 border-black">
				<a href="register.php" class="linkbutton block bg-blue-200 hover:bg-blue-300 active:bg-blue-400 border-blue-500 border-2 p-1 rounded-lg transition-all">新規登録</a>
			</div>
		</div>
	___EOF___;
	$_SESSION["error"] = null;
	echo str_replace("<!-- CONTENT -->", $content, $guest_html);
}
