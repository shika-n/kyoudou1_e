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
$post_arr = get_posts($dbh, $_SESSION["user_id"]);

$comments = [];

foreach ($post_arr as $row) {
	if ($row["reply_to"]) {
		$comments[$row["reply_to"]][] = $row;
	} else {
		$content .= post_panel($row, $target_timezone, get_if_set($row["post_id"], $comments));
	}
}

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
