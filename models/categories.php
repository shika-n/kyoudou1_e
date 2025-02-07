<?php
function get_categories(PDO $dbh) {
    $statement = $dbh->prepare("
        SELECT * FROM categories;
    ");
    $statement->execute();

	$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function set_post_categories(PDO $dbh, $post_id, $category_ids) {
	$placeholder = "";
	$exec_arr = [];
	for ($i = 0; $i < count($category_ids); ++$i) {
		if ($i > 0) {
			$placeholder .= ", ";
		}
		$placeholder .= "(?, ?)";
		$exec_arr[] = $post_id;
		$exec_arr[] = $category_ids[$i];
	}
    $statement = $dbh->prepare("
        INSERT INTO post_category VALUES $placeholder
    ");

    return $statement->execute($exec_arr);
}
