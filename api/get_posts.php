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

$type = get_if_set("type", $_GET);
$post_arr = [];

if ($type === "timeline") {
	$post_arr = get_posts($dbh, $_SESSION["user_id"], $limit, $offset, get_if_set("sort_order", $_SESSION, "newest"));
} else if ($type === "profile") {
	$profile_id = get_if_set("id", $_GET);
	if ($profile_id) {
		if ($profile_id == -1) {
			$profile_id = $_SESSION["user_id"];
		}
		$post_arr = get_posts_by_user($dbh, $_SESSION["user_id"], $profile_id, $limit, $offset, get_if_set("sort_order", $_SESSION, "newest"));
	} else {
		header("Content-Type: text/json", true, 400);
		echo json_encode([
			"message" => "No profile id"
		]);
		return;
	}
} else if ($type === "tags") {
	$tags = explode(",", get_if_set("query", $_GET));
	$post_arr = get_post_by_tags($dbh, $_SESSION["user_id"], $tags, $limit, $offset, get_if_set("sort_order", $_SESSION, "newest"));
} else if ($type === "category") {
	$category_id = get_if_set("id", $_GET, 0);
	$post_arr = get_posts_by_category($dbh, $_SESSION["user_id"], $category_id, $limit, $offset, get_if_set("sort_order", $_SESSION, "newest"));
} else if ($type === "followings") {
	$post_arr = get_posts_by_followings($dbh, $_SESSION["user_id"], $limit, $offset, get_if_set("sort_order", $_SESSION, "newest"));
} else if ($type === "liked") {
	// get liked posts
} else {
	header("Content-Type: text/json", true, 400);
	echo json_encode([
		"message" => "Bad type"
	]);
	return;
}

$content = "";
foreach ($post_arr as $row) {
	$content .= post_panel($row, $target_timezone);
}


header("Content-Type: text/json", true, 200);
echo $content;
