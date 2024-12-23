<?php

function get_user(PDO $dbh, $email) {
	$statement = $dbh->prepare("SELECT * FROM users WHERE email = ?;");
	if ($statement->execute([$email])) {
		return $statement->fetch();
	}
}
