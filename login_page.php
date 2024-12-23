<?php
require_once("db_open.php");
require_once("util.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
	$email = get_if_set("email", $_POST);
    $password = get_if_set("password", $_POST);
    if ($email == NULL || $password == NULL) {
        echo "email or password or both not input";
    } else {
        $sql = "SELECT * FROM users WHERE email = '{$email}'";
        $sql_res = $dbh->query($sql);
        $record = $sql_res->fetch();
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
    <a href="registration.php">新規登録はこちら</a>
    _BODY_;
}