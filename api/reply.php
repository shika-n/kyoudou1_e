<?php
require_once("../db_open.php");
require_once("../models/posts.php");
require_once("../util.php");
require_once("../templates.php");

session_start();
if (!is_authenticated()) {
	echo "403 Forbidden";
	return http_response_code(403);
}

$user_id = $_SESSION["user_id"];
$body = json_decode(file_get_contents("php://input"), true);
$content = trim(get_if_set("content", $body));
$reply_to = trim(get_if_set("reply_to", $body));

if (mb_strlen($content) < 1 || mb_strlen($content) > 8192) {
	header("Content-Type: text/json", true, 400);
	echo json_encode([
		"message" => "コメントは1~8192文字以上で入力してください"
	]);
	return;
}

if (!$reply_to) {
	header("Content-Type: text/json", true, 400);
	echo json_encode([
		"message" => "エラー発生しました"
	]);
	return;
}

if (comment($dbh, $user_id, $content, $reply_to)) {
	$comment_id = $dbh->lastInsertId("post_id");
	$comment = get_post_by_id($dbh, $user_id, $comment_id);

	date_default_timezone_set("UTC");
	$target_timezone = new DateTimeZone("Asia/Tokyo");

	$comment_html = comment_panel($comment, $target_timezone);

	$new_comment_count = get_comment_count($dbh, $reply_to);

	header("Content-Type: text/json", true, 200);
	echo json_encode([
		"message" => "OK",
		"html" => $comment_html,
		"comment_count" => $new_comment_count
	]);
} else {
	header("Content-Type: text/json", true, 400);
	echo json_encode([
		"message" => "Failed"
	]);
}

