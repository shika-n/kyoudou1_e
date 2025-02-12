<?php
require_once("layout.php");
include "db_open.php";  
include("models/posts.php");
include("models/users.php");
require_once("models/follows.php");
require_once("models/follow_requests.php");
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
$post_arr = get_posts_by_user($dbh, $_SESSION["user_id"], $target_id, 5, 0, get_if_set("sort_order", $_SESSION, "newest"));

$is_following = false;
$is_followed = false;

if ($target_id != $_SESSION["user_id"]) {
	$is_following = is_following($dbh, $_SESSION["user_id"], $target_id);
	$is_followed = is_following($dbh, $target_id, $_SESSION["user_id"]);
	$request_sent = is_request_sent($dbh, $_SESSION["user_id"], $target_id);
	$request_received = is_request_sent($dbh, $target_id, $_SESSION["user_id"]);
}

$follow_text = $is_following && $is_followed ? "フレンドから解除" : "フレンド申請";
$follow_class = $is_following && $is_followed ? "bg-red-400" : "bg-blue-200";

$ff_count = get_ff_count($dbh, $target_id);

if (count($post_arr) === 0) {
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
        $content .= post_panel($row, $target_timezone);
    }
}
$pages = Pages::k_base_url;

$user_info = <<<HTML
<div class="container mx-auto p-4">
    <div class="flex flex-col md:flex-row gap-4 rounded-lg p-4 items-center">
        <img src="profile_pictures/$icon" alt="アイコン" class="w-24 h-24 rounded-full aspect-square object-cover object-center">
        
        <div class="flex flex-col lg:flex-row items-center w-full border-2 border-gray-300 rounded-lg p-4">
            <div class="flex-1 min-w-0">
                <p class="font-bold text-lg overflow-hidden text-ellipsis">名前: $name</p>
                <hr>
                <p class="font-bold text-lg overflow-hidden text-ellipsis">ニックネーム: $nickname</p>
				<hr>
				<div class="flex gap-2 font-bold">
					<span>{$ff_count["followings"]} フレンド</span>
				</div>
            </div>
            
HTML;

$pc_friend_button = "";
$mobile_friend_button = "";
if ($target_id != $_SESSION["user_id"]) {
    $pc_friend_button = <<<HTML
        <button class="follow-btn border-2 p-3 pl-12 pr-12 rounded-full md:ml-4 md:static md:mt-0 mt-4 $follow_class hidden md:block" data-user-id="$target_id">
            $follow_text
        </button>
HTML;
    $mobile_friend_button = <<<HTML
    <div class="flex justify-center md:hidden mt-4">
        <button class="follow-btn border-2 p-3 pl-12 pr-12 rounded-full $follow_class block md:hidden" data-user-id="$target_id">
            $follow_text
        </button>
    </div>
HTML;
	if ($request_sent) {
		$follow_text = "申請済み";
		$follow_class = "bg-gray-300";
		$pc_friend_button = <<<HTML
			<button class="follow-btn border-2 p-3 pl-12 pr-12 rounded-full md:ml-4 md:static md:mt-0 mt-4 $follow_class hidden md:block" disabled>
				$follow_text
			</button>
		HTML;
		$mobile_friend_button = <<<HTML
			<div class="flex justify-center md:hidden mt-4">
				<button class="follow-btn border-2 p-3 pl-12 pr-12 rounded-full $follow_class block md:hidden" data-user-id="$target_id" disabled>
					$follow_text
				</button>
			</div>
		HTML;
	} else if ($request_received) {
		$follow_text = "フレンド申請一覧へ";
		$follow_class = "bg-gray-300";
		$friend_requests_url = Pages::k_friend_requests->get_url();
		$pc_friend_button = <<<HTML
			<a href="$friend_requests_url" class="follow-btn border-2 p-3 pl-12 pr-12 rounded-full md:ml-4 md:static md:mt-0 mt-4 $follow_class hidden md:block">
				$follow_text
			</a>
		HTML;
		$mobile_friend_button = <<<HTML
			<div class="flex justify-center md:hidden mt-4">
				<a href="$friend_requests_url" class="follow-btn border-2 p-3 pl-12 pr-12 rounded-full $follow_class block md:hidden" data-user-id="$target_id">
					$follow_text
				</a>
			</div>
		HTML;
	}
}

$user_info .= $pc_friend_button;
$user_info .= "</div><!-- EDIT --></div></div>";
$user_info .= $mobile_friend_button;

if ($target_id == $_SESSION["user_id"]) {
    $user_info = str_replace("<!-- EDIT -->", "<a href='{$pages::k_profile_edit->get_url()}'>編集</a>", $user_info);
}

$post_section = <<<HTML
<hr>
<h2 class="text-xl font-bold mb-4 text-center">記事一覧</h2>
$content
HTML;
$html = str_replace("<!-- CONTENT -->", $user_info . $post_section, $html);

$scripts = <<< ___EOF___
	<script src='js/reach_bottom_action.js'></script>

	<script>
		reachBottomActionQuery.set("type", "profile");
		reachBottomActionQuery.set("id", $target_id);
	</script>
___EOF___;

hide_markdown_image();
$html = str_replace("<!-- CONTENT -->", $user_info . $post_section . $scripts, $html);
echo $html;
?>
<script src="js/follows.js"></script>
</body>
</html>
