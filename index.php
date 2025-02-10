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
$post_arr = get_posts($dbh, $_SESSION["user_id"], 5, 0, get_if_set("sort_order", $_SESSION, "newest"));

$content .= sort_order_select();
$content .= <<< ___EOF___
	<div class="flex">
		<button type="button" class="px-2 py-1 bg-gray-200 hover:bg-gray-100 border border-black rounded-t-md" onclick="changeTopTab('timeline')">すべて</button>
		<button type="button" class="px-2 py-1 bg-gray-200 hover:bg-gray-100 border border-black rounded-t-md" onclick="changeTopTab('followings')">フォローした投稿者から</button>
		<button type="button" class="px-2 py-1 bg-gray-200 hover:bg-gray-100 border border-black rounded-t-md" onclick="changeTopTab('liked')">いいね！した記事</button>
	</div>
___EOF___;
foreach ($post_arr as $row) {
	$content .= post_panel($row, $target_timezone);
}

$scripts = <<< ___EOF___
	<script src='js/reach_bottom_action.js'></script>
	<script src='js/top_tab.js'></script>
	<script>
		reachBottomActionQuery.set("type", "timeline");
	</script>
___EOF___;

hide_markdown_image();
$html = str_replace("<!-- CONTENT -->", $content . $scripts, $html);
echo $html;
