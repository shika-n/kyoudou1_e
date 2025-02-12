<?php
enum Pages: string {
	case k_base_url = "/kyoudou1_e";

	case k_index = "/";
	case k_register = "/register.php";
	case k_login = "/login_page.php";
	case k_logout = "/logout.php";
	
	case k_profile_edit = "/edit_profile.php";
	case k_profile = "/profile.php";
	
	case k_okiniiri = "/followings.php";
	case k_friend_requests = "/friend_requests.php";

	case k_kensaku = "/tag_search.php";

	case k_user_list = "/user_list.php";
	
	case k_post = "/post.php";
	case k_post_detail = "/post_exclusive.php";

	case k_category_list = "/category_list.php";
	case k_category_posts = "/category_posts.php";

	function get_url() {
		if ($this === Pages::k_base_url) {
			return $this->value;
		}
		return Pages::k_base_url->value . $this->value;
	}
};

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
	if ($image["size"] > 5000000) {
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

function is_authenticated() {
	if (get_if_set("user_id", $_SESSION)) {
		return true;
	}
	return false;
}

function redirect_to(Pages $page) {
	header("Location: {$page->get_url()}", true, 303);
	exit;
}

function redirect_back() {
	header("Location: {$_SERVER['HTTP_REFERER']}", true, 303);
	exit;
}
