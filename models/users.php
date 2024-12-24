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
		return true;
	} else {
		return false;
	}
}

function get_users(PDO $dbh) {
	$statement = $dbh->prepare("SELECT * FROM users;");
	return $statement->fetchAll();
}

function get_user_by_email(PDO $dbh, $email) {
	$statement = $dbh->prepare("SELECT * FROM users WHERE email = ?;");
	if ($statement->execute([$email])) {
		return $statement->fetch();
	}
}
