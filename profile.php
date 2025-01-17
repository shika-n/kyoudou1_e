<?php
require_once("layout.php"); // レイアウトテンプレート
include "db_open.php";  // データベース接続

// ** ユーザー情報取得と表示 **
$sql = "SELECT * FROM users LIMIT 1"; // ユーザー情報を1件取得
$sql_res = $dbh->query($sql);

// ユーザー情報を初期化
$name = "";  
$nickname = "";  
$icon = "";    

if ($record = $sql_res->fetch()) {
    $icon = htmlspecialchars($record['icon'], ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($record['name'], ENT_QUOTES, 'UTF-8');
    $nickname = htmlspecialchars($record['nickname'], ENT_QUOTES, 'UTF-8');
}

// ** 投稿一覧取得と表示 **
function get_posts($dbh) {
    $sql = "SELECT * FROM posts ORDER BY created_at DESC"; // 投稿を新しい順に取得
    $stmt = $dbh->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$content = "";
$post_arr = get_posts($dbh);

if (count($post_arr) === 0) {
    // 記事がない場合のメッセージ
    $content = <<<HTML
    <div class="text-center text-gray-500 p-4">
        <p>投稿された記事がありません。</p>
    </div>
    HTML;
} else {
    // 記事がある場合の表示
    foreach ($post_arr as $row) {
        // 投稿データをエスケープ処理
        $row['icon'] = htmlspecialchars($row['icon'], ENT_QUOTES, 'UTF-8');
        $row['nickname'] = htmlspecialchars($row['nickname'], ENT_QUOTES, 'UTF-8');
        $row['created_at'] = htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8');
        $row['title'] = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
        $row['content'] = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');

        $content .= <<<___EOF___
        <div class="my-1 border-2 rounded-lg border-black p-2 bg-slate-100">
            <div class="flex flex-row">
                <button class="rounded-full">
                    <img src="profile_pictures/{$row['icon']}" alt="アイコン" class="w-8">
                </button>
                <div class="flex flex-col ml-5 text-sm p-2 divide-y divide-black">
                    <div class="font-semibold">
                        <p>{$row['nickname']}</p>
                    </div>
                    <div>
                        <p>{$row['created_at']}</p>
                    </div>
                </div>
            </div>
            <div class="font-semibold">
                <p>{$row['title']}</p>
            </div>
            <div class="leading-4">
                <p>{$row['content']}</p>
            </div>
        </div>
        ___EOF___;
    }
}

// ** HTML出力生成（ユーザー情報） **
$user_info = <<<HTML
<div class="container mx-auto p-4">
    <!-- ユーザー情報 -->
    <div class="rounded-lg p-4 mb-6 flex items-center">
        <img src="$icon" alt="アイコン" class="w-24 h-24 rounded-full mr-4">
        <div class="border-2 border-gray-300 rounded-lg p-4 w-full">
            <p class="font-bold text-lg">名前: $name</p>
            <hr>
            <p class="font-bold text-lg">ニックネーム: $nickname</p>
        </div>
    </div>
</div>
HTML;

// ** 投稿一覧HTML生成 **
$post_section = <<<HTML
<hr class="mt-8">
<div class="container mx-auto p-4">
    <!-- 投稿一覧 -->
    <h2 class="text-xl font-bold mb-4 text-center">記事一覧</h2>
    <div class="post-list border-2 border-gray-300 rounded-lg p-4">
        $content
    </div>
</div>
HTML;

// ** レイアウトに組み込み＆出力 **
$html = str_replace("<!-- CONTENT -->", $user_info, $html);
echo $html;

// ** 記事一覧を枠外に出力（別エリア） **
echo $post_section;
?>
