<?php
require_once("db_open.php");
require_once("layout.php");
require_once("util.php");
require_once("models/posts.php");
require_once("templates.php");

require("require_auth.php");

date_default_timezone_set("UTC");
$target_timezone = new DateTimeZone("Asia/Tokyo");

$content = "";
$post_arr = get_posts($dbh);
foreach ($post_arr as $row) {
	$content .= post_panel($row, $target_timezone);
}

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
