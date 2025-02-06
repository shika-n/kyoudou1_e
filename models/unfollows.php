<?php

function unfollow(PDO $dbh, $user_id, $user_id_target) {
	$statement = $dbh->prepare("delete from follows WHERE user_id = ? and user_id_target = ?");
	return $statement->execute([$user_id, $user_id_target]);
}