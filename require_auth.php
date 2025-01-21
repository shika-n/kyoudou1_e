<?php
// グローバルスコープで呼ぶこと
// ログインしていないとログインページに投げる
if (!get_if_set("user_id", $_SESSION)) {
	header("Location: login_page.php", true, 303);
	exit;
}
