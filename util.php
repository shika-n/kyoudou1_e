<?php

function get_if_set($key, $arr, $default = null) {
	if (isset($arr[$key])) {
		return $arr[$key];
	}
	return $default;
}

function get_image_extension($image) {
	return array_search(mime_content_type($image["tmp_name"]), [
		"png" => "image/png",
		"jpg" => "image/jpeg",
		"gif" => "image/gif",
	]);
}

function check_uploadable_image($image) {
	if (getimagesize($image["tmp_name"]) === false) {
		return "ファイルのアップロードエラー";
	}
	if ($image["size"] > 500000) {
		return "ファイルのサイズが大きすぎます";
	}
	$extension = get_image_extension($image);
	if ($extension === false) {
		return "アップロード不可能な拡張子です";
	}
	return true;
}

function get_unique_image_name($image) {
	return time() . "_" . uniqid() . "_" . sha1_file($image["tmp_name"]) . "." . get_image_extension($image);
}
