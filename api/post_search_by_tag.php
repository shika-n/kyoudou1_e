<?php
require_once("../db_open.php");
require_once("../util.php");
require_once("../models/tags.php");
require_once("../models/posts.php");
require_once("../templates.php");

session_start();
if (!is_authenticated()) {
	redirect_to(Pages::k_login);
}

if (isset($_GET["page"]) && $_GET['page'] > 0) {
	$page = (int)$_GET["page"];
} else {
	$page = 1;
}
$limit = 5;
$offset = $limit * ($page - 1);

$tags = explode(",", get_if_set("query", $_GET));

$posts = get_post_by_tags($dbh, $_SESSION["user_id"], $tags, $limit, $offset, "newest");

date_default_timezone_set("UTC");
$target_timezone = new DateTimeZone("Asia/Tokyo");

$content = "";
foreach ($posts as $post) {
	$content .= post_panel($post, $target_timezone);
}

header("Content-Type: text/json", true, 200);
echo $content;

