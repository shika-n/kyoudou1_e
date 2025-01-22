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

function get_posts(PDO $dbh, $user_id) {
	$statement = $dbh->prepare("
		WITH RECURSIVE base AS (
			(
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
						WHERE c.reply_to = p.post_id
					) AS 'comment_count',
					EXISTS(
						SELECT 1
						FROM likes
						WHERE p.post_id = post_id AND user_id = :user_id1
					) AS 'liked_by_user'
				FROM posts p
				JOIN users u ON u.user_id = p.user_id
				WHERE p.reply_to IS NULL
				ORDER BY created_at DESC
				LIMIT 10
			)
			UNION ALL
			(
				SELECT
					p.post_id,
					p.user_id,
					p.created_at,
					p.title,
					p.content,
					p.image,
					p.updated_at,
					p.reply_to,
					p.deleted_at,
					u.name,
					u.nickname,
					u.icon,
					(
						SELECT COUNT(l.user_id)
						FROM likes l
						WHERE l.post_id = p.post_id 
					) AS 'like_count',
					(
						SELECT COUNT(c.post_id)
						FROM posts c
						WHERE c.reply_to = p.post_id
					) AS 'comment_count',
					EXISTS(
						SELECT 1
						FROM likes
						WHERE p.post_id = post_id AND user_id = :user_id2
					) AS 'liked_by_user'
				FROM posts p
				JOIN users u ON u.user_id = p.user_id
				JOIN base b ON b.post_id = p.reply_to
			)
		) SELECT * FROM base ORDER BY reply_to IS NULL, created_at DESC;
	");
	$statement->bindParam(":user_id1", $user_id);
	$statement->bindParam(":user_id2", $user_id);
	$statement->execute();
	return $statement->fetchAll();
}

function get_posts_by_user(PDO $dbh, $user_id) {
	$statement = $dbh->prepare("
		WITH RECURSIVE base AS (
			(
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
						WHERE c.reply_to = p.post_id
					) AS 'comment_count',
					EXISTS(
						SELECT 1
						FROM likes
						WHERE p.post_id = post_id AND user_id = :user_id1
					) AS 'liked_by_user'
				FROM posts p
				JOIN users u ON u.user_id = p.user_id
				WHERE p.reply_to IS NULL AND p.user_id = :user_id2
				ORDER BY created_at DESC
				LIMIT 10
			)
			UNION ALL
			(
				SELECT
					p.post_id,
					p.user_id,
					p.created_at,
					p.title,
					p.content,
					p.image,
					p.updated_at,
					p.reply_to,
					p.deleted_at,
					u.name,
					u.nickname,
					u.icon,
					(
						SELECT COUNT(l.user_id)
						FROM likes l
						WHERE l.post_id = p.post_id 
					) AS 'like_count',
					(
						SELECT COUNT(c.post_id)
						FROM posts c
						WHERE c.reply_to = p.post_id
					) AS 'comment_count',
					EXISTS(
						SELECT 1
						FROM likes
						WHERE p.post_id = post_id AND user_id = :user_id3
					) AS 'liked_by_user'
				FROM posts p
				JOIN users u ON u.user_id = p.user_id
				JOIN base b ON b.post_id = p.reply_to
			)
		) SELECT * FROM base ORDER BY reply_to IS NULL, created_at DESC;
	");
	$statement->bindParam(":user_id1", $user_id);
	$statement->bindParam(":user_id2", $user_id);
	$statement->bindParam(":user_id3", $user_id);
	$statement->execute();
	return $statement->fetchAll();
}
