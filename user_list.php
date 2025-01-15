<?php
require_once("layout.php");
include "db_open.php";

$sql = "select * from users";
$sql_res = $dbh->query($sql);

// ユーザーリストのHTML
$user_list = "";
while ($record = $sql_res->fetch()) {
    $icon = htmlspecialchars($record['icon'], ENT_QUOTES, 'UTF-8');
    $user = htmlspecialchars($record['name'], ENT_QUOTES, 'UTF-8');
    $nickname = htmlspecialchars($record['nickname'], ENT_QUOTES, 'UTF-8');

    $user_list .= <<< ___EOF___
        <li class="flex items-center mb-4">
            <img src="profile_pictures/$icon" alt="icon" class="w-12 h-12 rounded-full mr-4 aspect-square object-cover object-center">
            <span class="mr-4">$user</span>
            <span class="mr-4">($nickname)</span>
            <a href="" class="text-blue-500 hover:underline">投稿記事一覧</a>
        </li>
        <hr class="m-4">
    ___EOF___;
}

$content = <<< ___EOF___

    <h1 class="ml-16 text-xl">ユーザー一覧</h1>

    <!--ユーザー情報取得・表示-->
    <ul class="border-8 border-slate-300 rounded-md h-96 my-2 mx-2 p-2 text-xl overflow-auto">
        $user_list
    </ul>

___EOF___;

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
