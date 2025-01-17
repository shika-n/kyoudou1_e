<?php
require_once("layout.php");
include "db_open.php";

$sql = "select * from users";
$sql_res = $dbh->query($sql);

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
					<a href="profile.php?id=$id" class="w-full mr-2 font-bold hover:underline truncate">$user</a>
					<span class="w-full text-sm text-left text-gray-700 truncate">($nickname)</span>
				</div>
			</div>
			<!-- <a href="profile.php" class="block w-min-40 text-blue-500 align-middle text-center md:text-left shrink-0 whitespace-nowrap hover:underline">投稿記事一覧</a> -->
		</div>
		<hr class="m-4">
	___EOF___;
}

$content = <<< ___EOF___

    <h1 class="text-xl">ユーザー一覧</h1>

    <!--ユーザー情報取得・表示-->
    <div class="my-2 mx-2 p-2 text-xl border border-4">
        $user_list
    </div>

___EOF___;

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
