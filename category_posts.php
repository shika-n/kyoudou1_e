<?php
require_once("db_open.php");
require_once("layout.php");
require_once("util.php");
require_once("models/posts.php");
require_once("templates.php");

if (!is_authenticated()) {
	redirect_to(Pages::k_login);
}

$category_id = get_if_set("id", $_GET, 0);

date_default_timezone_set("UTC");
$target_timezone = new DateTimeZone("Asia/Tokyo");

$content = "";
$post_arr = get_posts_by_category($dbh, $_SESSION["user_id"], $category_id, 5, 0, get_if_set("sort_order", $_SESSION, "newest"));

$content .= sort_order_select();
foreach ($post_arr as $row) {
	$content .= post_panel($row, $target_timezone);
}

$scripts = <<< ___EOF___
	<script src='js/reach_bottom_action.js'></script>

	<script>
		reachBottomActionQuery.set("type", "category");
		reachBottomActionQuery.set("id", $category_id);
	</script>
___EOF___;
hide_markdown_image();
$html = str_replace("<!-- CONTENT -->", $content . $scripts, $html);
echo $html;
