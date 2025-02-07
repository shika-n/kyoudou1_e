<?php
require_once("layout.php");
include "db_open.php";

if (!is_authenticated()) {
    redirect_to(Pages::k_login);
}
$sql = "SELECT * FROM users";
$sql_res = $dbh->query($sql);

$pages = Pages::k_base_url;
$current_user_id = intval($_SESSION["user_id"]);

$user_list = "";
while ($record = $sql_res->fetch()) {
    $id = intval($record['user_id']);
    $icon = htmlspecialchars($record['icon'], ENT_QUOTES, 'UTF-8');
    $user = htmlspecialchars($record['name'], ENT_QUOTES, 'UTF-8');
    $nickname = htmlspecialchars($record['nickname'], ENT_QUOTES, 'UTF-8');

    $follow_button_pc = "";    // PC
    $follow_button_mobile = "";    // レスポンシブデザイン

    if ($current_user_id !== $id) {
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM follows WHERE user_id = ? AND user_id_target = ?");
        $stmt->execute([$current_user_id, $id]);
        $is_following = $stmt->fetchColumn() > 0;

        $follow_text = $is_following ? "フォロー解除" : "フォロー";    // テキスト動き
        $follow_class = $is_following ? "bg-red-400" : "bg-blue-200";    //ボタンカラー

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