<?php

function user_exists(PDO $dbh, $email) {
	$statement = $dbh->prepare("SELECT COUNT(user_id) FROM users WHERE email = ?");
	if ($statement->execute([$email])) {
		return $statement->fetchAll()[0][0] === 1;
	} else {
		return false;
	}
}

function register(PDO $dbh, $name, $nickname, $email, $hashed_password, $icon) {
	$statement = $dbh->prepare("INSERT INTO users (name, nickname, email, password, icon) VALUES (?, ?, ?, ?, ?)");
	return $statement->execute([$name, $nickname, $email, $hashed_password, $icon]);
}

function edit_profile(PDO $dbh, $user_id, $name, $nickname, $email, $hashed_password, $icon) {
	if ($hashed_password) {
		$statement = $dbh->prepare("UPDATE users SET name = ?, nickname = ?, email = ?, password = ?, icon = ? WHERE user_id = ?;");
		return $statement->execute([$name, $nickname, $email, $hashed_password, $icon, $user_id]);

	} else {
		$statement = $dbh->prepare("UPDATE users SET name = ?, nickname = ?, email = ?, icon = ? WHERE user_id = ?;");
		return $statement->execute([$name, $nickname, $email, $icon, $user_id]);
	}
}

function get_users(PDO $dbh) {
	$statement = $dbh->prepare("SELECT * FROM users;");
	return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_user_by_email(PDO $dbh, $email) {
	$statement = $dbh->prepare("SELECT * FROM users WHERE email = ?;");
	if ($statement->execute([$email])) {
		return $statement->fetch(PDO::FETCH_ASSOC);
	}
	return false;
}

function get_user_by_id(PDO $dbh, $id) {
	$statement = $dbh->prepare("SELECT * FROM users WHERE user_id = ?;");
	if ($statement->execute([$id])) {
		return $statement->fetch(PDO::FETCH_ASSOC);
	}
	return false;
}
