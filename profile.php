<?php
require_once("layout.php"); // レイアウトテンプレート
include "db_open.php";  // データベース接続
include("models/posts.php");
include("models/users.php");

require("require_auth.php");

$target_id = get_if_set("id", $_GET);
if (!$target_id) {
	$target_id = $_SESSION["user_id"];
}

$content = "";
$user = get_user_by_id($dbh, $target_id);
$icon = $user["icon"];
$name = htmlspecialchars($user["name"], ENT_QUOTES, "UTF-8");
$nickname = htmlspecialchars($user["nickname"], ENT_QUOTES, "UTF-8");
$post_arr = get_posts_by_user($dbh, $target_id);

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
    foreach ($post_arr as $row) {
        // 投稿データをエスケープ処理
		$id = htmlspecialchars($row["user_id"], ENT_QUOTES, "UTF-8");
		$row['icon'] = htmlspecialchars($row['icon'], ENT_QUOTES, 'UTF-8');
		$row['nickname'] = htmlspecialchars($row['nickname'], ENT_QUOTES, 'UTF-8');
		$row['created_at'] = htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8');
		$row['title'] = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
		$row['content'] = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
		$created_at = (new DateTime($row["created_at"]))->setTimezone($target_timezone)->format("Y-m-d H:i:s");
		$content .= <<<___EOF___
			<div class="border-2 rounded-lg border-black p-2 bg-slate-100">
				<div class="flex flex-row flex-wrap items-center">
					<div class="rounded-full">
						<img src="profile_pictures/{$row['icon']}" class="w-8 rounded-full aspect-square object-cover object-center">
					</div>
					<div class="flex flex-col flex-wrap ml-5 text-sm p-2 divide-y divide-black">
						<div class="font-semibold">
							<a href="profile.php?id=$id">{$row['nickname']}</a>
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
					<p class="text-wrap break-all hover:line-clamp-none text-ellipsis overflow-hidden line-clamp-3">{$row['content']}</p>
				</div>
			</div>
		___EOF___;
    }
}

// ** HTML出力生成（ユーザー情報） **
$user_info = <<<HTML
<div class="container mx-auto p-4">
    <!-- ユーザー情報 -->
    <div class="rounded-lg p-4 flex items-center">
        <img src="$icon" alt="アイコン" class="w-24 h-24 rounded-full mr-4 aspect-square object-cover object-center">
        <div class="border-2 border-gray-300 rounded-lg p-4 min-w-0 w-full">
            <p class="font-bold text-lg overflow-hidden text-ellipsis">名前: $name</p>
            <hr>
            <p class="font-bold text-lg overflow-hidden text-ellipsis">ニックネーム: $nickname</p>
        </div>
    </div>
</div>
HTML;

// ** 投稿一覧HTML生成 **
$post_section = <<<HTML
<hr>
<div class="container mx-auto p-4">
    <!-- 投稿一覧 -->
    <h2 class="text-xl font-bold mb-4 text-center">記事一覧</h2>
    <div class="post-list border-2 border-gray-300 rounded-lg p-4 flex flex-col gap-2">
        $content
    </div>
</div>
HTML;

// ** レイアウトに組み込み＆出力 **
$html = str_replace("<!-- CONTENT -->", $user_info . $post_section, $html);
echo $html;
?>
