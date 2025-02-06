<?php
session_start();
header("Content-Type: application/json");

require_once("../db_open.php");
require_once("../models/follows.php");
require_once("../models/unfollows.php");
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

$is_following = is_following($dbh, $user_id, $user_id_target);

if ($is_following) {
    if (unfollow_user($dbh, $user_id, $user_id_target)) {
        echo json_encode(["success" => true, "message" => "フォロー解除", "following" => false]);
    } else {
        echo json_encode(["success" => false, "message" => "フォロー解除に失敗", "following" => true]);
    }
} else {
    if (follow_user($dbh, $user_id, $user_id_target)) {
        echo json_encode(["success" => true, "message" => "フォローしました", "following" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "フォローに失敗", "following" => false]);
    }
}
exit;
