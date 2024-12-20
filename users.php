<?php

function user_exists(PDO $dbh, $email) {
	$statement = $dbh->prepare("SELECT COUNT(user_id) FROM users WHERE email = ?");
	if ($statement->execute([$email])) {
		return $statement->fetchAll()[0][0] === 1;
	} else {
		return false;
	}
}

function register(PDO $dbh, $name, $nickname, $email, $hashed_password) {
	$statement = $dbh->prepare("INSERT INTO users (name, nickname, email, password) VALUES (?, ?, ?, ?)");
	if ($statement->execute([$name, $nickname, $email, $hashed_password])) {
		echo "Success";
	} else {
		echo "Failed";
	}
}
