<?php
require_once("db_open.php");
require_once("layout.php");
require_once("util.php");
require_once("models/posts.php");
require_once("templates.php");

if (!is_authenticated()) {
	redirect_to(Pages::k_login);
}

date_default_timezone_set("UTC");
$target_timezone = new DateTimeZone("Asia/Tokyo");

$content = "";
[ $post_arr, $comments ] = get_posts($dbh, $_SESSION["user_id"], 5, 0, get_if_set("sort_order", $_SESSION, "newest"));

$content .= sort_order_select();

foreach ($post_arr as $row) {
	$content .= post_panel($row, $target_timezone, get_if_set($row["post_id"], $comments));
}

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
