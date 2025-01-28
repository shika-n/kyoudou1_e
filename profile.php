<?php
require_once("layout.php"); // レイアウトテンプレート
include "db_open.php";  // データベース接続
include("models/posts.php");
include("models/users.php");
require_once("templates.php");

if (!is_authenticated()) {
	redirect_to(Pages::k_login);
}

$target_id = get_if_set("id", $_GET);
if (!$target_id) {
	$target_id = $_SESSION["user_id"];
}

$content = "";
$user = get_user_by_id($dbh, $target_id);
$icon = $user["icon"];
$name = htmlspecialchars($user["name"], ENT_QUOTES, "UTF-8");
$nickname = htmlspecialchars($user["nickname"], ENT_QUOTES, "UTF-8");
[ $post_arr, $comments ] = get_posts_by_user($dbh, $_SESSION["user_id"], $target_id, 5, 0, get_if_set("sort_order", $_SESSION, "newest"));

if (count($post_arr) === 0) {
    // 記事がない場合のメッセージ
    $content = <<<HTML
    <div class="text-center text-gray-500 p-4">
        <p>投稿された記事がありません。</p>
    </div>
    HTML;
} else {
	date_default_timezone_set("UTC");
	$target_timezone = new DateTimeZone("Asia/Tokyo");
    // 記事がある場合の表示
	$content .= sort_order_select();
	foreach ($post_arr as $row) {
		$content .= post_panel($row, $target_timezone, get_if_set($row["post_id"], $comments));
	}
}

$pages = Pages::k_base_url;

// ** HTML出力生成（ユーザー情報） **
$user_info = <<<HTML
<div class="container mx-auto p-4">
    <!-- ユーザー情報 -->
    <div class="rounded-lg p-4 flex items-center">
        <img src="profile_pictures/$icon" alt="アイコン" class="w-24 h-24 rounded-full mr-4 aspect-square object-cover object-center">
        <div class="border-2 border-gray-300 rounded-lg p-4 min-w-0 w-full">
            <p class="font-bold text-lg overflow-hidden text-ellipsis">名前: $name</p>
            <hr>
            <p class="font-bold text-lg overflow-hidden text-ellipsis">ニックネーム: $nickname</p>
        </div>
		<a href="{$pages::k_profile_edit->get_url()}" class="ml-4">編集</a>
    </div>
</div>
HTML;

// ** 投稿一覧HTML生成 **
$post_section = <<<HTML
<hr>
<!-- 投稿一覧 -->
<h2 class="text-xl font-bold mb-4 text-center">記事一覧</h2>
$content
HTML;

// ** レイアウトに組み込み＆出力 **
$html = str_replace("<!-- CONTENT -->", $user_info . $post_section, $html);
echo $html;
?>
