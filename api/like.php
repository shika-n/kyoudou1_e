<?php
require_once("../db_open.php");
require_once("../models/likes.php");
require_once("../models/unlikes.php");
require_once("../util.php");

session_start();
if (!is_authenticated()) {
	echo "403 Forbidden";
	return http_response_code(403);
}

// user_id + post_id 取得
$user_id = $_SESSION["user_id"];
$post_id = $_GET["post_id"];

if (!is_liked($dbh, $post_id)) {
	// いいね
	if (like($dbh, $user_id, $post_id)) {
		header("Content-Type: text/json;", true, 200);
		echo json_encode(["message" => "いいねしました"]);
	} else {
		header("Content-Type: text/json", true, 500);
		echo json_encode(["message" => "いいね失敗しました"]);
	}
} else {
	// いいね取り消し
	if (unlike($dbh, $user_id, $post_id)) {
		header("Content-Type: text/json", true, 200);
		echo json_encode(["message" => "いいね取り消しました"]);
	} else {
		header("Content-Type: text/json", true, 500);
		echo json_encode(["message" => "いいえの取り消しは失敗しました"]);
	}
}







