<?php
require_once("layout.php");
include "db_open.php";
require_once("models/follows.php");

if (!is_authenticated()) {
    redirect_to(Pages::k_login);
}

$pages = Pages::k_base_url;
$current_user_id = $_SESSION["user_id"];

$sql_res = get_followings($dbh, $current_user_id);
$user_list = "";
foreach ($sql_res as $record) {
    $id = htmlspecialchars($record['user_id'], ENT_QUOTES, 'UTF-8');
    $icon = htmlspecialchars($record['icon'], ENT_QUOTES, 'UTF-8');
    $user = htmlspecialchars($record['name'], ENT_QUOTES, 'UTF-8');
    $nickname = htmlspecialchars($record['nickname'], ENT_QUOTES, 'UTF-8');

	$is_following = is_following($dbh, $_SESSION["user_id"], $id);

    if ($current_user_id !== $id) {
        $follow_text = $is_following ? "フォロー解除" : "フォロー";
        $follow_class = $is_following ? "bg-red-400" : "bg-blue-200";

        $follow_button_pc = <<<HTML
        <button class="follow-btn border-2 p-2 md:p-3 md:pl-12 md:pr-12 rounded-full absolute md:right-32 lg:right-32 xl:right-48 hidden md:block $follow_class" data-user-id="$id">
            $follow_text
        </button>
        HTML;

        $follow_button_mobile = <<<HTML
        <button class="follow-btn border-2 p-2 rounded-full $follow_class block md:hidden mt-2 md:mt-0" data-user-id="$id">
            $follow_text
        </button>
        HTML;
    } else {
        $follow_button_pc = "";
        $follow_button_mobile = "";
    }
    $user_list .= <<<HTML
        <div class="flex flex-col md:flex-row flex-wrap items-left md:items-center align-middle mb-4">
        <div class="flex items-center w-full">
            <img src="profile_pictures/$icon" alt="icon" class="w-12 h-12 rounded-full mr-4 aspect-square object-cover object-center">
            <div class="flex flex-col md:flex-row flex-wrap items-baseline overflow-hidden">
                <a href="{$pages::k_profile->get_url()}?id=$id" class="w-full mr-2 font-bold hover:underline truncate">$nickname</a>
                <span class="w-full text-sm text-left text-gray-700 truncate">$user</span>
            </div>
            $follow_button_pc
        </div>

        <div class="w-full md:hidden flex justify-start">
            $follow_button_mobile
        </div>

    </div>
    <hr class="m-4">
    HTML;
}
$content = <<<HTML
<h1 class="text-xl">ユーザー一覧</h1>

<div class="my-2 mx-2 p-2 text-xl border border-2 border-gray-300 rounded-lg">
    $user_list
</div>
HTML;

$html = str_replace("<!-- CONTENT -->", $content, $html);

echo $html;
?>
<script src="js/follows.js"></script>
</body>
</html>
