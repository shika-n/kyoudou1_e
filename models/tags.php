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
		ORDER BY frequency DESC
		LIMIT 20
	");
	$statement->bindValue(":search", "%$search%");
	$statement->execute();
	return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_tag_id_or_create(PDO $dbh, $name) {
	$statement = $dbh->prepare("SELECT tag_id FROM tags WHERE name = ? LIMIT 1");
	$statement->execute([$name]);
	$tag_id = get_if_set("tag_id", $statement->fetchAll(PDO::FETCH_ASSOC)[0]);
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

function is_post_tagged(PDO $dbh, $post_id, $tag_id) {
	$statement = $dbh->prepare("SELECT 1 FROM post_tag WHERE post_id = ? AND tag_id = ?");
	$statement->execute([$post_id, $tag_id]);
	return $statement->fetchAll(PDO::FETCH_COLUMN)[0] === 1;
}

function tag_post(PDO $dbh, $post_id, $tag_id) {
	if (is_post_tagged($dbh, $post_id, $tag_id)) {
		return true;
	}
	$statement = $dbh->prepare("INSERT INTO post_tag(post_id, tag_id) VALUES (?, ?)");
	return $statement->execute([$post_id, $tag_id]);
}

function remove_unlisted_tag(PDO $dbh, $post_id, $tags) {
	if (count($tags) === 0) {
		$placeholder = "";
	} else {
		$placeholder = "AND tag_id NOT IN (";
		for ($i = 0; $i < count($tags); ++$i) {
			if ($i > 0) {
				$placeholder .= ", ";
			}
			$placeholder .= "?";
		}
		$placeholder .= ")";
	}
	$statement = $dbh->prepare("DELETE FROM post_tag WHERE post_id = ? $placeholder");
	$result = $statement->execute([$post_id, ...$tags]);

	// $statement = $dbh->prepare("DELETE FROM tags WHERE tag_id NOT IN (SELECT DISTINCT tag_id FROM post_tag)");
	// $statement->execute([$post_id, ...$tags]);
	return $result;
}
