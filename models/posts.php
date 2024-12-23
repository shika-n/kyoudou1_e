<?php

function post(PDO $dbh, $user_id, $title, $content) {
	$statement = $dbh->prepare("INSERT INTO posts(user_id, title, content) VALUES (?, ?, ?);");
	if ($statement->execute([$user_id, $title, $content])) {
		return true;
	} else {
		return false;
	}
}

function get_posts(PDO $dbh) {
	$statement = $dbh->prepare("SELECT * FROM posts;");
	return $statement->fetchAll();
}


