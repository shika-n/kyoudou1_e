<?php
function is_following($dbh, $user_id, $user_id_target) {
    $stmt = $dbh->prepare("SELECT COUNT(*) FROM follows WHERE user_id = ? AND user_id_target = ?");
    $stmt->execute([$user_id, $user_id_target]);
    return $stmt->fetchColumn() > 0;
}

function follow_user($dbh, $user_id, $user_id_target) {
    $stmt = $dbh->prepare("INSERT INTO follows (user_id, user_id_target) VALUES (?, ?)");
    return $stmt->execute([$user_id, $user_id_target]);
}

function unfollow_user($dbh, $user_id, $user_id_target) {
    $stmt = $dbh->prepare("DELETE FROM follows WHERE user_id = ? AND user_id_target = ?");
    return $stmt->execute([$user_id, $user_id_target]);
}

function get_followers(PDO $dbh, $user_id) {
	$statement = $dbh->prepare("
		SELECT
			u.user_id,
			u.name,
			u.nickname,
			u.email,
			u.icon
		FROM users u
		JOIN follows f ON u.user_id = f.user_id
		WHERE f.user_id_target = :user_id;
	");
	$statement->bindParam(":user_id", $user_id);
	$statement->execute();
	return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_followings(PDO $dbh, $user_id) {
	$statement = $dbh->prepare("
		SELECT
			u.user_id,
			u.name,
			u.nickname,
			u.email,
			u.icon
		FROM users u
		JOIN follows f ON u.user_id = f.user_id_target
		WHERE f.user_id = :user_id;
	");
	$statement->bindParam(":user_id", $user_id);
	$statement->execute();
	return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_ff_count(PDO $dbh, $user_id) {
	$statement = $dbh->prepare("
		SELECT
		(SELECT COUNT(user_id_target) FROM follows WHERE user_id = ?) AS 'followings',
		(SELECT COUNT(user_id) FROM follows WHERE user_id_target = ?) AS 'followers';	
	");
	$statement->execute([$user_id, $user_id]);
	return $statement->fetchAll(PDO::FETCH_ASSOC)[0];
}
?>
