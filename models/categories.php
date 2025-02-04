<?php
function get_categories(PDO $dbh) {
    $statement = $dbh->prepare("
        SELECT * FROM categories;
    ");
    $statement->execute();

	$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}