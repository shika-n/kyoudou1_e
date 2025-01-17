<?php
require_once("db_open.php");
require_once("layout.php");
require_once("util.php");
require_once("models/posts.php");

session_start();

// ログインしていないとログインページに投げる
if (!get_if_set("user_id", $_SESSION)) {
	header("Location: login_page.php", true, 303);
	return;
}

date_default_timezone_set("UTC");
$target_timezone = new DateTimeZone("Asia/Tokyo");

$content = "";
$post_arr = get_posts($dbh);
foreach ($post_arr as $row) {
	$created_at = (new DateTime($row["created_at"]))->setTimezone($target_timezone)->format("Y-m-d H:i:s");
    $content .= <<<___EOF___
    <div class="border-2 rounded-lg border-black p-2 bg-slate-100">
        <div class="flex flex-row flex-wrap items-center">
            <div class="rounded-full">
                <img src="profile_pictures/{$row['icon']}" class="w-8 rounded-full aspect-square object-cover object-center">
            </div>
            <div class="flex flex-col flex-wrap ml-5 text-sm p-2 divide-y divide-black">
                <div class="font-semibold">
                    <p>{$row['nickname']}</p>
                </div>
                <div>
                    <p>{$created_at}</p>
                </div>
            </div>
        </div>
        <div class="font-semibold">
            <p>{$row['title']}</p>
        </div>
        <div class="leading-4">
            <p>{$row['content']}</p>
        </div>
    </div>
    ___EOF___;
}

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
