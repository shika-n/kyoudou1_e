<?php

function like(PDO $dbh, $user_id, $post_id) {
	$statement = $dbh->prepare("INSERT INTO likes VALUES (?, ?)");
	return $statement->execute([$user_id, $post_id]);
}

function is_liked(PDO $dbh, $post_id) {
	$statement = $dbh->prepare("SELECT COUNT(user_id) FROM likes WHERE user_id = ? AND post_id = ?");
	if ($statement->execute([$_SESSION["user_id"], $post_id])) {
		return $statement->fetchAll()[0][0] === 1;
	} else {
		return false;
	}
}