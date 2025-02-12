<?php
require_once("../db_open.php");
require_once("../models/follows.php");
require_once("../util.php");
require_once("../models/follow_requests.php");

session_start();
if (!is_authenticated()) {
	echo "403 Forbidden";
	return http_response_code(403);
}

$user_id = $_SESSION["user_id"];
$user_id_from = get_if_set("request_from_user_id", $_GET);

if (is_request_sent($dbh, $user_id_from, $user_id)) {
    mutual_follow($dbh, $user_id, $user_id_from);
    delete_request($dbh, $user_id_from, $user_id);
    header("Content-Type: text/json;", true, 200);
    echo json_encode(["message" => "OK"]);
} else {
    echo json_encode(["message" => "NOT OK"]);
}
