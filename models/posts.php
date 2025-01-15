<?php

function post(PDO $dbh, $user_id, $title, $content) {
	$statement = $dbh->prepare("INSERT INTO posts(user_id, title, content, created_at) VALUES (?, ?, ?, ?);");
	date_default_timezone_set("UTC");
	if ($statement->execute([$user_id, $title, $content, date("Y-m-d H:i:s")])) {
		return true;
	} else {
		return false;
	}
}

function get_posts(PDO $dbh) {
	$statement = $dbh->prepare("SELECT * FROM posts JOIN users ON users.user_id = posts.user_id ORDER BY created_at DESC;");
	$statement->execute();
	return $statement->fetchAll();
}

