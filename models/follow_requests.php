<?php

function send_friend_request(PDO $dbh, $user_id, $user_id_target) {
	$statement = $dbh->prepare("
		INSERT INTO follow_requests VALUES (:user_id, :user_id_target)
	");
	return $statement->execute([$user_id, $user_id_target]);
}

function get_friend_requests(PDO $dbh, $user_id) {
	$statement = $dbh->prepare("
		SELECT
			u.user_id,
			u.name,
			u.nickname,
			u.icon
		FROM follow_requests fr
		JOIN users u ON fr.user_id = u.user_id
		WHERE fr.user_id_target = ?
	");
	$statement->execute([$user_id]);
	return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function delete_request(PDO $dbh, $user_id, $user_id_target) {
	$statement = $dbh->prepare("
		DELETE FROM follow_requests WHERE user_id = ? AND user_id_target = ?
	");
	return $statement->execute([$user_id, $user_id_target]);
}

function is_request_sent(PDO $dbh, $user_id, $user_id_target) {
	$statement = $dbh->prepare("
		SELECT 1 FROM follow_requests WHERE user_id = ? AND user_id_target = ?
	");
	$statement->execute([$user_id, $user_id_target]);
	return count($statement->fetchAll(PDO::FETCH_ASSOC)) === 1;
}
