<?php

function search_tags(PDO $dbh, $search) {
	$statement = $dbh->prepare("
		SELECT
			name,
			COUNT(post_id) AS 'frequency'
		FROM tags t
		LEFT OUTER JOIN post_tag pt ON t.tag_id = pt.tag_id
		WHERE name LIKE :search
		GROUP BY t.tag_id
	");
	$statement->bindValue(":search", "%$search%");
	$statement->execute();
	return $statement->fetchAll(PDO::FETCH_ASSOC);
}

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
