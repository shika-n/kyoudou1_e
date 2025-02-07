<?php
require_once("db_open.php");
require_once("layout.php");
require_once("util.php");
require_once("models/posts.php");
require_once("models/categories.php");
require_once("templates.php");

if (!is_authenticated()) {
	redirect_to(Pages::k_login);
}

$category_id = get_if_set("id", $_GET);
$category = get_category_by_id($dbh, $category_id);
if ($category_id === null || count($category) === 0) {
	redirect_to(Pages::k_category_list);
}
$category = $category[0];

date_default_timezone_set("UTC");
$target_timezone = new DateTimeZone("Asia/Tokyo");

$content = "<h1 class='text-xl'>カテゴリー・{$category["category_name"]}</h1>";
$post_arr = get_posts_by_category($dbh, $_SESSION["user_id"], $category_id, 5, 0, get_if_set("sort_order", $_SESSION, "newest"));

$content .= sort_order_select();
foreach ($post_arr as $row) {
	$content .= post_panel($row, $target_timezone);
}
if (count($post_arr) === 0) {
	$content .= "<p class='text-sm text-gray-400 text-center'>{$category['category_name']}カテゴリーにポストはありません</p>";
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
