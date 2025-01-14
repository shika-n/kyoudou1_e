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

$content = "";
$post_arr = get_posts($dbh);
foreach ($post_arr as $row) {
    $content .= <<<___EOF___
    <div class="my-1">
        <div class="flex flex-row">
            <button class="rounded-full">
                <img src="profile_pictures/{$row['icon']}" class="w-8">
            </button>
            <div class="flex flex-col ml-10 text-sm border-2">
                <p>{$row['nickname']}</p>
                <p>{$row['created_at']}</p>
            </div>
        </div>
        <p>{$row['title']}</p>
        <p>{$row['content']}</p>
    </div>
    ___EOF___;
}

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
