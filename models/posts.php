<?php

function post(PDO $dbh, $user_id, $title, $content, $image) {
	$statement = $dbh->prepare("INSERT INTO posts(user_id, title, content, created_at, updated_at, image) VALUES (?, ?, ?, ?, ?, ?);");
	date_default_timezone_set("UTC");
	$now = date("Y-m-d H:i:s");
	return $statement->execute([$user_id, $title, $content, $now, $now, $image]);
}

function edit_post(PDO $dbh, $post_id, $title, $content, $image) {
	$statement = $dbh->prepare("UPDATE posts SET title = ?, content = ?, image = ?, updated_at = ? WHERE post_id = ?;");
	date_default_timezone_set("UTC");
	$now = date("Y-m-d H:i:s");
	return $statement->execute([$title, $content, $image, $now, $post_id]);
}

function delete_post(PDO $dbh, $post_id) {
	$statement = $dbh->prepare("UPDATE posts SET deleted_at = ? WHERE post_id = ?;");
	date_default_timezone_set("UTC");
	$now = date("Y-m-d H:i:s");
	return $statement->execute([$now, $post_id]);
}

function comment(PDO $dbh, $user_id, $content, $reply_to) {
	$statement = $dbh->prepare("INSERT INTO posts(user_id, content, created_at, updated_at, reply_to) VALUES (?, ?, ?, ?, ?);");
	date_default_timezone_set("UTC");
	$now = date("Y-m-d H:i:s");
	return $statement->execute([$user_id, $content, $now, $now, $reply_to]);
}

function get_posts(PDO $dbh) {
	$statement = $dbh->prepare("SELECT * FROM posts JOIN users ON users.user_id = posts.user_id ORDER BY created_at DESC;");
	$statement->execute();
	return $statement->fetchAll();
}

function get_posts_by_user(PDO $dbh, $user_id) {
	$statement = $dbh->prepare("SELECT * FROM posts JOIN users ON users.user_id = posts.user_id WHERE posts.user_id = ? ORDER BY created_at DESC;");
	$statement->execute([$user_id]);
	return $statement->fetchAll();
}
