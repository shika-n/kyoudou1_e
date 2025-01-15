<?php
require_once("db_open.php");
require_once("util.php");
require_once("models/users.php");

session_start();

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
    } elseif (mb_strlen($password) < 6) {
        #echo "password is too short, should be 6 characters or longer";
        $_SESSION["error"] = "パスワードは6文字以上で入力してください";
        header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		return;
    } else {
        $record = get_user_by_email($dbh, $email);
        $email_and_password_are_right = false;
        if ($record != NULL && password_verify($password, $record['password'])) {
            $email_and_password_are_right = true;
            $_SESSION["user_id"] = $record['user_id'];
            $_SESSION["name"] = $record['name'];
        }
        if (!$email_and_password_are_right) {
            #echo "email/password is wrong";
            $_SESSION["error"] = "メールかパスワードが間違っています";
            header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
		    return;
        } else {
            echo "logged in as {$_SESSION["name"]} id={$_SESSION["user_id"]}";
        }
    }

} else {
    echo <<<_BODY_
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="login.css">
        <title>ログイン画面</title>
    </head>
    <body>
        <h1>ログイン</h1>
        <div>
            <form method="POST">
            <input type="password" name="password" placeholder="パスワード"><br>
            <input type="email" name="email" placeholder="メールアドレス"><br>
            <input type="submit" name="log" value="ログイン">
            </form>

            <hr>

            <input type="button" name="log" value="新規登録はこちら">
        </div>
    </body>
    </html>
    _BODY_;
}