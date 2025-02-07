<?php
require_once("../db_open.php");
require_once("../util.php");
require_once("../models/tags.php");

session_start();
if (!is_authenticated()) {
	echo "403 Forbidden";
	return http_response_code(403);
}

$image = get_if_set("file", $_FILES);
if (get_if_set("tmp_name", $image)) {
	$is_uploadable = check_uploadable_image($image);
	if ($is_uploadable !== true) {
		header("Content-Type: text/json", true, 400);
		echo json_encode([
			"message" => "アップロードできないファイル"
		]);
		return;
	}
	$image_filename = get_unique_image_name($image);
	move_uploaded_file($image["tmp_name"], "../post_images/" . $image_filename);
	header("Content-Type: text/json", true, 200);
	echo json_encode([
		"message" => "OK",
		"filepath" => $image_filename
	]);
} else {
	header("Content-Type: text/json", true, 400);
	echo json_encode([
		"message" => "アップロード失敗しました"
	]);
}


