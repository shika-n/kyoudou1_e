<?php
require_once("db_open.php");
require_once("util.php");
require_once("layout.php");
require_once("templates.php");
require_once("models/posts.php");

if (!is_authenticated()) {
	redirect_to(Pages::k_login);
}

// `post_id` を取得
$post_id = get_if_set("id", $_GET);

date_default_timezone_set("UTC");
$target_timezone = new DateTimeZone("Asia/Tokyo");

[ $row, $comments ] = get_post_detail_by_id($dbh, $post_id, $_SESSION['user_id']);

$content = post_panel($row[0], $target_timezone, get_if_set($post_id, $comments, []), true, false);

// `<!-- CONTENT -->` を `$content` に置き換えて出力
$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;


