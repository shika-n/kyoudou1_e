<?php
require_once("../db_open.php");
require_once("../models/posts.php");
require_once("../util.php");

session_start();
if (!is_authenticated()) {
	echo "403 Forbidden";
	return http_response_code(403);
}

header("Content-Type: text/json", true, 200);
echo json_encode(get_posts($dbh, $_SESSION["user_id"]));
