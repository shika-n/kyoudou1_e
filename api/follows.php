<?php
session_start();
header("Content-Type: application/json");

require_once("../db_open.php");
require_once("../models/follows.php");
require_once("../models/unfollows.php");
require_once("../models/follow_requests.php");
require_once("../util.php");

if (!is_authenticated()) {
	echo "403 Forbidden";
	return http_response_code(403);
}

$user_id = $_SESSION["user_id"];
$user_id_target = $_POST["user_id_target"] ?? null;

if (!$user_id_target || $user_id_target == $user_id) {
    echo json_encode(["success" => false, "message" => "無効なリクエスト"]);
    exit;
}

$is_following = is_following($dbh, $user_id, $user_id_target) ;
$is_followed = is_following($dbh, $user_id_target, $user_id);
$request_sent_or_received = is_request_sent($dbh, $user_id, $user_id_target) || is_request_sent($dbh, $user_id_target, $user_id);

if ($is_following) {
	$dbh->beginTransaction();
    if (mutual_unfollow($dbh, $user_id, $user_id_target)) {
		$dbh->commit();
        echo json_encode(["success" => true, "message" => "フレンド解除", "is_friend" => false]);
    } else {
		$dbh->rollBack();
        http_response_code(400);
		echo json_encode(["success" => false, "message" => "解除失敗", "is_friend" => $is_following]);
    }
} else {
	if (!$request_sent_or_received) {
		if (send_friend_request($dbh, $user_id, $user_id_target)) {
			echo json_encode(["success" => true, "message" => "フレンド申請送信しました", "is_friend" => $is_following]);
		} else {
			http_response_code(400);
			echo json_encode(["success" => false, "message" => "フレンド申請失敗しました", "is_friend" => $is_following]);
		}
	} else {
        http_response_code(400);
		echo json_encode(["success" => false, "message" => "すでに申請済みです", "is_friend" => $is_following]);
	}
}//
exit;
