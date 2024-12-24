<?php
require_once("db_open.php");
require_once("util.php");
require_once("models/users.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
	$email = get_if_set("email", $_POST);
    $password = get_if_set("password", $_POST);
    if ($email == NULL || $password == NULL) {
        echo "email or password or both not input";
    } elseif (mb_strlen($email) > 120) {
        echo "email is too long, should be no longer than 120 characters";
    } elseif (mb_strlen($password) < 6) {
        echo "password is too short, should be 6 characters or longer";
    } else {
        $record = get_user_by_email($dbh, $email);
        $email_and_password_are_right = false;
        if ($record != NULL && password_verify($password, $record['password'])) {
            $email_and_password_are_right = true;
            $_SESSION["user_id"] = $record['user_id'];
            $_SESSION["name"] = $record['name'];
        }
        if (!$email_and_password_are_right) {
            echo "email/password is wrong";
        } else {
            echo "logged in as {$_SESSION["name"]} id={$_SESSION["user_id"]}";
        }
    }

} else {
    echo <<<_BODY_
    <form method="POST">
        <h2>ログイン</h2>
        <p><input type="text" name="email" placeholder="メール"></p>
        <p><input type="text" name="password" placeholder="パスワード"></p>
        <p><button type="submit" name="login">ログイン</button></p>
    </form>
    <a href="register.php">新規登録はこちら</a>
    _BODY_;
}