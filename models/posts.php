<?php

function post(PDO $dbh, $user_id, $title, $content, $image) {
	$statement = $dbh->prepare("INSERT INTO posts(user_id, title, content, created_at, updated_at, image) VALUES (?, ?, ?, ?, ?, ?);");
	date_default_timezone_set("UTC");
	$now = date("Y-m-d H:i:s");
	if ($statement->execute([$user_id, $title, $content, $now, $now, $image])) {
		return $dbh->lastInsertId();
	} else {
		return false;
	}
}

function edit_post(PDO $dbh, $user_id, $post_id, $title, $content, $image) {
	$statement = $dbh->prepare("UPDATE posts SET title = ?, content = ?, image = ?, updated_at = ? WHERE user_id = ? AND post_id = ?;");
	date_default_timezone_set("UTC");
	$now = date("Y-m-d H:i:s");
	return $statement->execute([$title, $content, $image, $now, $user_id, $post_id]);
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

function get_comment_count(PDO $dbh, $post_id) {
	$statement = $dbh->prepare("SELECT COUNT(post_id) FROM posts WHERE reply_to = ?");
	$statement->execute([$post_id]);
	return $statement->fetchAll()[0][0];
}

function get_post_by_id(PDO $dbh, $user_id, $post_id) {
	$statement = $dbh->prepare("
		SELECT
			post_id,
			p.user_id,
			created_at,
			title,
			content,
			image,
			updated_at,
			reply_to,
			deleted_at,
			name,
			nickname,
			icon,
			(
				SELECT COUNT(l.user_id)
				FROM likes l
				WHERE l.post_id = p.post_id 
			) AS 'like_count',
			(
				SELECT COUNT(c.post_id)
				FROM posts c
				WHERE c.reply_to = p.post_id AND c.deleted_at IS NULL
			) AS 'comment_count',
			EXISTS(
				SELECT 1
				FROM likes
				WHERE p.post_id = post_id AND user_id = :user_id1
			) AS 'liked_by_user'
		FROM posts p
		JOIN users u ON u.user_id = p.user_id
		WHERE p.post_id = :post_id AND deleted_at IS NULL
		LIMIT 1
	");
	$statement->bindParam(":user_id1", $user_id);
	$statement->bindParam(":post_id", $post_id);
	$statement->execute();
	return $statement->fetch(PDO::FETCH_ASSOC);
}

function get_posts(PDO $dbh, $user_id, $limit, $offset, $sort_order) {
	if ($sort_order === "newest") {
		$sort_order_inject = "";
	} else {
		$sort_order_inject = "like_count DESC, ";
	}
	$statement = $dbh->prepare("
		SELECT
			p.post_id,
			p.user_id,
			created_at,
			title,
			content,
			image,
			updated_at,
			reply_to,
			deleted_at,
			u.name,
			nickname,
			icon,
			(
				SELECT COUNT(l.user_id)
				FROM likes l
				WHERE l.post_id = p.post_id 
			) AS 'like_count',
			(
				SELECT COUNT(c.post_id)
				FROM posts c
				WHERE c.reply_to = p.post_id AND c.deleted_at IS NULL
			) AS 'comment_count',
			EXISTS(
				SELECT 1
				FROM likes
				WHERE p.post_id = post_id AND user_id = :user_id1
			) AS 'liked_by_user',
			GROUP_CONCAT(t.name) AS 'tags'
		FROM posts p
		JOIN users u ON u.user_id = p.user_id
		LEFT OUTER JOIN post_tag pt ON p.post_id = pt.post_id
		LEFT OUTER JOIN tags t ON pt.tag_id = t.tag_id
		WHERE p.reply_to IS NULL AND deleted_at IS NULL
		GROUP BY p.post_id
		ORDER BY $sort_order_inject created_at DESC
		LIMIT $limit OFFSET $offset
	");
	$statement->bindParam(":user_id1", $user_id);
	$statement->execute();

	$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

	$comment_start_index = count($rows);
	for ($i = 0; $i < count($rows); ++$i) {
		if ($rows[$i]["reply_to"]) {
			$comment_start_index = $i;
			break;
		}
	}

	$comments = [];
	for ($i = $comment_start_index; $i < count($rows); ++$i) {
		$comments[$rows[$i]["reply_to"]][] = $rows[$i];
	}

	return [ array_splice($rows, 0, $comment_start_index), $comments ];
}

function get_posts_by_user(PDO $dbh, $auth_id, $user_id, $limit, $offset, $sort_order) {
	if ($sort_order === "newest") {
		$sort_order_inject = "";
	} else {
		$sort_order_inject = "like_count DESC, ";
	}
	$statement = $dbh->prepare("
		SELECT
			p.post_id,
			p.user_id,
			created_at,
			title,
			content,
			image,
			updated_at,
			reply_to,
			deleted_at,
			u.name,
			nickname,
			icon,
			(
				SELECT COUNT(l.user_id)
				FROM likes l
				WHERE l.post_id = p.post_id 
			) AS 'like_count',
			(
				SELECT COUNT(c.post_id)
				FROM posts c
				WHERE c.reply_to = p.post_id AND c.deleted_at IS NULL
			) AS 'comment_count',
			EXISTS(
				SELECT 1
				FROM likes
				WHERE p.post_id = post_id AND user_id = :auth_id1
			) AS 'liked_by_user',
			GROUP_CONCAT(t.name) AS 'tags'
		FROM posts p
		JOIN users u ON u.user_id = p.user_id
		LEFT OUTER JOIN post_tag pt ON p.post_id = pt.post_id
		LEFT OUTER JOIN tags t ON pt.tag_id = t.tag_id
		WHERE p.reply_to IS NULL AND p.user_id = :user_id AND deleted_at IS NULL
		GROUP BY p.post_id
		ORDER BY $sort_order_inject created_at DESC
		LIMIT :limit OFFSET :offset
	");
	$statement->bindParam(":auth_id1", $auth_id);
	$statement->bindParam(":user_id", $user_id);
	$statement->bindParam(":limit", $limit);
	$statement->bindParam(":offset", $offset);
	$statement->execute();
	
	$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

	$comment_start_index = count($rows);
	for ($i = 0; $i < count($rows); ++$i) {
		if ($rows[$i]["reply_to"]) {
			$comment_start_index = $i;
			break;
		}
	}

	$comments = [];
	for ($i = $comment_start_index; $i < count($rows); ++$i) {
		$comments[$rows[$i]["reply_to"]][] = $rows[$i];
	}

	return [ array_splice($rows, 0, $comment_start_index), $comments ];
}
