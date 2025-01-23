<?php

function like(PDO $dbh, $user_id, $post_id) {
	$statement = $dbh->prepare("INSERT INTO likes VALUES (?, ?)");
	return $statement->execute([$user_id, $post_id]);
}
