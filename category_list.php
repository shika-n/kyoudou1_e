<?php
require_once("db_open.php");
require_once("layout.php");
require_once("util.php");
require_once("models/categories.php");
require_once("templates.php");

if (!is_authenticated()) {
	redirect_to(Pages::k_login);
}

date_default_timezone_set("UTC");
$target_timezone = new DateTimeZone("Asia/Tokyo");

$categories = get_categories($dbh);
$pages = Pages::k_base_url;

$content = <<< ___EOF___
	<h1 class="text-xl">カテゴリー一覧</h1>
	<div class='flex flex-wrap gap-2'>
___EOF___;
foreach ($categories as $category) {
	$content .= <<< ___EOF___
		<a href="{$pages::k_category_posts->get_url()}?id={$category['category_id']}" class="min-w-20 p-4 text-center font-bold flex-grow rounded-lg bg-blue-300 active:bg-blue-400 transition-all hover:shadow-xl hover:-translate-y-1">
			{$category["category_name"]}
		</a>
	___EOF___;
}
$content .= "</div>";

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
