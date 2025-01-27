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
if (isset($_GET["page"]) && $_GET['page'] > 0) {
	$page = (int)$_GET["page"];
} else {
	$page = 1;
}
$limit = 5;
$offset = $limit * ($page - 1);
$post_arr = get_posts($dbh, $_SESSION["user_id"], $limit, $offset);
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
