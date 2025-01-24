<?php
require_once("../db_open.php");
require_once("../models/posts.php");
require_once("../util.php");
require_once("../templates.php");

session_start();
if (!is_authenticated()) {
	echo "403 Forbidden";
	return http_response_code(403);
}

date_default_timezone_set("UTC");
$target_timezone = new DateTimeZone("Asia/Tokyo");
$post_arr = get_posts($dbh, $_SESSION["user_id"]);
$content = "";
$comments = [];

foreach ($post_arr as $row) {
	if ($row["reply_to"]) {
		$comments[$row["reply_to"]][] = $row;
	} else {
		$content .= post_panel($row, $target_timezone, get_if_set($row["post_id"], $comments));
	}
}


header("Content-Type: text/json", true, 200);
echo $content;
