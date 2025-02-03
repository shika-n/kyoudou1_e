<?php

function get_tag_id_or_create(PDO $dbh, $name) {
	$statement = $dbh->prepare("SELECT tag_id FROM tags WHERE name = ? LIMIT 1");
	$statement->execute([$name]);
	$tag_id = $statement->fetchAll(PDO::FETCH_ASSOC)[0]["tag_id"];
	
	if (!$tag_id) {
		$statement = $dbh->prepare("INSERT INTO tags(name) VALUES (?)");
		if ($statement->execute([$name])) {
			$tag_id = $dbh->lastInsertId("tag_id");
		} else {
			return false;
		}
	}

	return $tag_id;
}

function tag_post(PDO $dbh, $post_id, $tag_id) {
	$statement = $dbh->prepare("INSERT INTO post_tag(post_id, tag_id) VALUES (?, ?)");
	return $statement->execute([$post_id, $tag_id]);
}
