<?php
require_once("layout.php");
include "db_open.php";
require_once("models/follows.php");
require_once("models/follow_requests.php");

if (!is_authenticated()) {
    redirect_to(Pages::k_login);
}
$pages = Pages::k_base_url;
$current_user_id = intval($_SESSION["user_id"]);

$sql_res = get_friend_requests($dbh, $current_user_id);
$user_list = "";
foreach ($sql_res as $record) {
    $id = htmlspecialchars($record['user_id'], ENT_QUOTES, 'UTF-8');
    $icon = htmlspecialchars($record['icon'], ENT_QUOTES, 'UTF-8');
    $user = htmlspecialchars($record['name'], ENT_QUOTES, 'UTF-8');
    $nickname = htmlspecialchars($record['nickname'], ENT_QUOTES, 'UTF-8');

    $is_pending = is_request_sent($dbh, $id, $current_user_id);
    $accept_text = $is_pending? "許可" : "許可しました！";
    $deny_text = $is_pending? "拒否" : "拒否しました！";
    $accept_class = $is_pending ? "bg-blue-200" : "bg-gray-200";
    $deny_class = $is_pending ? "bg-red-400" : "bg-gray-200";
    $accept_button_pc = <<<HTML
    <button class="accept-btn border-2 p-2 md:p-3 md:pl-12 md:pr-12 rounded-full min-w-fit hidden md:block $accept_class" data-user-id="$id">
        $accept_text
    </button>
    HTML;

    $accept_button_mobile = <<<HTML
    <button class="accept-btn border-2 p-2 rounded-full $accept_class block md:hidden mt-2 md:mt-0" data-user-id="$id">
        $accept_text
    </button>
    HTML;

    $deny_button_pc = <<<HTML
    <button class="deny-btn border-2 p-2 md:p-3 md:pl-12 md:pr-12 rounded-full min-w-fit hidden md:block $deny_class" data-user-id="$id">
        $deny_text
    </button>
    HTML;

    $deny_button_mobile = <<<HTML
    <button class="deny-btn border-2 p-2 rounded-full $deny_class block md:hidden mt-2 md:mt-0" data-user-id="$id">
        $deny_text
    </button>
    HTML;
    
    $user_list .= <<<HTML
    <div class="flex flex-col md:flex-row flex-wrap items-left md:items-center align-middle mb-4">
        <div class="flex items-center w-full">
            <img src="profile_pictures/$icon" alt="icon" class="w-12 h-12 rounded-full mr-4 aspect-square object-cover object-center">
            <div class="flex flex-col md:flex-row flex-wrap flex-grow items-baseline overflow-hidden">
                <a href="{$pages::k_profile->get_url()}?id=$id" class="w-full mr-2 font-bold hover:underline truncate">$nickname</a>
                <span class="w-full text-sm text-left text-gray-700 truncate">$user</span>
            </div>
            $accept_button_pc
            $deny_button_pc
        </div>
        
        <div class="w-full md:hidden flex justify-start">
            $accept_button_mobile
            $deny_button_mobile
        </div>
    </div>
    <hr class="m-4">
    HTML;
}


$content = <<<HTML
<h1 class="text-xl">フレンドリクエスト</h1>

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
