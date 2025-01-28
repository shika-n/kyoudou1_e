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

$body = json_decode(file_get_contents("php://input"), true);
$sort_order = get_if_set("sort_order", $body);

$options = [ "newest", "likes" ];
if (array_search($sort_order, $options) !== null) {
	$_SESSION["sort_order"] = $sort_order;
	header("Content-Type: text/json", true, 200);
	echo json_encode([
		"message" => "OK"
	]);
} else {
	header("Content-Type: text/json", true, 400);
	echo json_encode([
		"message" => "選択した並び順は無効です"
	]);
}
