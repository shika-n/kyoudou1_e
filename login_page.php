<?php
$body = <<<_BODY_
<form method="POST">
    <h2>ログイン</h2>
    <p><input type="text" name="password" placeholder="パスワード"></p>
    <p><input type="text" name="email" placeholder="メール"></p>
    <p><button type="submit" name="login">ログイン</button></p>
</form>
<a href="registration.php">新規登録はこちら</a>
_BODY_;
# echo $body;
