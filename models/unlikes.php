<?php

function unlike(PDO $dbh, $user_id, $post_id) {
	$statement = $dbh->prepare("delete from likes WHERE user_id = ? and post_id = ?");
	return $statement->execute([$user_id, $post_id]);
}
