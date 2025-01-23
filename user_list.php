<?php
require_once("layout.php");
include "db_open.php";

if (!is_authenticated()) {
	redirect_to(Pages::k_login);
}

$sql = "select * from users";
$sql_res = $dbh->query($sql);

$pages = Pages::k_base_url;

// ユーザーリストのHTML
$user_list = "";
while ($record = $sql_res->fetch()) {
    $id = htmlspecialchars($record['user_id'], ENT_QUOTES, 'UTF-8');
    $icon = htmlspecialchars($record['icon'], ENT_QUOTES, 'UTF-8');
    $user = htmlspecialchars($record['name'], ENT_QUOTES, 'UTF-8');
    $nickname = htmlspecialchars($record['nickname'], ENT_QUOTES, 'UTF-8');

	$user_list .= <<< ___EOF___
		<div class="flex flex-col md:flex-row flex-wrap items-left md:items-center align-middle mb-4">
			<div class="flex items-center w-full">
				<img src="profile_pictures/$icon" alt="icon" class="w-12 h-12 rounded-full mr-4 aspect-square object-cover object-center">
				<div class="flex flex-col md:flex-row flex-wrap items-baseline overflow-hidden">
					<a href="{$pages::k_profile->get_url()}?id=$id" class="w-full mr-2 font-bold hover:underline truncate">$nickname</a>
					<span class="w-full text-sm text-left text-gray-700 truncate">($user)</span>
				</div>
			</div>
		</div>
		<hr class="m-4">
	___EOF___;
}

$content = <<< ___EOF___

    <h1 class="text-xl">ユーザー一覧</h1>

    <!--ユーザー情報取得・表示-->
    <div class="my-2 mx-2 p-2 text-xl border border-2 border-gray-300 rounded-lg">
        $user_list
    </div>

___EOF___;

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
