<?php
require_once("layout.php");
include "db_open.php";

if (!is_authenticated()) {
    redirect_to(Pages::k_login);
}

$sql = "SELECT * FROM users";
$sql_res = $dbh->query($sql);

$pages = Pages::k_base_url;
$current_user_id = $_SESSION["user_id"];

$user_list = "";
while ($record = $sql_res->fetch()) {
    $id = htmlspecialchars($record['user_id'], ENT_QUOTES, 'UTF-8');
    $icon = htmlspecialchars($record['icon'], ENT_QUOTES, 'UTF-8');
    $user = htmlspecialchars($record['name'], ENT_QUOTES, 'UTF-8');
    $nickname = htmlspecialchars($record['nickname'], ENT_QUOTES, 'UTF-8');

    $stmt = $dbh->prepare("SELECT COUNT(*) FROM follows WHERE user_id = ? AND user_id_target = ?");
    $stmt->execute([$current_user_id, $id]);
    $is_following = $stmt->fetchColumn() > 0;
//
    if ($current_user_id !== $id) {
        $follow_text = $is_following ? "フォロー解除" : "フォロー";
        $follow_class = $is_following ? "bg-red-400" : "bg-blue-200";

        $follow_button = <<<HTML
        <button class="follow-btn border-2 p-3 pl-12 pr-12 rounded-full absolute md:right-64 $follow_class" data-user-id="$id">
            $follow_text
        </button>
        HTML;
    } else {
        $follow_button = ""; 
    }
	$user_list .= <<<HTML
    <div class="flex flex-col md:flex-row flex-wrap items-left md:items-center align-middle mb-4">
        <div class="flex items-center w-full">
            <img src="profile_pictures/$icon" alt="icon" class="w-12 h-12 rounded-full mr-4 aspect-square object-cover object-center">
            <div class="flex flex-col md:flex-row flex-wrap items-baseline overflow-hidden">
                <a href="{$pages::k_profile->get_url()}?id=$id" class="w-full mr-2 font-bold hover:underline truncate">$nickname</a>
                <span class="w-full text-sm text-left text-gray-700 truncate">$user</span>
            </div>
            $follow_button
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
