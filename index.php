<?php
require_once("db_open.php");
require_once("layout.php");
require_once("models/posts.php");

session_start();
$content = "";
$post_arr = get_posts($dbh);
foreach ($post_arr as $row) {
    $content .= <<<___EOF___
    <div>
        <hr>
        <p>{$row['created_at']}</p>
        <p>{$row['title']}</p>
        <p>{$row['content']}</p>
        <hr>
    </div>
    ___EOF___;
}

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
