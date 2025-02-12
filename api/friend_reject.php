<?php
require_once("../db_open.php");
require_once("../util.php");
require_once("../models/follow_requests.php");

session_start();
if (!is_authenticated()) {
	echo "403 Forbidden";
	return http_response_code(403);
}

$user_id = $_SESSION["user_id"];
$user_id_from = get_if_set("request_from_user_id", $_GET);

delete_request($dbh, $user_id_from, $user_id);
